<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Product as ProductResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;


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
        

        try {
            if($request->file('image')){
                $user = JWTAuth::parseToken()->toUser();
                $rawfile = $_FILES['image']["name"];
                $split = explode(".", $rawfile);
                $fileExt = end($split);
                $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', 'prod_'.$user->current);
                $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
                $file = $request->file('image');
    
                if (!Storage::directories('public/'.$user->current.'/temp')) {
                    Storage::makeDirectory('public/'.$user->current.'/temp');
                }
                Storage::disk('public')->put($user->current.'/temp'.'/'.$filename, File::get($file));
                
                return response()->json([
                    'img' => $filename,
                ], 200);
    
            }
        } catch (JWTException $e) {
            return response()->json([
                'msg' => 'Error!'
            ], 500);
        }

    }
    public function delAllTempImg() {
        $user = JWTAuth::parseToken()->toUser();
        //if (Storage::disk('public')->exists('public/'.$user->current.'/temp')) {
            Storage::deleteDirectory('public/'.$user->current.'/temp');
        //}if (Storage::disk('public')->exists('public/'.$user->current.'/temp2')) {
            Storage::deleteDirectory('public/'.$user->current.'/temp2');
        //}if (Storage::disk('public')->exists('public/'.$user->current.'/deleted')) {
            Storage::deleteDirectory('public/'.$user->current.'/deleted');
        //}
        return response()->json([
            'status' => 'success',
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
        $user = JWTAuth::parseToken()->toUser();
        //delete from folder
        if (Storage::disk('public')->exists($user->current.'/temp'.'/'.$id)) {
            Storage::disk('public')->delete($user->current.'/temp'.'/'.$id);
        }
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
