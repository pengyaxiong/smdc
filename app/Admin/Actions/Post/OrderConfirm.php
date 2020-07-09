<?php

namespace App\Admin\Actions\Post;

use App\Models\Desk;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class OrderConfirm extends RowAction
{
    public $name = '确认订单';

    public function handle(Model $model)
    {
        // $model ...
        if (!in_array($model->status,[1,3])){
            return $this->response()->error('订单状态错误.')->refresh();
        }

        Desk::where('id',$model->desk_id)->update(['is_able'=>0]);

        $model->status=2;
        $model->save();

        return $this->response()->success('确认订单成功.')->refresh();
    }

}