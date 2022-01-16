<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function getProducts() {
        return $this->hasMany(Product::class);
    }
    public function getCategories() {
        return $this->hasMany(Category::class);
    }
}
