<?php

namespace App\Admin\Actions\Post;

use App\Models\Cart;
use App\Models\Desk;
use App\Models\OrderFood;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class OrderOver extends RowAction
{
    public $name = '结束订单';

    public function handle(Model $model)
    {
        // $model ...
        if ($model->status!=2){
            return $this->response()->error('订单状态错误.')->refresh();
        }
        Desk::where('id',$model->desk_id)->update(['is_able'=>1]);
        $model->status=4;
        $model->save();

        $foods = $model->products;
        foreach ($foods as $key=>$food){
            OrderFood::create([
                'order_id' => $model->id,
                'desk_id' => $model->desk_id,
                'food_id' => $food['id'],
                'num' => $food['num'],
                'price' => $food['price'],
                'total_price' => $food['price'] * $food['num'],
                'type' => $food['type'],
            ]);
        }

        Cart::where('desk_id',$model->desk_id)->delete();
        return $this->response()->success('结束订单成功.')->refresh();
    }

}