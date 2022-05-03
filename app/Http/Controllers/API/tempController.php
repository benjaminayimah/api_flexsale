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
    
                if (!Storage::directories('public/'.$user->id.'/temp')) {
                    Storage::makeDirectory('public/'.$user->id.'/temp');
                }
                Storage::disk('public')->put($user->id.'/temp'.'/'.$filename, File::get($file));
                
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
    public function storeTempUpload(Request $request) {
        try {
            if($request->file('image')){
                $user = JWTAuth::parseToken()->toUser();
                $rawfile = $_FILES['image']["name"];
                $split = explode(".", $rawfile);
                $fileExt = end($split);
                $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', 'store_'.$user->id);
                $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
                $file = $request->file('image');
    
                if (!Storage::directories('public/'.$user->id.'/temp')) {
                    Storage::makeDirectory('public/'.$user->id.'/temp');
                }
                Storage::disk('public')->put($user->id.'/temp'.'/'.$filename, File::get($file));
                
                return response()->json([
                    'image' => $filename,
                ], 200);
    
            }
        } catch (JWTException $e) {
            return response()->json([
                'msg' => 'Error!'
            ], 500);
        }
    }
    public function resetTempImage(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = JWTAuth::parseToken()->toUser()->current;
        
        try {
            Storage::deleteDirectory('public/'.$user->id.'/temp');
            Storage::makeDirectory('public/'.$user->id.'/temp');
            Storage::disk('public')->copy($user->id.'/'.$store_id.'/'.$request['id'], $user->id.'/temp'.'/'.$request['id']);

        } catch (JWTException $e) {
            return response()->json([
                'msg' => 'Error!'
            ], 500);
        }
        return response()->json([
            'image' => $request['id'],
        ], 200);

    }
    // public function delProdTemp() {
    //     if (! $user = JWTAuth::parseToken()->authenticate()) {
    //         return response()->json(['status' => 'User not found!'], 404);
    //     }
    //     Storage::deleteDirectory('public/'.$user->current.'/temp');
    //     return response()->json([
    //         'status' => 'success',
    //     ], 200);
    // }
    public function delStoreTemp($id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        //delete from folder
        Storage::deleteDirectory('public/'.$user->id.'/temp');
        return response()->json([
            'status' => 'success'
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
        //delete from folder
        Storage::deleteDirectory('public/'.$user->current.'/temp');

        // if (Storage::disk('public')->exists($user->current.'/temp'.'/'.$id)) {
        //     Storage::disk('public')->delete($user->current.'/temp'.'/'.$id);
        // }
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
