<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Posts extends MyModel
{
    use HasFactory,HasTranslations;

    public $translatable = ['title', 'description', 'link_name', 'subtitle'];

    protected $guarded = [];

    protected $table = 'posts';

    protected $imagePath = '/uploads/companies/';

    public function getImageLinkAttribute()
    {
        return $this->getImage($this->image, config('global.company_details_image'));
    }
}
