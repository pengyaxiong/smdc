<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\OrderConfirm;
use App\Admin\Actions\Post\OrderOver;
use App\Admin\Actions\Post\OrderPrint;
use App\Models\Desk;
use App\Models\Food;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';
    protected $status = [];

    public function __construct()
    {
        $this->status = [1 => '已下单', 2 => '已确认', 3 => '已加菜', 4 => '已完成'];
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());

        $grid->column('id', __('Id'));
        $grid->column('order_sn', __('Order sn'));
        $grid->column('desk.name', __('餐桌编号'));
        $grid->column('status', __('Status'))->using($this->status)->label([
            1 => 'default',
            2 => 'info',
            3 => 'warning',
            4 => 'success',
        ]);
        $grid->column('total_price', __('Total price'));
        $grid->column('products', __('菜品详情'))->display(function () {
            return '点击查看';
        })->expand(function ($model) {
            $foods = $model->products;

//            $foods = $model->products->map(function ($model) {
//                $data = [
//                    'id' => $model['id'],
//                    'name' => $model['name'],
//                    'price' => $model['price'],
//                    'num' => $model['num'],
//                    'total_price' =>$model['total_price'],
//                ];
//                return $data;
//            });

            return new Table(['ID', '数量','菜名', '单价', '小计'], $foods);
        });

        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->filter(function ($filter) {

            $filter->equal('status', __('Status'))->select($this->status);

            $filter->between('created_at', __('Created at'))->date();

        });

        //禁用创建按钮
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableView();
            //  $actions->disableEdit();
             $actions->disableDelete();

            $actions->add(new OrderConfirm());
            $actions->add(new OrderOver());
            $actions->add(new OrderPrint());
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_sn', __('Order sn'));
        $show->field('desk_id', __('Desk id'));
        $show->field('status', __('Status'));
        $show->field('total_price', __('Total price'));
        $show->field('products', __('Products'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());

        $form->text('order_sn', __('Order sn'));

        $desks=Desk::all()->toArray();
        $select_array = array_column($desks, 'name', 'id');
        $form->select('desk_id', __('餐桌编号'))->options($select_array);

        $form->radio('status', __('Status'))->options($this->status)->default(1);

        $form->table('products', __('菜品详情'), function ($table) {
            $staffs = Food::where('sell_out',0)->get()->toArray();
            $select_staff = array_column($staffs, 'name', 'id');
            $table->select('id', '菜名')->options($select_staff);

            $table->number('num', '数量')->default(1);
        });

        $form->decimal('total_price', __('Total price'));

        $form->textarea('remark', __('Remark'));

        return $form;
    }
}
