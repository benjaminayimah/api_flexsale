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
        $user = JWTAuth::parseToken()->toUser();
        $tags = Store::find($user->current)->getTags;
        // $tags = DB::table('tag_items')
        //     ->join('tags', 'tag_items.tag_id', '=', 'tags.id')
        //     //->join('products', 'stores.id', '=', 'tags.store_id')
        //     // ->join('products', )
        //     ->select('tag_items.*', 'stores.*')
        //     ->get();
           

        return response()->json([
            'tags' => $tags
        ], 200);
    }
    public function getAllFilters(Request $request) {

        $store_id = JWTAuth::parseToken()->toUser()->current;
        $filters = Store::find($store_id)->getFilters;
        
        return response()->json([
            'filters' => $filters
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
            $CheckTag = DB::table('tags')->where(['store_id' => $store_id, 'name' => $tagg])->get();
            $count = count($CheckTag);
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

                return response()->json([
                    'title' => 'Successful!',
                    'status' => 1,
                    'tags' => $tags,
                    'message' => '"'.$tagg.'"'.' tag is created.',
                    'id' => $tag_id,
                    'prod' => $request['products']
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
