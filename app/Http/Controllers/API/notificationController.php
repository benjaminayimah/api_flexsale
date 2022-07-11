<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notification;
use App\Store;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class notificationController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $notificationsArr = array();
        if($user->role == 1) {
            $stores = User::find($user->id)->getStores;
        }elseif ($user->role == 2) {
            $stores = User::find($user->admin_id)->getStores;
        }
        if(count($stores) > 0) {
            $store = $user->current;
            $notification = new Notification();
            //low stock
        $low_stock = Store::find($store)->getProducts()
        ->where('stock', '<', 10)
        ->get();
        if(count($low_stock) > 0) {
            $noti_key = 'low-stocks';
            $unit = false;
            $notification->insertNotifications($low_stock, $store, $noti_key, $unit);
        }
        //expired
        $today = Carbon::today();
        $expired_prods = Store::find($store)->getProductUnits()
        ->where('expiry_date', '<', $today)
        ->get();
        if (count($expired_prods) > 0) {
            $noti_key = 'expired';
            $unit = true;
            $notification->insertNotifications($expired_prods, $store, $noti_key, $unit);
        }
        //expiring soon
        $expiry_limit = 30;
        $today_plus_30 = \Carbon\Carbon::parse()->addDays($expiry_limit);
        $expiring_soon = Store::find($store)->getProductUnits()
        ->where([
            ['expiry_date', '>', $today],
            ['expiry_date', '<', $today_plus_30]
            ])
        ->get();
        if (count($expiring_soon) > 0) {
            $noti_key = 'expiring-soon';
            $unit = true;
            $notification->insertNotifications($expiring_soon, $store, $noti_key, $unit);
        }
        $notificationsArr = DB::table('notifications')
            ->join('products', 'notifications.product_id', '=', 'products.id')
            ->where(['notifications.store_id' => $store, 'products.deleted' => false])
            ->select('notifications.*', 'products.name', 'products.image', 'products.stock')
            ->get();
        }
        return response()->json([
            'notifications' => $notificationsArr
        ], 200);

        
    }

    public function update(Request $request, $id)
    {
        //

    }

    public function destroy($id)
    {
        //
    }
}
