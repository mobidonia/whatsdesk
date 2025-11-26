<?php

namespace Modules\Embedwhatsapp\Models;

use App\Models\MyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Whatsappwidget extends MyModel
{
    use HasFactory;

    protected $table = 'whatsappwidgets';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected $imagePath = '/uploads/companies/';

    public function getImageLinkAttribute()
    {
        return $this->getImage($this->logo, '');
    }
}
