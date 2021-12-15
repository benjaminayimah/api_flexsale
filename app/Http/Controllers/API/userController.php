<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Image;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;


class userController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = JWTAuth::parseToken()->toUser();
        //if (Storage::disk('public')->exists('public/'.$user->id.'/temp')) {
            Storage::deleteDirectory('public/'.$user->id.'/temp');
        //}if (Storage::disk('public')->exists('public/'.$user->id.'/temp2')) {
            Storage::deleteDirectory('public/'.$user->id.'/temp2');
        //}if (Storage::disk('public')->exists('public/'.$user->id.'/deleted')) {
            Storage::deleteDirectory('public/'.$user->id.'/deleted');
        //}

        try {

            if($user->role == 'editor' || $user->role == 'admin' || $user->role == 'super'){
                return response()->json([
                    'status' => 1,
                    'user' => $user
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Token error.'
            ], 500);
        }


        return response()->json([
            'status' => ""
        ], 200);

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
