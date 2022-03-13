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
    public function getDiscounts() {
        return $this->hasMany(Discount::class);
    }
    public function getSales() {
        return $this->hasMany(Sale::class);
    }
}
