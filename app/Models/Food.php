<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_food';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getTypeAttribute($type)
    {
        return array_values(json_decode($type, true) ?: []);
    }

    public function setTypeAttribute($type)
    {
        $this->attributes['type'] = json_encode(array_values($type));
    }
}
