<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
    protected function getImage($imageValue, $default, $version = '_large.jpg')
    {
        if ($imageValue == '' || $imageValue == null) {
            //No image
            return $default;
        } else {
            if (strpos($imageValue, 'http') !== false) {
                //Have http
                if (
                    strpos($imageValue, '.jpg') !== false ||
                    strpos($imageValue, '.svg') !== false ||
                    strpos($imageValue, '.jpeg') !== false ||
                    strpos($imageValue, '.png') !== false ||
                    strpos($imageValue, 'api.tinify.com') !== false ||
                    strpos($imageValue, 'amazonaws.com') !== false ||
                    strpos($imageValue, 'googleapis.com') !== false
                ) {
                    //Has extension
                    return $imageValue;
                } else {
                    //No extension
                    return $imageValue.$version;
                }
            } else {
                if (
                    strpos($imageValue, 'default/') !== false
                ) {
                    return '/uploads/'.$imageValue;
                }

                //Local image
                return (config('settings.image_store_location').$this->imagePath.$imageValue).$version;
            }
        }
    }
}
