<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Product as ProductResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class tempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function store(Request $request)
    {

        if($request->file('image')){
            $rawfile = $_FILES['image']["name"];
            $split = explode(".", $rawfile);
            $fileExt = end($split);
            $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', 'tmp');
            $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
            $file = $request->file('image');

            $user = JWTAuth::parseToken()->toUser();
            if (!Storage::directories('public/'.$user->id.'/temp')) {
                Storage::makeDirectory('public/'.$user->id.'/temp');
            }
            Storage::disk('public')->put($user->id.'/temp'.'/'.$filename, File::get($file));
            $id = rand(1,999999999);
            return response()->json([
                'id' => $id,
                'img' => $filename,
            ], 200);

        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->toUser();
        //delete from folder
        if (Storage::disk('public')->exists($user->id.'/temp'.'/'.$id)) {
            Storage::disk('public')->delete($user->id.'/temp'.'/'.$id);
        }
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
