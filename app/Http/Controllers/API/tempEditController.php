<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class tempEditController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            if (!Storage::directories('public/'.$user->id.'/temp2')) {
                Storage::makeDirectory('public/'.$user->id.'/temp2');
            }
            Storage::disk('public')->put($user->id.'/temp2'.'/'.$filename, File::get($file));
            $id = rand(1,999999999);
            return response()->json([
                'id' => $id,
                'img' => $filename,
            ], 200);
        }
    }

    public function destroy($id)
    {
        $user_id = JWTAuth::parseToken()->toUser()->id;
        //move to deleted folder
        if (Storage::disk('public')->exists($user_id.'/temp2'.'/'.$id)) {
            //Storage::disk('public')->delete($user_id.'/temp2'.'/'.$id);
            if (!Storage::directories('public/'.$user_id.'/deleted')) {
                Storage::makeDirectory('public/'.$user_id.'/deleted');
            }
            if (!Storage::disk('public')->exists($user_id.'/deleted'.'/'.$id)) {
                Storage::disk('public')->move($user_id.'/temp2'.'/'.$id, $user_id.'/deleted'.'/'.$id);
            }

        }
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
