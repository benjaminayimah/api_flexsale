<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
use App\Image;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;

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



        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
         /*$this->validate($request, [
            'name' => 'required',
        ]);*/

        try {

            $user = JWTAuth::parseToken()->toUser();
            $user_id = $user->id;

            $category = null;
            $status = null;
            if ($request['status'] == '1') {
                $status = 'active';
            }if ($request['status'] == '2'){
                $status = 'draft';
            } elseif ($request['status'] == '0') {
                $status = 'disabled';
            }
            $best_seller = 0;
            $new_arrival = 0;
            $track_sales = 0;
            $continue_selling = 0;
            if ($request['tags']['bs'] == true ) {
                $best_seller = 1;
            }if ($request['tags']['new'] == true) {
                $new_arrival = 1;
            }if ($request['track'] == true) {
                $track_sales = 1;
            }if ($request['continue'] == true) {
                $continue_selling = 1;
            }
            $tempImg =  $request['tempImage'];
            $deleted = $request['deleted'];


            $product = Product::findOrFail($id);
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->price = $request['price'];
            $product->compared_as = $request['compared'];
            $product->cpu = $request['cpu'];
            $product->profit = $request['profit'];
            $product->profit_margin = $request['profitMargin'];
            $product->discount = null;
            $product->sku = $request['sku'];
            $product->barcode = $request['barcode'];
            $product->track_qty = $track_sales;
            $product->continue_selling = $continue_selling;
            $product->product_type = $request['type'];
            $product->best_seller = $best_seller;
            $product->new_arrival = $new_arrival;
            $product->update();

            if ($request['category'] != null) {
                $checkcat = DB::table('categories')->where('id', $request['category'])->first();
                $category = $checkcat->name;
                $product->category = $category;
                $product->update();
            }
            if ($request['status'] != null) {
                $product->status = $status;
                $product->update();
            }

            if(count($tempImg) > 0 || count($deleted) > 0) {
                $checkimg = DB::table('images')->where('product_id', $id)->get();
                foreach ($checkimg as $img) {
                    $image = Image::findOrFail($img->id);
                    $image->delete();
                    if (Storage::disk('public')->exists($user_id.'/'.$img->name)) {
                        Storage::disk('public')->delete($user_id.'/'.$img->name);
                    }
                }
            }
            if(count($deleted) > 0 && count($tempImg) < 1 ) {
                $product->cover_img = null;
                $product->update();
            }

            if(count($tempImg) > 0) {
                $checkimg = DB::table('images')->where('product_id', $id)->get();
                foreach ($checkimg as $img) {
                    $image = Image::findOrFail($img->id);
                    $image->delete();
                    if (Storage::disk('public')->exists($user_id.'/'.$img->name)) {
                        Storage::disk('public')->delete($user_id.'/'.$img->name);
                    }
                }
                foreach ($tempImg as $img) {
                    if (Storage::disk('public')->exists($user_id.'/temp2'.'/'.$img['name'])) {
                        Storage::disk('public')->move($user_id.'/temp2'.'/'.$img['name'], $user_id.'/'.$img['name']);
                        //Storage::putFileAs('me', $user->id.'/'.$img['image']);
                    };
                    $image = new Image();
                    $image->product_id = $id;
                    $image->name = $img['name'];
                    $image->save();
                }
                $product->cover_img = $tempImg[0]['name'];
                $product->update();
            };

        }
        catch (\Throwable $th) {

            return response()->json([
                'title' => 'Error!',
                'body' => 'Could not upload the product, please check your connection.'
            ], 500);
        }
        return response()->json([
            'title' => 'Product is successfully updated',
            'body' => 'You may continue to add another product.',
            'thisCategory' => $category,
            'thisStatus' => $status
        ], 200);
    }


    public function destroy($id)
    {
        //
    }
}
