<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Image;
use App\Sale;
use App\SaleItem;
use App\Store;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
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
        $user = JWTAuth::parseToken()->toUser();
        $filters = [];
        $stores = [];
        $tags = [];
        $products = [];
        $discounts = [];
        $sales = [];
        $sales_items = [];

        
        try {
            if($user->role == 1) {
                $stores = User::find($user->id)->getStores;
            }elseif ($user->role == 2) {
                $stores = User::find($user->admin_id)->getStores;
            }
            if($user->current != null) {
                $tags = Store::find($user->current)->getTags;
                $products = Store::find($user->current)->getProducts;
                $discounts = Store::find($user->current)->getDiscounts;
                $sales = DB::table('sales')->where([ 
                    ['store_id', '=', $user->current],
                    ['created_at', '>=', Carbon::today()]
                    ])->get();
                $sales_items = DB::table('sale_items')->where([
                    ['store_id', '=', $user->current],
                    ['created_at', '>=', Carbon::today()]
                    ])->get();
                if(count($products) != 0 && count($tags) != 0) {
                    $filters = DB::table('tag_items')
                    ->join('products', 'tag_items.product_id', '=', 'products.id')
                    ->where('tag_items.store_id', '=', $user->current)
                    ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.name', 'products.image')
                    ->get();
                }
            }
            
            if($user->role == '0' || $user->role == '1' || $user->role == '2'){
                return response()->json([
                    'status' => 1,
                    'user' => $user,
                    'stores' => $stores,
                    'tags' => $tags,
                    'filters' => $filters,
                    'products' => $products,
                    'discounts' => $discounts,
                    'sales' => $sales,
                    'sales_items' => $sales_items,
                    'today' => Carbon::today()
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }

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
