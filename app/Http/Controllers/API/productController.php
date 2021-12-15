<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use App\Image;
use App\User;
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
        $user = JWTAuth::parseToken()->toUser();
        $products = User::find($user->id)->getProducts;
        return response()->json([
            'products' => $products

        ], 200);

    }

    public function store(Request $request)
    {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
         /*$this->validate($request, [
            'name' => 'required',
            'price' => 'required|integer',
            'image'=> 'required',
            'category' => 'required'
        ]);*/
        try {

            $user = JWTAuth::parseToken()->toUser();
            $user_id = $user->id;

            $category = null;
            $status = 'active';
            if ($request['status'] == '2'){
                $status = 'draft';
            } elseif ($request['status'] == '0') {
                $status = 'disabled';
            }

            if ($request['category'] != null) {
                $checkcat = DB::table('categories')->where('id', $request['category'])->first();
                $category = $checkcat->name;
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

            $product = new Product();
            $product->user_id = $user_id;
            $product->name = $request['name'];
            $product->description = $request['description'];
            $product->price = $request['price'];
            $product->compared_as = $request['compared'];
            $product->cpu = $request['cpu'];
            $product->profit = $request['profit'];
            $product->profit_margin = $request['profitMargin'];
            $product->qty_before = $request['quantity'];
            $product->quantity = $request['quantity'];
            $product->discount = null;
            $product->sku = $request['sku'];
            $product->barcode = $request['barcode'];
            $product->track_qty = $track_sales;
            $product->continue_selling = $continue_selling;
            $product->status = $status;
            $product->category = $category;
            $product->product_type = $request['type'];
            $product->best_seller = $best_seller;
            $product->new_arrival = $new_arrival;
            $product->cover_img = null;
            $product->save();
            $product_id = $product->id;


            $tempImg =  $request['tempImage'];
            if(count($tempImg) > 0 ) {
                //$i = 0;
                foreach ($tempImg as $img) {
                    if (Storage::disk('public')->exists($user_id.'/temp'.'/'.$img['image'])) {
                        Storage::disk('public')->move($user_id.'/temp'.'/'.$img['image'], $user_id.'/'.$img['image']);
                        //Storage::putFileAs('me', $user->id.'/'.$img['image']);

                        $image = new Image();
                        $image->product_id = $product_id;
                        $image->name = $img['image'];
                        $image->save();
                    };
                    //$i++;
                }
                $productimg = Product::find($product_id);
                $productimg->cover_img = $tempImg[0]['image'];
                $productimg->update();
            };

        }
        catch (\Throwable $th) {

            return response()->json([
                'title' => 'Error!',
                'body' => 'Could not upload the product, please check your connection.'
            ], 500);
        }
        return response()->json([
            'title' => 'Product is successfully added',
            'body' => 'You may continue to add another product.',
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
