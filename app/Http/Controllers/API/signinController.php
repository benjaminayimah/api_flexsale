<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class signinController extends Controller
{

    public function store(Request $request)
    {
         /*$user = JWTAuth::parseToken()->toUser(); */
         $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = $request->only('email', 'password');
        try {
            if( !$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'title' => 'Error!',
                    'status' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create token.'
            ], 500);
        }
        return response()->json([
            'token' => $token
        ], 200);
    }

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
    public function destroy()
    {
        $user = JWTAuth::parseToken()->toUser(); 
        return response()->json(['status', 'logged out!'], 200);
    }
}
