<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Config;
use App\Models\Desk;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    public function __construct()
    {
        $config = Config::first();
        view()->share([
            'config' => $config
        ]);
    }


    public function index(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }
        $hot = Food::where('is_hot', 1)->orderBy('sort_order')->get();
        $categories = Category::with(['foods' => function ($query) {
            $query->orderBy('sort_order')->orderBy('id');
        }])->orderBy('sort_order')->get();

        return view('home.index', compact('categories', 'hot'));
    }


    public function add(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }

        //type=1加菜 =0下单
        Cart::create([
            'desk_id' => $request->desk_id,
            'food_id' => $request->food_id,
            'num' => $request->num,
            'type' => $request->type,
        ]);
    }

    public function delete(Request $request)
    {
        $desk_id = $request->desk_id;
        $food_id = $request->food_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }
       Cart::where('desk_id',$desk_id)->where('food_id',$food_id)->delete();
    }

    public function order(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }

        $products = Cart::with('food')->where('desk_id',$desk_id)->get();

        return $products;
    }


    public function do_order(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }

        $products = Cart::where('desk_id',$desk_id)->get()->toarray();

        foreach ($products as $key => $product) {

            $food = Food::find($product['food_id']);
            $products[$key]['id'] = $food->id;
            $products[$key]['name'] = $food->name;
            $products[$key]['price'] = $food->price;
            $products[$key]['total_price'] = $food->price * $product['num'];

            unset($products[$key]['desk_id']);
            unset($products[$key]['food_id']);
        }

        $order_sn = date('YmdHms', time()) . $desk_id;
        $total_price = array_sum(array_pluck($products, 'total_price'));

        try {
            Order::create([
                'order_sn' => $order_sn,
                'desk_id' => $desk_id,
                'total_price' => $total_price,
                'products' => $products,
                'remark' => $request->remark,
            ]);

            $desk->is_able=0;
            $desk->save();

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            return ['status' => 0, 'msg' => $exception->getMessage()];
        }

        return ['status' => 1, 'msg' => '提交成功'];

    }

    public function do_add(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        $order = Order::where('desk_id', $desk_id)->where('status', '<', 4)->first();
        if (empty($desk) || empty($order)) {
            return '参数错误，请重新扫描二维码！';
        }


        $products = Cart::where('desk_id',$desk_id)->get()->toarray();

        foreach ($products as $key => $product) {

            $food = Food::find($product['food_id']);
            $products[$key]['id'] = $food->id;
            $products[$key]['name'] = $food->name;
            $products[$key]['price'] = $food->price;
            $products[$key]['total_price'] = $food->price * $product['num'];

            unset($products[$key]['desk_id']);
            unset($products[$key]['food_id']);
        }
        $total_price = array_sum(array_pluck($products, 'total_price'));
        try {
            $order->total_price = $total_price;
            $order->products = $products;
            $order->remark = $request->remark;
            $order->status = 3;
            $order->save();

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            return ['status' => 0, 'msg' => $exception->getMessage()];
        }

        return ['status' => 1, 'msg' => '提交成功'];


    }


    public function order_info(Request $request)
    {
        $desk_id = $request->desk_id;
        $desk = Desk::find($desk_id);
        if (empty($desk)) {
            return '参数错误，请重新扫描二维码！';
        }

        $order = Order::with('desk')->where('desk_id', $desk_id)->where('status', '<', 4)->first();

        return view('home.order_info', compact('order'));
    }
}
