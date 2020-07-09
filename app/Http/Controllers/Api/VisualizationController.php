<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Desk;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderFood;
use DB, Cache;
use Illuminate\Http\Request;

class VisualizationController extends Controller
{
    //本周起止时间unix时间戳
    private $week_start;
    private $week_end;

    //本月起止时间unix时间戳
    private $month_start;
    private $month_end;

    function __construct()
    {
        $this->week_start = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
        $this->week_end = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));

        $this->month_start = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $this->month_end = mktime(23, 59, 59, date("m"), date("t"), date("Y"));
    }

    /**
     * 本周订单数
     * @return array
     */
//    function sales_count()
//    {
//        return \Cache::remember('xApi_visualization_sales_count', 60, function () {
//            $count = [];
//            for ($i = 0; $i < 7; $i++) {
//                $start = date('Y-m-d H:i:s', strtotime("+" . $i . " day", $this->week_start));
//                $end = date('Y-m-d H:i:s', strtotime("+" . ($i + 1) . " day", $this->week_start));
//
//                //待支付
//                $count['create'][] = Order::whereBetween('created_at', [$start, $end])->where('status', 1)->count();
//
//                $count['pay'][] = Order::whereBetween('pay_time', [$start, $end])->where('status', 2)->count();
//
//                $count['shipping'][] = Order::whereBetween('shipping_time', [$start, $end])->where('status', 3)->count();
//
//                $count['finish'][] = Order::whereBetween('finish_time', [$start, $end])->where('status', 5)->count();
//                //申请退货
//                $count['return_back'][] = Order::whereBetween('refund_time', [$start, $end])->where('status', 6)->count();
//                //退货中
//                $count['refund_index'][] = Order::whereBetween('refund_add_time', [$start, $end])->where('status', 9)->count();
//                //退货完成
//                $count['refund_suc'][] = Order::whereBetween('refund_suc_time', [$start, $end])->where('status', 10)->count();
//            }
//
//            $data = [
//                'week_start' => date("Y年m月d日", $this->week_start),
//                'week_end' => date("Y年m月d日", $this->week_end),
//                'count' => $count,
//            ];
//            return $data;
//        });
//
//    }

    /**
     * 本周销售额
     * @return array
     */
    function sales_amount()
    {
        // return \Cache::remember('xApi_visualization_sales_amount', 60, function () {
        $amount = [];
        $series = [];
        for ($i = 0; $i < 7; $i++) {
            $start = date('Y-m-d H:i:s', strtotime("+" . $i . " day", $this->week_start));
            $end = date('Y-m-d H:i:s', strtotime("+" . ($i + 1) . " day", $this->week_start));

            $desks = Desk::all()->toArray();
            foreach ($desks as $key => $desk) {
                $amount[$desk['name']][] = Order::whereBetween('created_at', [$start, $end])->where('desk_id', $desk['id'])->where('status', 4)->sum('total_price');

                $series[$key]['name'] = $desk['name'];
                $series[$key]['data'][] = Order::whereBetween('created_at', [$start, $end])->where('desk_id', $desk['id'])->where('status', 4)->sum('total_price');
                $series[$key]['type'] = 'line';
                //  $series[$key]['markLine']=['data'=>['type'=>'average','name'=>'平均值']];
            }
        }
        $data = [
            'week_start' => date("Y年m月d日", $this->week_start),
            'week_end' => date("Y年m月d日", $this->week_end),
            'amount' => $amount,
            'series_data' => $series,
        ];
        return $data;
        //  });
    }

    /**
     * 本月热门销量
     * @return mixed
     */
    function order_count()
    {
      //  return \Cache::remember('xApi_visualization_top', 60, function () {
//            DB::enableQueryLog();
            $start = date("Y-m-d H:i:s", $this->month_start);
            $end = date("Y-m-d H:i:s", $this->month_end);

            //本月订单的id
            $order = Order::whereBetween('created_at', [$start, $end])->pluck('id');

            //对应热门商品,前10名. 语句较复杂,请自己return sql出来看
            $orders = OrderFood::with('food')->select('food_id', \DB::raw('sum(num) as sum_num'))
                ->whereIn('order_id', $order)
                ->groupBy('food_id')
                ->orderBy(\DB::raw('sum(num)'), 'desc')
                ->take(10)
                ->get();


            // return DB::getQueryLog();

            $data = [
                'month_start' => date("Y年m月d日", $this->month_start),
                'month_end' => date("Y年m月d日", $this->month_end),
                'orders' => $orders,
            ];
            return $data;
    //    });

    }


    public function order_status()
    {
        $today = date("Y-m-d");
        $start = $today . ' 00:00:00';
        $end = $today . ' 23:59:59';

        $status_1 = Order::where('status', '<', 4)->count();
        $status_2 = Desk::where('is_able', 1)->count();

        $status_3 = Order::whereBetween('created_at', [$start, $end])->where('status', 4)->sum('total_price');

        $status_4 = Order::where('status', 4)->sum('total_price');

        $data = [$status_1, $status_2, $status_3, $status_4];

        return $data;

    }

    public function sales_count()
    {

            $products = Food::all();

            foreach ($products as $key => $product) {
                $products[$key]['num'] = OrderFood::where(array('food_id' => $product['id']))->sum('num');
            }

        return $products;
    }
}
