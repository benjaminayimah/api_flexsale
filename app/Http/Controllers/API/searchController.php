<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class searchController extends Controller
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
        $query = $request['query'];
            $products = Store::find($user->current)->getProducts()
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->get();
        return response()->json([
            'results' => $products
        ], 200);
    }
}
