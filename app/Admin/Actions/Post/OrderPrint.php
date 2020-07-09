<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class OrderPrint extends RowAction
{
    public $name = '打印订单';

    public function handle(Model $model)
    {
        // $model ...
        if ($model->status!=4){
            return $this->response()->error('订单状态错误.')->refresh();
        }

        return $this->response()->success('打印订单成功.')->refresh();
    }

}