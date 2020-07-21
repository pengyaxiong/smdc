<?php

namespace App\Admin\Controllers;

use App\Models\Desk;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DeskController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '餐桌管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Desk());

        $grid->model()->orderby('sort_order');

        $grid->column('id', __('Id'));
        $grid->column('image', __('二维码'))->display(function (){
            return '<img src="https://api.qrserver.com/v1/create-qr-code/?data='.$_SERVER['HTTP_HOST'].'/wechat/index?desk_id='.$this->id.'&amp;size=100x100" alt="" title="" />';
        });
        $grid->column('name', __('Name'));

        $grid->column('is_able', __('Is able'))->using([
            1 => '无客',
            0 => '有客',
        ])->label([
            1 => 'success',
            0 => 'danger',
        ]);

        $grid->column('sort_order', __('Sort order'))->sortable()->editable()->help('按数字大小正序排序');
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->filter(function ($filter) {

            $filter->equal('is_able', __('Is able'))->select([
                1 => '无客',
                0 => '有客',
            ]);

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
        $show = new Show(Desk::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('is_able', __('Is able'));
        $show->field('sort_order', __('Sort order'));
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
        $form = new Form(new Desk());

        $form->text('name', __('Name'))->rules('required');

        $states = [
            'on' => ['value' => 1, 'text' => '无客', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '有客', 'color' => 'danger'],
        ];
     //   $form->switch('is_able', __('Is able'))->states($states)->default(1);

        $form->number('sort_order', __('Sort order'))->default(99);

        return $form;
    }
}
