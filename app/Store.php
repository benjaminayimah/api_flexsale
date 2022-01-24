<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function getProducts() {
        return $this->hasMany(Product::class);
    }
    public function getTags() {
        return $this->hasMany(Tag::class);
    }
    public function getFilters() {
        return $this->hasMany(TagItem::class);
    }
}
