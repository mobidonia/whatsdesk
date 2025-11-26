<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

if (config('settings.enable_miltilanguage_menus')) {
    class TranslateAwareModel extends Model
    {
        use HasFactory;
        use HasTranslations;
    }
} else {
    class TranslateAwareModel extends Model
    {
        use HasFactory;
    }
}
