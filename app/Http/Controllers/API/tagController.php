<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Store;
use App\Tag;
use App\TagItem;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class tagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = JWTAuth::parseToken()->toUser();
        // //$tags = Store::find($user->current)->getTags;
        // $filters = DB::table('tag_items')
        //     ->join('products', 'tag_items.product_id', '=', 'products.id')
        //     ->where('tag_items.store_id', '=', $user->current)
        //     ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.name', 'products.image', 'products.batch_no')
        //     ->get();
        // return response()->json([
        //     'filters' => $filters
        // ], 200);
    }
    public function getAllFilters(Request $request) {
        
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $filters = [];
        $stores = [];
        if($user->role == 1) {
            $stores = User::find($user->id)->getStores;
        }elseif ($user->role == 2) {
            $stores = User::find($user->admin_id)->getStores;
        }
        if(count($stores) > 0) {
            $store_id = $user->current;
            $tags = Store::find($store_id)->getTags;
            if(count($tags) != 0){
                $filters = DB::table('tag_items')
                ->join('products', 'tag_items.product_id', '=', 'products.id')
                ->where(['tag_items.store_id' => $store_id , 'products.deleted' => false ])
                ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.stock', 'products.name', 'products.image', 'products.cost', 'products.selling_price', 'products.discount', 'products.created_at')
                ->get();
            } 
        }
        return response()->json([
            'filters' => $filters
        ], 200);
        
    }
    public function getThisFilter(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $tag_id = $request['id'];

        try {
            $tag = DB::table('tags')->where(['id' => $tag_id, 'store_id' => $store_id])->first();
            $filters = '';
            if($tag) {
                $filters = DB::table('tag_items')
                ->join('products', 'tag_items.product_id', '=', 'products.id')
                ->where(['tag_items.store_id' =>  $store_id, 'tag_items.tag_id' => $tag_id, 'products.deleted' => false ])
                ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.name', 'products.image', 'products.selling_price', 'products.discount', 'products.stock')
                ->get();
            }
        }catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }  
        return response()->json([
            'filters' => $filters,
            'tag' => $tag
        ], 200);
    }

  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $this->validate($request, [
            'tag' => 'required',
            'products' => 'required'
        ]);
     
        try {
            $tagg = trim($request['tag']);
            $count = DB::table('tags')->where(['store_id' => $store_id, 'name' => $tagg])->count();
            //$count = count($CheckTag);
            if ($count < 1){
                $tag = new Tag();
                $tag->store_id = $store_id;
                $tag->name = $tagg;
                $tag->save();
                $tag_id = $tag->id;
                foreach($request['products'] as $product) {
                    $tagItem = new TagItem();
                    $tagItem->tag_id = $tag_id;
                    $tagItem->product_id = $product['id'];
                    $tagItem->store_id = $store_id;
                    $tagItem->save();
                };
                $filters = DB::table('tag_items')
                    ->join('products', 'tag_items.product_id', '=', 'products.id')
                    ->where('tag_items.store_id', '=', $user->current)
                    ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.name', 'products.image')
                    ->get();

                return response()->json([
                    'title' => 'Successful!',
                    'status' => 1,
                    'tag' => $tag,
                    'message' => '"'.$tagg.'"'.' tag is created.',
                    'id' => $tag_id,
                    'filters' => $filters,
                ], 200);

            }else{
                return response()->json([
                    'title' => 'Error!',
                    'status' => 2,
                    'message' => '"'.$tagg.'"'.' tag already exists.' 
                ], 200);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not add tag, please check your connection.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $this->validate($request, [
            'tag' => 'required',
            'products' => 'required'
        ]);
        
        try {
            $newTag = trim($request['tag']);
            $CheckTag = DB::table('tags')->where(['store_id' => $store_id, 'id' => $id])->first();
            // $findOldTags = DB::table('tag_items')->where(['tag_id' => $id, 'store_id' => $store_id])->get();
            $findOldTags = Store::find($store_id)->getFilters()
            ->where('tag_id', $id)
            ->get();

            if($CheckTag->name == $newTag) {
                //created
                foreach ($findOldTags as $old) {
                    // $tagI = TagItem::findOrFail($old->id);
                    $old->delete();
                }
                foreach($request['products'] as $product) {
                    $tagItem = new TagItem();
                    $tagItem->tag_id = $id;
                    $tagItem->product_id = $product['id'];
                    $tagItem->store_id = $store_id;
                    $tagItem->save();
                };
                $tags = Store::find($user->current)->getTags;
                
                
            }else {
                $count = DB::table('tags')->where(['store_id' => $store_id, 'name' => $newTag])->count();
                if($count < 1) {
                    //created
                    $tag = Tag::findOrFail($id);
                    $tag->name = $newTag;
                    $tag->update();

                    
                    foreach ($findOldTags as $old) {
                        // $tagI = TagItem::findOrFail($old->id);
                        $old->delete();
                    }
                    foreach($request['products'] as $product) {
                        $tagItem = new TagItem();
                        $tagItem->tag_id = $id;
                        $tagItem->product_id = $product['id'];
                        $tagItem->store_id = $store_id;
                        $tagItem->save();
                    }; 
                    $tags = Store::find($user->current)->getTags;

                }else {

                    return response()->json([
                        'title' => 'Error!',
                        'status' => 2,
                        'message' => '"'.$newTag.'"'.' tag already exists.' 
                    ], 200);
                    
                }
                
            }

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not tag, please check your connection.'
            ], 500);
        }
        return response()->json([
            'title' => 'Successful!',
            'status' => 1,
            'message' => '"'.$newTag.'"'.' tag is created.',
            'tags' => $tags
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        
        try{
            $tagItems = Store::find($store_id)->getFilters()
            ->where('tag_id', $id)
            ->get();
            if(count($tagItems) > 0) {
                foreach($tagItems as $item) {
                    $item->delete();
                }
                $tag = Tag::findOrFail($id);
                $tag->delete();
            }
        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'Tag is deleted successfully.',
            'id' => $id
        ], 200);
    }
}
