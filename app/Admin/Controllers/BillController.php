<?php

namespace App\Admin\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BillController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '流水记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bill());

        $grid->column('id', __('Id'));
        $grid->column('customer.nickname', __('Nickname'))->copyable();
        $grid->column('order_id', __('类型'))->display(function (){
            if ($this->order_id==0){
               return "系统操作";
            }else{
                $order_sn=Order::find($this->order_id)->order_sn;
                return $order_sn."订单消费";
            }
        });
        $grid->column('type', __('Type'))->using([
            0 => '扣款',
            1 => '充值',
        ], '未知')->dot([
            1 => 'success',
            0 => 'danger',
        ], 'warning');

        $grid->column('money', __('Money'));
        $grid->column('description', __('Description'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        //禁用创建按钮
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->filter(function ($filter) {
            $customers=Customer::all()->pluck('nickname','id');

            $filter->equal('customer_id', __('Nickname'))->select($customers);
            $status_text = [
                0 => '扣款',
                1 => '充值',
            ];
            $filter->equal('type', __('Type'))->select($status_text);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Bill::findOrFail($id));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Bill());


        return $form;
    }
}
