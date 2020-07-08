<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Food;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FoodController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '菜单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Food());

        $grid->model()->orderby('sort_order');

        $grid->column('id', __('Id'));
        $grid->column('category.name', __('菜品栏目'));
        $grid->column('image', __('Image'))->image();
        $grid->column('name', __('Name'));
        $grid->column('type', __('Type'))->pluck('name')->label();
        $grid->column('description', __('Description'));
        $grid->column('price', __('Price'));
        $states = [
            'on' => ['value' => 1, 'text' => '是', 'color' => 'danger'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'success'],
        ];
        $grid->column('sell_out', __('Sell out'))->switch($states);
        $hot_states = [
            'on' => ['value' => 1, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
        ];
        $grid->column('is_hot', __('Is hot'))->switch($hot_states);
        $grid->column('sort_order', __('Sort order'))->sortable()->editable()->help('按数字大小正序排序');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->filter(function ($filter) {
            $filter->like('name', __('Name'));

            $categories = Category::all()->toArray();
            $select_array = array_column($categories, 'name', 'id');

            $filter->equal('category_id', __('Category id'))->select($select_array);

            $status_text = [
                1 => '是',
                0 => '否'
            ];
            $filter->equal('is_check', __('Sell out'))->select($status_text);
            $filter->equal('is_hot', __('Is hot'))->select($status_text);

            $filter->between('created_at', __('Created at'))->date();

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
        $show = new Show(Food::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('image', __('Image'));
        $show->field('name', __('Name'));
        $show->field('type', __('Type'));
        $show->field('description', __('Description'));
        $show->field('price', __('Price'));
        $show->field('sell_out', __('Sell out'));
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
        $form = new Form(new Food());

        $categories = Category::all()->toArray();
        $select_array = array_column($categories, 'name', 'id');
        $form->select('category_id', __('Category id'))->options($select_array);

        $form->image('image', __('Image'))->rules('required|image');
        $form->text('name', __('Name'))->rules('required');

        $form->table('type', __('Type'), function ($table) {

            $table->text('name', '标签');
        });

        $form->textarea('description', __('Description'))->rules('required');
        $form->decimal('price', __('Price'))->default(99.00);

        $hot_states = [
            'on' => ['value' => 1, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
        ];
        $form->switch('is_hot', __('Is hot'))->states($hot_states)->default(0);

        $states = [
            'on' => ['value' => 1, 'text' => '是', 'color' => 'danger'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'success'],
        ];
        $form->switch('sell_out', __('Sell out'))->states($states)->default(0);

        $form->number('sort_order', __('Sort order'))->default(99);

        return $form;
    }
}
