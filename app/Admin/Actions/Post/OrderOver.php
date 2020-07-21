<?php

namespace App\Admin\Actions\Post;

use App\Models\Cart;
use App\Models\Desk;
use App\Models\OrderFood;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderOver extends RowAction
{
    public $name = '结束订单';

    public function handle(Model $model,Request $request)
    {
        // $model ...
        if ($model->status!=2){
            return $this->response()->error('订单状态错误.')->refresh();
        }
        Desk::where('id',$model->desk_id)->update(['is_able'=>1]);
        $model->status=4;
        $model->type=$request->get('type');
        $model->save();

        //用户扣款&记录流水


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



    public function form(Model $model)
    {
        $this->text('money','用户余额')->default($model->customer->money)->disable();
        $this->text('order_money','订单金额')->default($model->total_price)->disable();
        $type = [
            0 => '余额支付',
            1 => '线下支付',
        ];
        $this->radio('type', '支付类型')->options($type);

    }
}