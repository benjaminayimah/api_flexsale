<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use App\Unit;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = JWTAuth::parseToken()->toUser();
        // $products = Store::find($user->current)->getProducts;
        // return response()->json([
        //     'products' => $products

        // ], 200);

    }

    public function store(Request $request)
    {
        // return response()->json([
        //     'message' => $request['expiryDate']
        // ], 200);

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $expires = $request['expires'];
        $expiry_date = null;
        $this->validate($request, [
            'name' => 'required',
            'batch' => 'required'
        ]);
        if($expires) {
            $this->validate($request, [
                'expiryDate' => 'required',
            ]);
            $expiry_date = $request['expiryDate'];
        }
        $store_id = $user->current;
        $stock = 0;
        $price = 0.00;
        $cost = 0.00;
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        if($request['stock'] != '') {
            $stock = $request['stock'];
        }
        if($request['sellingPrice'] != '') {
            $price = number_format((float)$request['sellingPrice'], 2, '.', '');
        }
        if($request['cost'] != '') {
            $cost = number_format((float)$request['cost'], 2, '.', '');
        }
        $today = Carbon::today();
        try {
            $product = new Product();
            $product->store_id = $store_id;
            $product->name = $request['name'];
            $product->image = null;
            $product->prod_type = $request['prodType'];
            $product->cost = $cost;
            $product->selling_price = $price;
            $product->stock = $stock;
            $product->description = $request['description'];
            $product->supplier_id = $request['supplier'];
            $product->expires = $expires;
            $product->discount = null;
            $product->added_by = $user->name;
            $product->save();
            if ($request['prodType'] == '0') {
                $unit = new Unit();
                $unit->store_id = $store_id;
                $unit->product_id = $product->id;
                $unit->batch_no = $request['batch'];
                $unit->unit_stock = $stock;
                if ($expires) {
                    $unit->expires = true;
                    $unit->expiry_date = $expiry_date;
                    if($today->gt($expiry_date) ) {
                        $unit->active = 0;
                    }
                }
                $unit->save();
            }
            if($request['tempImage'] != null) {
                if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['tempImage'])) {
                    Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['tempImage'], $userAdminID.'/'.$store_id.'/'.$request['tempImage']);
                    $productimg = Product::find($product->id);
                    $productimg->image = $request['tempImage'];
                    $productimg->update();
                    Storage::deleteDirectory('public/'.$userAdminID.'/temp');
                };
            }
            $newProduct = DB::table('products')->where('id', $product->id)->first();
        } catch(\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'body' => 'Could not upload the product, please check your connection.'
            ], 500);
        }
        return response()->json([
            'title' => 'Success',
            'body' => 'Product is added!',
            'product' => $newProduct
        ], 200);
    }

    public function checkUnit(Request $request) {
        $store = JWTAuth::parseToken()->toUser()->current;
        
        try {
            $check = DB::table('units')->where([
                'store_id' =>  $store,
                'batch_no' => $request['batch_no']
            ])->count();
            if($check != 0){
                return response()->json([
                    'status' => 2,
                ], 200);
            }else{
                return response()->json([
                    'status' => 1,
                ], 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }



    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $user = JWTAuth::parseToken()->toUser();
        $store_id = $user->current;
        $price = 0.00;
        $cost = 0.00;
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        if($request['sellingPrice'] != '') {
            $price = number_format((float)$request['sellingPrice'], 2, '.', '');
        }
        if($request['cost'] != '') {
            $cost = number_format((float)$request['cost'], 2, '.', '');
        }
         try {
            $product = Product::findOrFail($id);
            $product->name = $request['name'];
            $product->cost = $cost;
            $product->selling_price = $price;
            $product->description = $request['description'];
            $product->supplier_id = $request['supplier'];
            $product->expires = $request['expires'];
            $product->update();
            if($request['tempImage'] != null && $product->image != $request['tempImage']) {
                if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['tempImage'])) {
                    $old_pic = $product->image;
                    Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['tempImage'], $userAdminID.'/'.$store_id.'/'.$request['tempImage']);
                    $product->image = $request['tempImage'];
                    $product->update();
                    if(Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$old_pic)) {
                        Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$old_pic);
                    }
                };
            }elseif ($request['tempImage'] == '') {
                if(!$product->image == null) {
                    if(Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$product->image)) {
                        Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$product->image);
                    }
                }
                $product->image = null;
                $product->update();
            }else{
                $productimg = Product::find($product->id);
                $productimg->image = $request['tempImage'];
                $productimg->update();
            }
            if(Storage::disk('public')->exists($userAdminID.'/temp')) {
                Storage::deleteDirectory('public/'.$userAdminID.'/temp');
            }
            $units = Product::find($id)->getUnits;
            foreach ($units as $key => $value) {
                $value->expires = $request['expires'];
                $value->update();
            }
            $newProduct = DB::table('products')->where('id', $product->id)->first();

        } catch(\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'body' => 'Could not upload the product, please check your connection.'
            ], 500);

        }
        return response()->json([
            'title' => 'Success',
            'body' => 'Product is updated!',
            'product' => $newProduct,
            'units' => $units
        ], 200);

    }


    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            $tagItems = Store::find($store_id)->getFilters()
            ->where('product_id', $id)
            ->get();
            if(count($tagItems) > 0) {
                foreach($tagItems as $item) {
                    $item->delete();
                }
            }
            $units = Product::find($id)->getUnits;
            if(count($units) > 0) {
                foreach($units as $unit) {
                    $unit->delete();
                }
            }
            $product = Product::findOrFail($id);
            $image = $product->image;
            if (Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$image)) {
                Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$image);
            }
            $product->delete();

        } catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'Product is deleted',
            'id' => $id
        ], 200);
    }

}
