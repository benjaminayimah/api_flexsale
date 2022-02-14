<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Store;
use App\Tag;
use App\TagItem;

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

        $store_id = JWTAuth::parseToken()->toUser()->current;
        $filters = DB::table('tag_items')
            ->join('products', 'tag_items.product_id', '=', 'products.id')
            ->where('tag_items.store_id', '=', $store_id)
            ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.name', 'products.image', 'products.batch_no')
            ->get();
            
        return response()->json([
            'filters' => $filters
        ], 200);
        
    }
    public function getThisFilter(Request $request) {
        $store_id = JWTAuth::parseToken()->toUser()->current;
        $tag_id = $request['id'];

        try {
            $tag = DB::table('tags')->where(['id' => $tag_id, 'store_id' => $store_id])->first();
            $filters = '';
            if($tag) {
                $filters = DB::table('tag_items')
                ->join('products', 'tag_items.product_id', '=', 'products.id')
                ->where(['tag_items.store_id' =>  $store_id, 'tag_items.tag_id' => $tag_id ])
                ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.name', 'products.image', 'products.batch_no')
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
                $tags = DB::table('tags')->where(['store_id' => $store_id, 'name' => $tagg])->first();
                $filters = DB::table('tag_items')
                    ->join('products', 'tag_items.product_id', '=', 'products.id')
                    ->where('tag_items.store_id', '=', $user->current)
                    ->select('tag_items.id', 'tag_items.tag_id', 'tag_items.store_id', 'products.id', 'products.name', 'products.image', 'products.batch_no')
                    ->get();

                return response()->json([
                    'title' => 'Successful!',
                    'status' => 1,
                    'tag' => $tags,
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
                'message' => 'Could not tag, please check your connection.'
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
            $findOldTags = DB::table('tag_items')->where(['tag_id' => $id, 'store_id' => $store_id])->get();

            if($CheckTag->name == $newTag) {
                //created
                foreach ($findOldTags as $old) {
                    $tagI = TagItem::findOrFail($old->id);
                    $tagI->delete();
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
                        $tagI = TagItem::findOrFail($old->id);
                        $tagI->delete();
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
            $tagItems = DB::table('tag_items')->where(['tag_id' => $id, 'store_id' => $store_id])->get();
            if(count($tagItems) > 0) {
                foreach($tagItems as $item) {
                    $tagItem = TagItem::findOrFail($item->id);
                    $tagItem->delete();
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
