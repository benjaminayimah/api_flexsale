<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function getSaleItems() {
        return $this->hasMany(SaleItem::class);
    }
}
