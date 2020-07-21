<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    //黑名单为空
    protected $guarded = [];
    protected $table = 'mini_bill';


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
