<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use Tymon\JWTAuth\Facades\JWTAuth;

class productDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try{
            $product = Store::find($user->current)->getProducts()
            ->where('id', $request['id'])
            ->first();
            $units = Product::find($request['id'])->getUnits;
        }catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        return response()->json([
            'product' => $product,
            'units' => $units
        ], 200);
    }


    public function update(Request $request, $id)
    {
       
       
    }


    public function destroy($id)
    {
        //
    }
}
