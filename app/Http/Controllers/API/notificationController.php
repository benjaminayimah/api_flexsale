<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notification;
use App\Store;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class notificationController extends Controller
{
    public function index()
    {
        //
    }
    public function computeNotification($prod_units, $store, $noti_key, $unit) {
        $newArray = array();
        foreach ($prod_units as $key => $value) {
            $batch_no = null;
            $unit_id = null;
            $expiry_date = null;
            $prod_id = $value->id;
            if ($unit == true) {
                $prod_id = $value->product_id;
                $unit_id = $value->id;
                $expiry_date = $value->expiry_date;
                $batch_no = $value->batch_no;
            }
            $product = Store::find($store)->getProducts()
            ->where('id', $prod_id)->first();
            $notiObj = new Notification();
            $notiObj->product_id = $prod_id;
            $notiObj->batch_no = $batch_no;
            $notiObj->unit_id = $unit_id;
            $notiObj->store_id = $store;
            $notiObj->key = $noti_key;
            $notiObj->read = 0;
            $notiObj->expiry_date = $expiry_date;
            $notiObj->name = $product->name;
            $notiObj->stock = $product->stock;
            $notiObj->image = $product->image;
            $notiObj->updated_at = $value->updated_at;
            $notiObj->created_at = $value->updated_at;
            array_push($newArray, $notiObj);
        }
        return $newArray;

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
            $store_id = $user->current;
            //low stock
            $low_stock = Store::find($store_id)->getProducts()
            ->where([['stock', '<', 10], ['deleted', '=>', false]])
            ->get();
            if(isset($low_stock)) {
                $noti_key = 'low-stocks';
                $unit = false;
                array_push($notificationsArr, $this->computeNotification($low_stock, $store_id, $noti_key, $unit));
            }
            //expired
            $today = Carbon::today();
            $expired_prods = DB::table('units')
                ->join('products', 'units.product_id', '=', 'products.id')
                ->where('units.store_id', $store_id)
                ->where('units.expires', true)
                ->where('units.expiry_date', '<', $today)
                ->where('products.deleted', false)
                ->select('units.*')
                ->get();
            if (count($expired_prods) > 0) {
                $noti_key = 'expired';
                $unit = true;
                array_push($notificationsArr, $this->computeNotification($expired_prods, $store_id, $noti_key, $unit));
            }
            //expiring soon
            $expiry_limit = 30;
            $today_plus_30 = \Carbon\Carbon::parse()->addDays($expiry_limit);
            $expiring_soon = DB::table('units')
                ->join('products', 'units.product_id', '=', 'products.id')
                ->where('units.store_id', $store_id)
                ->where('units.expiry_date', '>', $today)
                ->where('units.expiry_date', '<', $today_plus_30)
                ->where('products.deleted', false)
                ->select('units.*')
                ->get();

            if (count($expiring_soon) > 0) {
                $noti_key = 'expiring-soon';
                $unit = true;
                array_push($notificationsArr, $this->computeNotification($expiring_soon, $store_id, $noti_key, $unit));
            }
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
