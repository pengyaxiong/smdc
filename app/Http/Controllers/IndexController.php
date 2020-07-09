<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Config;
use App\Models\Desk;
use App\Models\Food;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct()
    {
        $config = Config::first();
        view()->share([
            'config'=>$config
        ]);
    }


    public function index(Request $request)
    {
        $desk_id=$request->desk_id;
        $desk=Desk::find($desk_id);
        if (empty($desk)){
            return '参数错误，请重新扫描二维码！';
        }
        $hot = Food::where('is_hot', 1)->orderBy('sort_order')->get();
        $categories = Category::with(['foods' => function ($query) {
            $query->orderBy('sort_order')->orderBy('id');
        }])->orderBy('sort_order')->get();

        return  view('home.index', compact('categories', 'hot'));
    }



}
