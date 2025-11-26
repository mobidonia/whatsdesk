<?php

namespace Modules\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CompanyScope;

class Group extends Model
{
    use SoftDeletes;
    
    protected $table = 'groups';
    public $guarded = [];

    public function contacts()
    {
        return $this->belongsToMany(
                Contact::class,
                'groups_contacts',
                'group_id',
                'contact_id'
            );
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
