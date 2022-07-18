<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class statsController extends Controller
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
        $store_id = $user->current;
        $sales = Store::find($store_id)->getSalesItem()
        ->where('product_id', $request['id'])
        ->get();
        return response()->json([
            'stats' => $sales
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
