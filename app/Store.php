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
    public function getSalesItem() {
        return $this->hasMany(SaleItem::class);
    }
    public function getSuppliers() {
        return $this->hasMany(Supplier::class);
    }
    public function getNotifications() {
        return $this->hasMany(Notification::class);
    }
    public function getProductUnits() {
        return $this->hasMany(Unit::class);
    }
}
