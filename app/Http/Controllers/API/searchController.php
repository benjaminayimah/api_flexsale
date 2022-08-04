<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use App\User;
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
        $products = array();
        if($user->role == 1) {
            $stores = User::find($user->id)->getStores;
        }elseif ($user->role == 2) {
            $stores = User::find($user->admin_id)->getStores;
        }
        try {
            if(count($stores) > 0) {
                $query = $request['query'];
                $products = Store::find($user->current)->getProducts()
                ->where('name', 'like', '%'.$query.'%')
                ->get();
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        return response()->json([
            'results' => $products
            // 'input' => $request['query']
        ], 200);
        
    }
}
