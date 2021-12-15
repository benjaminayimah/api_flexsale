<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /*public function images() {
        return $this->hasMany(Image::class);
    }*/
    public function image() {
        return $this->hasMany(Image::class);
    }
}
