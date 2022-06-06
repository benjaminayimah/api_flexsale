<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use App\Image;
use App\Store;
use App\TagItem;
use App\Unit;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
        
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $stock = '';
        $type = 0;
        $price = 0.00;
        $cost = 0.00;
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        if($request['prodType'] == '0') {
            $stock = count($request['batch']);
        }else{
            $stock = $request['batch']['stock'];
            $type = 1;
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
            $product->store_id = $user->current;
            $product->name = $request['name'];
            $product->image = null;
            $product->prod_type = $type;
            $product->cost = $cost;
            $product->selling_price = $price;
            $product->stock = $stock;
            $product->description = $request['description'];
            $product->supplier_id = $request['supplier'];
            $product->discount = null;
            $product->added_by = $user->name;
            $product->save();

            if($request['prodType'] == '0') {
             if(count($request['batch']) > 0) {
                 foreach ($request['batch'] as $key) {
                    $unit = new Unit();
                    $unit->store_id = $user->current;
                    $unit->product_id = $product->id;
                    $unit->batch_no = $key['batch_no'];
                    $unit->expiry_date = $key['expiry_date'];
                    if($today->gt($key['expiry_date']) ) {
                        $unit->active = 0;
                    }
                    $unit->save();
                    
                 }
             }
            }else {
                if ($request['batch']['batch_no']) {
                    $unit = new Unit();
                    $unit->store_id = $user->current;
                    $unit->product_id = $product->id;
                    $unit->batch_no = $request['batch']['batch_no'];
                    $unit->expiry_date = $request['batch']['expiry_date'];
                    if($today->gt($request['batch']['expiry_date']) ) {
                        $unit->active = 0;
                    }
                    $unit->save();
                }
                
            }if($request['tempImage'] != null) {
                if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['tempImage'])) {
                    Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['tempImage'], $userAdminID.'/'.$user->current.'/'.$request['tempImage']);
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
            'title' => 'Product is successfully added',
            'body' => 'You may continue to add another product.',
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
        $stock = '';
        $type = 0;
        $price = 0.00;
        $cost = 0.00;
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }

        if($request['prodType'] == '0') {
            $stock = count($request['batch']);
        }else{
            $stock = $request['batch']['stock'];
            $type = 1;
        }
        if($request['sellingPrice'] != '') {
            $price = number_format((float)$request['sellingPrice'], 2, '.', '');
        }
        if($request['cost'] != '') {
            $cost = number_format((float)$request['cost'], 2, '.', '');
        }
         $today = Carbon::today();
         try {
            $product = Product::findOrFail($id);
            $product->name = $request['name'];
            $product->prod_type = $type;
            $product->cost = $cost;
            $product->selling_price = $price;
            $product->stock = $stock;
            $product->description = $request['description'];
            $product->supplier_id = $request['supplier'];
            $product->update();

            // if($request['prodType'] == '0') {
            //  if(count($request['batch']) > 0) {
            //         $oldUnit = Product::find($id)->getUnits;
            //         foreach ($oldUnit as $key) {
            //             $key->delete();
            //         }
            //         foreach ($request['batch'] as $key) {
            //         $unit = new Unit();
            //         $unit->store_id = $user->current;
            //         $unit->product_id = $id;
            //         $unit->batch_no = $key['batch_no'];
            //         $unit->expiry_date = $key['expiry_date'];
            //         if($today->gt($key['expiry_date']) ) {
            //             $unit->active = 0;
            //         }
            //         $unit->save();

            //     }
            //  }
            // }else {
            //     if ($request['batch']['batch_no']) {
            //         $Oldunit = Product::find($id)->getUnits;
            //         foreach ($Oldunit as $key) {
            //             $key->delete();
            //         }
            //         $newUnit = new Unit();
            //         $newUnit->store_id = $user->current;
            //         $newUnit->product_id = $id;
            //         $newUnit->batch_no = $request['batch']['batch_no'];
            //         $newUnit->expiry_date = $request['batch']['expiry_date'];
            //         if($today->gt($request['batch']['expiry_date'])) {
            //             $newUnit->active = 0;
            //         }
            //         $newUnit->save();
            //     }
                
            // }
            if($request['tempImage'] != null && $product->image != $request['tempImage']) {
                if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['tempImage'])) {
                    $old_pic = $product->image;
                    Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['tempImage'], $userAdminID.'/'.$user->current.'/'.$request['tempImage']);
                    $product->image = $request['tempImage'];
                    $product->update();
                    if(Storage::disk('public')->exists($userAdminID.'/'.$user->current.'/'.$old_pic)) {
                        Storage::disk('public')->delete($userAdminID.'/'.$user->current.'/'.$old_pic);
                    }
                };
            }elseif ($request['tempImage'] == '') {
                if(!$product->image == null) {
                    if(Storage::disk('public')->exists($userAdminID.'/'.$user->current.'/'.$product->image)) {
                        Storage::disk('public')->delete($userAdminID.'/'.$user->current.'/'.$product->image);
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
            $newProduct = DB::table('products')->where('id', $product->id)->first();
            $units = Product::find($id)->getUnits;

        } catch(\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'body' => 'Could not upload the product, please check your connection.'
            ], 500);

        }
        return response()->json([
            'title' => 'Product is successfully updated',
            'body' => 'Product is successfully updated',
            'product' => $newProduct,
            'units' => $units
        ], 200);

    }


    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store = $user->current;
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            $tagItems = Store::find($store)->getFilters()
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
            $notification = Store::find($store)->getNotifications()
            ->where('product_id', $id)
            ->get();
            if(count($notification) > 0) {
                foreach($notification as $noti) {
                    $noti->delete();
                }
            }
            $product = Product::findOrFail($id);
            $image = $product->image;
            if (Storage::disk('public')->exists($userAdminID.'/'.$user->current.'/'.$image)) {
                Storage::disk('public')->delete($userAdminID.'/'.$user->current.'/'.$image);
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
