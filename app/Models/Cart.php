<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_cart';

    public $timestamps = false;

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
