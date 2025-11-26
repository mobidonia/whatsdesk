<?php

namespace Modules\Wpbox\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Template extends Model
{
    use SoftDeletes;
    
    protected $table = 'wa_templates';
    public $guarded = [];


    public function isReferenced()
    {
        // Example: Check if the template is referenced in another table (e.g., messages table)
        return DB::table('wa_campaings')->where('template_id', $this->id)->exists();
    }

    protected static function booted(){
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model){
           $company_id=session('company_id',null);
            if($company_id){
                $model->company_id=$company_id;
            }
        });
    }
}
