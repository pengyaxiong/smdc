<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_desk';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
