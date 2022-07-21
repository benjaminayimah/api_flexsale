<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;


class trashController extends Controller
{
    public function index()
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $trash = Store::find($store_id)->getProducts()
        ->where('deleted', true)
        ->get();
        return response()->json([
            'trash' => $trash
        ], 200);
    }
    public function moveThisToTrash(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $id = $request['id'];
        $product = Store::find($store_id)->getProducts()
        ->where('id', $id)
        ->first();
        $product->deleted = 1;
        $product->update();
        return response()->json([
            'id' => $id,
            'status' => 'Product is moved to trash'
        ], 200);
    }

    public function update($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $product = Store::find($store_id)->getProducts()
        ->where('id', $id)
        ->first();
        $product->deleted = 0;
        $product->update();
        return response()->json([
            'product' => $product,
            'status' => 'Product is restored'
        ], 200);
    }
    public function bulkRestoreProducts(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $productsArr = array();
        foreach ($request['items'] as $key => $value) {
            $product = Store::find($store_id)->getProducts()
            ->where('id', $value)
            ->first();
            $product->deleted = 0;
            $product->update();
            array_push($productsArr, $product);
        }
        return response()->json([
            'products' => $productsArr,
            'status' => count($productsArr).' products restored'
        ], 200);
    }
    public function emptyTrash(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        $products = Store::find($store_id)->getProducts()
        ->where('deleted', true)->get();
        foreach ($products as $key => $product) {
            $tagItems = Store::find($store_id)->getFilters()
            ->where('product_id', $product->id)->get();
            if(count($tagItems) > 0) {
                foreach($tagItems as $key => $item) {
                    $item->delete();
                }
            }
            $units = Product::find($product->id)->getUnits;
            if(count($units) > 0) {
                foreach($units as $key => $unit) {
                    $unit->delete();
                }
            }
            $image = $product->image;
            if (Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$image)) {
                Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$image);
            }
            $product->delete();
        }
        return response()->json([
            'status' => count($products).' products deleted'
        ], 200);
        
    }
    public function bulkDeleteTrash(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        $productsArr = array();
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        foreach ($request['items'] as $key => $prod_id) {
            $tagItems = Store::find($store_id)->getFilters()
            ->where('product_id', $prod_id)->get();
            if(count($tagItems) > 0) {
                foreach($tagItems as $key => $item) {
                    $item->delete();
                }
            }
            $units = Product::find($prod_id)->getUnits;
            if(count($units) > 0) {
                foreach($units as $key => $unit) {
                    $unit->delete();
                }
            }
            $notification = Store::find($store_id)->getNotifications()
            ->where('product_id', $prod_id)
            ->get();
            if(count($notification) > 0) {
                foreach($notification as $noti) {
                    $noti->delete();
                }
            }
            $product = Store::find($store_id)->getProducts()
            ->where('id', $prod_id)->first();
            $image = $product->image;
            if (Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$image)) {
                Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$image);
            }
            $product->delete();
            array_push($productsArr, $product);
        }
        return response()->json([
            'products' => $productsArr,
            'status' => count($productsArr).' products deleted'
        ], 200);
        
    }

    public function destroy($id)
    {
        //
       
    }
}
