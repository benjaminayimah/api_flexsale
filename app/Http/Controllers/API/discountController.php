<?php

namespace App\Http\Controllers\API;

use App\Discount;
use App\Store;
use App\Http\Controllers\Controller;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class discountController extends Controller
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

    public function store(Request $request)
    {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required'
        ]);
        $percentage = 1;
        if($request['type'] == '0') {
            $percentage = 0;
        };
        $end_date = $request['endDate'];
        $start_date = $request['startDate'];
        try {
            $count = DB::table('discounts')->where(['store_id' => $store_id, 'name' => $request['name']])->count();
            if ($count < 1){
                $discount = new Discount();
                $discount->store_id = $store_id;
                $discount->name = $request['name'];
                $discount->value = $request['amount'];
                $discount->percentage = $percentage;
                $discount->start = $start_date;
                $discount->end = $end_date;
                $discount->save();
                $id = $discount->id;

                if(count($request['products']) > 0){
                    foreach($request['products'] as $product) {
                        $product = Product::findOrFail($product['id']);
                        $product->discount = $id;
                        $product->update();
                    }
                }
                $today = Carbon::today();
                if($today >= $start_date && $today <= $end_date ) {
                    $discount->active = '1';
                }elseif($today > $end_date) {
                    $discount->active = '0';
                }else{
                    $discount->active = '2';
                }
                $discount->update();
            }else{
                return response()->json([
                    'title' => 'Error!',
                    'status' => 2,
                    'message' => '"'.$request['name'].'"'.' discount name already exists.' 
                ], 200);
            }

        }catch(\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not tag, please check your connection.'
            ], 500);
        }
        $products = DB::table('products')->where(['store_id' => $store_id, 'discount' => $id])->get();


        return response()->json([
            'title' => 'Successful!',
            'status' => 1,
            'message' => '"'.$request['name'].'"'.' discount is created.',
            'discount' => $discount,
            'products' => $products,
        ], 200);

    }

    public function getThisDiscount(Request $request)
    {
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $discount_id = $request['id'];
        try{
            $products = DB::table('products')->where(['store_id' =>  $store_id, 'discount' => $discount_id])->get();
            $thisDiscount = DB::table('discounts')->where(['store_id' => $store_id, 'id' => $discount_id])->first();
        }catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }  
        return response()->json([
            'discount' => $thisDiscount,
            'products' => $products
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        // return response()->json([
        //         'product' => $request['products'],
        //         'id' => $id,
        //         'name' => $request['name']
        //     ], 200);

        $store_id = JWTAuth::parseToken()->toUser()->current;
        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required'
        ]);
        $percentage = 1;
        if($request['type'] == '0') {
            $percentage = 0;
        };
        $start_date = $request['startDate'];
        $end_date = $request['endDate'];
        $today = Carbon::today();
        
        try {
            $findOldDiscount = DB::table('discounts')->where(['id' => $id, 'store_id' => $store_id])->first();
            if($findOldDiscount->name == $request['name']) {
                $discount = Discount::findOrFail($id);
                $discount->value = $request['amount'];
                $discount->percentage = $percentage;
                $discount->start = $start_date;
                $discount->end = $end_date;
                if($today >= $start_date && $today <= $end_date ) {
                    $discount->active = '1';
                }elseif($today > $end_date) {
                    $discount->active = '0';
                }else{
                    $discount->active = '2';
                }
                $discount->update();
                
            }else{
                $count = DB::table('discounts')->where(['store_id' => $store_id, 'name' => $request['name']])->count();
                if($count < 1) {
                    $discount = Discount::findOrFail($id);
                    $discount->name = $request['name'];
                    $discount->value = $request['amount'];
                    $discount->percentage = $percentage;
                    $discount->start = $start_date;
                    $discount->end = $end_date;

                    if($today >= $start_date && $today <= $end_date ) {
                        $discount->active = '1';
                    }elseif($today > $end_date) {
                        $discount->active = '0';
                    }else{
                        $discount->active = '2';
                    }
                    $discount->update();

                }else{
                    return response()->json([
                        'title' => 'Error!',
                        'status' => 2,
                        'message' => '"'.$request['name'].'"'.' discount name already exists.' 
                    ], 200);
                }
            }
            

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not tag, please check your connection.'
            ], 500);
        }
        // $products1 = DB::table('products')->where(['store_id' => $store_id, 'discount' => $id])->get();
        $products = Store::find($store_id)->getProducts()
        ->where('discount', $id)
        ->get();
        foreach($products as $x) {
            // $product = Product::findOrFail($x->id);
            $x->discount = null;
            $x->update();
        }
        if(count($request['products']) > 0){
            foreach($request['products'] as $product) {
                $product = Product::findOrFail($product['id']);
                $product->discount = $id;
                $product->update();
            }
        }
        $discounts = Store::find($user->current)->getDiscounts;
        $newProducts = Store::find($user->current)->getProducts;

        return response()->json([
            'title' => 'Successful!',
            'status' => 1,
            'message' => 'Discount is updated.',
            'discounts' => $discounts,
            'discount' => $discount,
            'products' => $newProducts
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        
        try{
            $products = Store::find($store_id)->getProducts()
            ->where('discount', $id)
            ->get();
            if(count($products) > 0) {
                foreach($products as $x) {
                    $x->discount = null;
                    $x->update();
                }
            }
            $discount = Discount::findOrFail($id);
            $discount->delete();

        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'Discount is deleted successfully.',
            'id' => $id,
            'products' => $products
        ], 200);
    
    }
}
