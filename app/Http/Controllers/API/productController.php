<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use App\Image;
use App\User;
use App\Store;
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
          $this->validate($request, [
             'name' => 'required'
         ]);
        
        try {
            $user = JWTAuth::parseToken()->toUser();

            $product = new Product();
            $product->store_id = $user->current;
            $product->name = $request['name'];
            $product->image = null;
            $product->batch_no = $request['batchNumber'];
            $product->cost = $request['cost'];
            $product->selling_price = $request['sellingPrice'];
            $product->stock = $request['stock'];
            $product->description = $request['description'];
            $product->supplier = $request['supplier'];
            $product->track_qty = $request['trackQty'];
            $product->prod_type = $request['prodType'];
            $product->profit = $request['profit'];
            $product->profit_margin = $request['profitMargin'];
            $product->added_by = $user->name;
            $product->save();

            if (Storage::disk('public')->exists($user->current.'/temp'.'/'.$request['tempImage'])) {
                Storage::disk('public')->move($user->current.'/temp'.'/'.$request['tempImage'], $user->current.'/'.$request['tempImage']);

                $productimg = Product::find($product->id);
                $productimg->image = $request['tempImage'];
                $productimg->update();
            };
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



   /* public function update(Request $request)
    {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }

        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|integer',
            'image'=> 'required',
            'category' => 'required'
        ]);
        $id = $request['id'];
        try {
            if($request->file('image')){
                $query = DB::table('products')->where('id', $id)->first();
                $old_image = $query->image;
                if($old_image !== null){
                    Storage::disk('public')->delete($old_image);
                 }
                 $rawfile = $_FILES['image']["name"];
                 $split = explode(".", $rawfile);
                 $fileExt = end($split);
                 $imgtitle = strtolower($request['name']);
                 $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', $imgtitle);
                 $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
                 $file = $request->file('image');

                 Storage::disk('public')->put($filename, File::get($file));

                 $discount = $request['discount'];
                    if ($discount == null){
                        $discount = 0;
                    }
                    $best_seller = 0;
                    $new_arrival = 0;
                    if($request['newArrival'] == 'true'){
                        $new_arrival = true;
                    }if($request['bestSeller'] == 'true'){
                        $best_seller = true;
                    }
                $product = Product::findOrFail($id);
                $product->name = $request['name'];
                $product->price = $request['price'];
                $product->discount = $discount;
                $product->category = $request['category'];
                $product->description = $request['description'];

                $product->image = $filename;

                $product->update();
            }else{
                $discount = $request['discount'];
                    if ($discount == null){
                        $discount = 0;
                    }
                    $best_seller = 0;
                    $new_arrival = 0;
                    if($request['newArrival'] == 'true'){
                        $new_arrival = true;
                    }if($request['bestSeller'] == 'true'){
                        $best_seller = true;
                    }
                $product = Product::findOrFail($id);
                $product->name = $request['name'];
                $product->price = $request['price'];
                $product->discount = $discount;
                $product->category = $request['category'];
                $product->description = $request['description'];
                //$product->new_arrival = $new_arrival;
                //$product->best_seller = $best_seller;
                $product->update();

                return response()->json([
                    'title' => 'Successful!',
                    'statusType' => 1,
                    'status' => 'Product updated succesfully.'
                ], 200);
            }

        } catch (\Throwable $th) {
            return response()->json(['status' => $th], 500);
        }
        return response()->json([
            'title' => 'Successful!',
            'statusType' => 1,
            'status' => 'Product updated succesfully.'
        ], 200);

    }*/


    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $images = Product::find($id)->image;
            //return $images;
            foreach($images as $image) {
                $img = Image::findOrFail($image->id);
                $img->delete();
                if (Storage::disk('public')->exists($user->id.'/'.$image->name)) {
                    Storage::disk('public')->delete($user->id.'/'.$image->name);
                }
            }
            $product = Product::findOrFail($id);
            $product->delete();

        } catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json(['status' => 'Product deleted successfully.'], 200);
    }
    public function bulkdelete(Request $request ) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try{
            foreach($request[0] as $id) {
                $images = Product::find($id)->image;
                foreach($images as $image) {
                    $img = Image::findOrFail($image->id);
                    $img->delete();
                    if (Storage::disk('public')->exists($user->id.'/'.$image->name)) {
                        Storage::disk('public')->delete($user->id.'/'.$image->name);
                    }
                }
                $product = Product::findOrFail($id);
                $product->delete();

            }
        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json(['status' => 'Products deleted successfully.'], 200);


    }
}
