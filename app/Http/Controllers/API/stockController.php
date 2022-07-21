<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Product;
use App\Store;
use App\Unit;
use Carbon\Carbon;



class stockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchBatches(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $units = Product::find($request['id'])->getUnits;
        $product = Store::find($store_id)->getProducts()
            ->where('id', $request['id'])
            ->first();
        return response()->json([
            'units' => $units,
            'product' => $product
        ], 200);
    }
    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $this->validate($request, [
            'batch_no' => 'required',
        ]);
        $batch = $request['batch_no'];
        if(!$this->checkDuplicate($batch, $store_id)) {
            return response()->json([
                'exists' => 'This Batch number already exists in your store.',
            ], 200);
        }
        $stock = 0;
        if($request['stock'] != '') {
            $stock = $request['stock'];
        }
        $today = Carbon::today();
        try {
            $unit = new Unit();
            $unit->unit_stock = $stock;
            $unit->store_id = $store_id;
            $unit->product_id = $request['id'];
            $unit->batch_no = $batch;
            $unit->expiry_date = $request['expiry'];
            if($today->gt($request['expiry']) ) {
                $unit->active = 0;
            }
            $unit->save();
            $product = $this->updateProductStk($request['id']);

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'body' => 'Error updating stock.'
            ], 500);
        }
        return response()->json([
            'status' => 'Stock is updated',
            'unit' => $unit,
            'stock' => $product
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'batch_no' => 'required',
        ]);
        $store_id = $user->current;
        $unit = Unit::findOrFail($request['unitID']);
        $batch = $request['batch_no'];
        if($unit->batch_no != $batch) {
            if(!$this->checkDuplicate($batch, $store_id)) {
                return response()->json([
                    'exists' => 'This Batch number already exists in your store.',
                ], 200);
            }
        }
        try {
            $stock = 0;
            if($request['stock'] != '') {
                $stock = $request['stock'];
            }
            $today = Carbon::today();
            $unit->unit_stock = $unit->unit_stock + $stock;
            $unit->batch_no = $batch;
            $unit->expiry_date = $request['expiry'];
            if($today->gt($request['expiry']) ) {
                $unit->active = 0;
            }else{
                $unit->active = 1;
            }
            $unit->update();
            $product = $this->updateProductStk($id);
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'body' => 'Error updating stock.'
            ], 500);
        }
        return response()->json([
            'status' => 'Stock is updated',
            'unit' => $unit,
            'stock' => $product
        ], 200);
    }
    public function checkDuplicate($batch, $store) {
        $check = Store::findOrFail($store)->getProductUnits()
        ->where('batch_no', $batch)
        ->count();
        if($check > 0)
        return false;
        else
        return true;
    }
    public function updateProductStk($id) {
        $allUnits = Product::find($id)->getUnits;
        $sum = 0;
        foreach($allUnits as $key=>$value){
            if(isset($value->unit_stock))
            $sum += $value->unit_stock;
        }
        $product = Product::findOrFail($id);
        $product->stock = $sum;
        $product->update();
        return $product;
    }
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $unit = Unit::findOrFail($id);
        $unit->delete();
        $product = $this->updateProductStk($unit->product_id);
        return response()->json([
            'status' => 'Stock is deleted',
            'id' => $id,
            'stock' => $product
        ], 200);

    }
}
