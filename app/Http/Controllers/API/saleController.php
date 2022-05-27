<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\Sale;
use App\SaleItem;
use App\Store;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class saleController extends Controller
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

    public function fetchItem(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $item_id = $request['item'];
        try {
            $item = DB::table('units')
            ->join('products', 'units.product_id', '=', 'products.id')
            ->where(['units.store_id' => $store_id, 'units.batch_no' => $item_id])
            ->select('units.id', 'units.product_id', 'units.batch_no', 'units.active', 'products.name', 'products.image', 'products.selling_price', 'products.discount', 'products.prod_type')
            ->first();
            return response()->json([
                'item' => $item
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }

    }


    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $sale = new Sale();
            $sale->store_id = $user->current;
            $sale->receipt = $request['receipt'];
            $sale->total_paid = $request['total'];
            $sale->discounted = 0;
            $sale->discount_val = null;
            $sale->price_before = null;
            $sale->amount_recieved = null;
            $sale->balance = null;
            $sale->added_by = $user->name;
            $sale->save();

            foreach ($request['items'] as $key) {
                $item = new SaleItem();
                $item->sale_id = $sale->id;
                $item->store_id = $user->current;
                $item->product_id = $key['prod_id'];
                $item->product_name = $key['name'];
                $item->quantity = $key['qty'];
                $item->batch = $key['batch_no'];
                $item->total_paid = $key['qty'] * $key['unit_price'];
                if($key['discount'] != null) {
                    $item->discounted = 1;
                    $check = DB::table('discounts')->where('id', $key['discount'])->first();
                    $discountPrice = $check->value.'%';
                    if($check->percentage != 1) {
                        $discountPrice = $request['currency'].$check->value;
                    }
                    $item->discount_val = $discountPrice;
                    $item->price_before = $key['og_price'];
                }else{
                    $item->discounted = 0;
                }
                $item->save();
                $product = Product::findOrFail($key['prod_id']);
                if($product->stock > 0){
                    $product->stock = $product->stock - $key['qty'];
                    $product->update();
                }
                if($key['prod_type'] == 0) {
                    $unit = Unit::findOrFail($key['id']);
                    $unit->delete();
                }
            }
            $new_sale = DB::table('sales')->where('id', $sale->id)->first();
            $sales_items = DB::table('sale_items')->where([
                ['store_id', '=', $user->current],
                ['created_at', '>=', Carbon::today()]
                ])->get();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        return response()->json([
            'sale' => $new_sale,
            'sale_items' => $sales_items,
            'items' => $request['items'],
            'product' => $product
        ], 200);
    }
    public function filterSaleRecord(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $title = $request['title'];
            $type = $request['type'];
            $interval = $request['interval'];
            $result = array();
            $start_date = '';
            $end_date = '';
            if($type == 0) {
                //today
                $end_date = Carbon::today()->toDateTimeString();
                $result = Store::find($user->current)->getSales()
                    ->where([
                    ['created_at', '>=', $end_date]
                ])->get();
            }else{
                //to today interval
                $start_date = Carbon::today()->subDays($interval)->toDateTimeString();
                $end_date = Carbon::today()->toDateTimeString();
                //inbetween range
                if($type == 3) {
                    $start_date = \Carbon\Carbon::parse($request['start'])->toDateTimeString();;
                    $end_date = \Carbon\Carbon::parse($request['end'])->addDays(1)->toDateTimeString();;
                }
                $result = Store::find($user->current)->getSales()
                    ->whereBetween('created_at',[
                    $start_date, $end_date
                ])->get();
                $end_date = \Carbon\Carbon::parse($end_date)->subDays(1)->toDateTimeString();
                if($start_date == $end_date) {
                    $start_date = '';
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        return response()->json([
            'result' => $result,
            'title' => $title,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $type
        ], 200);
    }
    public function fetchDetailedRecordList(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $recordList = Sale::find($request['id'])->getSaleItems;
        return response()->json([
            'result' => $recordList
        ], 200);

    }
    public function receiptDetailedRecord(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $result = Store::find($user->current)->getSales()
            ->where('receipt', $request['receipt'])
            ->get();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        return response()->json([
            'result' => $result
        ], 200);
    }
  
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
