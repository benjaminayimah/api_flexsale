<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function getTagItems() {
        return $this->hasMany(TagItem::class);
    }
}
