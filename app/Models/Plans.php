<?php

namespace App\Models;

use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plans extends Model
{
    use HasConfig;
    use HasFactory;
    use SoftDeletes;

    protected $modelName = "App\Models\Plan";

    protected $table = 'plan';

    protected $guarded = [];
}
