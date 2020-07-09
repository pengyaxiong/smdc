<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFood extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_order_food';

    public $timestamps = false;

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
