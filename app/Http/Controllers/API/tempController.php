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
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            if($request->file('image')){
                $user = JWTAuth::parseToken()->toUser();
                $rawfile = $_FILES['image']["name"];
                $split = explode(".", $rawfile);
                $fileExt = end($split);
                $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', 'prod_'.$user->current);
                $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
                $file = $request->file('image');
    
                if (!Storage::directories('public/'.$userAdminID.'/temp')) {
                    Storage::makeDirectory('public/'.$userAdminID.'/temp');
                }
                Storage::disk('public')->put($userAdminID.'/temp'.'/'.$filename, File::get($file));
                
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
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            if($request->file('image')){
                $user = JWTAuth::parseToken()->toUser();
                $rawfile = $_FILES['image']["name"];
                $split = explode(".", $rawfile);
                $fileExt = end($split);
                $imgFinaltitle = preg_replace('#[^a-z0-9]#i', '', 'store_'.$userAdminID);
                $filename = $imgFinaltitle . '_'. rand(1,999999999) . '.'. $fileExt;
                $file = $request->file('image');
    
                if (!Storage::directories('public/'.$userAdminID.'/temp')) {
                    Storage::makeDirectory('public/'.$userAdminID.'/temp');
                }
                Storage::disk('public')->put($userAdminID.'/temp'.'/'.$filename, File::get($file));
                
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
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        
        try {
            Storage::deleteDirectory('public/'.$userAdminID.'/temp');
            Storage::makeDirectory('public/'.$userAdminID.'/temp');
            Storage::disk('public')->copy($userAdminID.'/'.$store_id.'/'.$request['id'], $userAdminID.'/temp'.'/'.$request['id']);

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
        $userAdminID = $user->id;
        if($user->role != 1) {
            $userAdminID = $user->admin_id;
        }
        Storage::deleteDirectory('public/'.$userAdminID.'/temp');
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
