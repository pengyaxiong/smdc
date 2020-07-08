<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_food_category';

    public function foods()
    {
        return $this->hasMany(Food::class,'category_id');
    }
}
