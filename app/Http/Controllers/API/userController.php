<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;


class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        // $filters = [];
        $stores = [];
        $tags = [];
        $products = [];
        $discounts = [];
        $sales = [];
        $sales_items = [];
        $suppliers = [];
        $yesterday_total = 0;
        $hasStore = false;
        $store_id = $user->current;
        try {
            if($user->role == 1) {
                $stores = User::find($user->id)->getStores;
            }elseif ($user->role == 2) {
                $stores = User::find($user->admin_id)->getStores;
            }
            if(count($stores) > 0) {
                $hasStore = true;
                $tags = Store::find($store_id)->getTags;
                $products = Store::find($store_id)->getProducts()
                ->where('deleted', false)
                ->get();
                $discounts = Store::find($store_id)->getDiscounts;
                $suppliers = Store::find($store_id)->getSuppliers;
                $sales = Store::find($store_id)->getSales()
                    ->where([
                    ['created_at', '>=', Carbon::today()->toDateTimeString()]
                    ])->get();
                $sales_items = DB::table('sale_items')->where([
                    ['store_id', '=', $store_id],
                    ['created_at', '>=', Carbon::today()->toDateTimeString()]
                    ])->get();
                // if(count($products) != 0 && count($tags) != 0) {
                //     $filters = DB::table('tag_items')
                //     ->join('products', 'tag_items.product_id', '=', 'products.id')
                //     ->where('tag_items.store_id', '=', $store_id)
                //     ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.name', 'products.image')
                //     ->get();
                // }

                // if($tags || $products) {
                //     $filters = DB::table('tag_items')
                //     ->join('products', 'tag_items.product_id', '=', 'products.id')
                //     ->where(['tag_items.store_id' => $store_id , 'products.deleted' => false ])
                //     ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.stock', 'products.name', 'products.image', 'products.cost', 'products.selling_price', 'products.discount', 'products.created_at')
                //     ->get();
                // }
                $start_date = Carbon::today()->subDays(1)->toDateTimeString();
                $end_date = Carbon::today()->toDateTimeString();
                $yesterday_sale = Store::find($store_id)->getSales()
                    ->whereBetween('created_at',[
                    $start_date, $end_date
                ])->get();
                foreach($yesterday_sale as $key=>$value){
                if(isset($value->total_paid))   
                    $yesterday_total += $value->total_paid;
                }                
            }
            
            if($user->role == '0' || $user->role == '1' || $user->role == '2'){
                return response()->json([
                    'status' => 1,
                    'user' => $user,
                    'stores' => $stores,
                    'tags' => $tags,
                    // 'filters' => $filters,
                    'products' => $products,
                    'discounts' => $discounts,
                    'sales' => $sales,
                    'sales_items' => $sales_items,
                    'suppliers' => $suppliers,
                    'today' => Carbon::today(),
                    'yesterday_sale' => $yesterday_total,
                    'hasStore' => $hasStore
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }

    }
    public function reFreshUser(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $user = User::findOrFail($user->id);
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }
        return response()->json([
            'user' => $user
        ], 200);
    }
    public function fetchAdmins(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $admins = DB::table('users')->where('admin_id', $user->id)->get();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }
        return response()->json([
            'admins' => $admins
        ], 200);
    }
    public function fetchThisAdmin(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $admin = User::findOrFail($request['id']);
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }
        return response()->json([
            'admin' => $admin
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
