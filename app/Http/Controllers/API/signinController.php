<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
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
    public function OAuthSignIn(Request $request) {
        $status = 0;
        $email = $request['email'];
        $token = null;
        $chekUser = User::all()
        ->where('email', $email)
        ->first();
        try {
            if(isset($chekUser)) {
                $status = 1;
                if( !$token = JWTAuth::fromUser($chekUser)) {
                    return response()->json([
                        'title' => 'Error!',
                        'status' => 'Invalid credentials'
                    ], 401);
                }
            }
        } catch (JWTException $e) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create token.'
            ], 500);
        }
    
        return response()->json([
            'status' => $status,
            'token' => $token
        ], 200);
        
    }
    public function OAuthSignUp(Request $request) {
        $token = null;
        try {
            $newuser = new User();
            $newuser->name = $request['name'];
            $newuser->email = $request['email'];
            $newuser->email_verified = true;
            $newuser->oauth = true;
            $newuser->has_pass = false;
            $newuser->oauth_provider = $request['type'];
            $newuser->save();
            $newuser->admin_id = $newuser->id;
            $newuser->update();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create user.'
            ], 500);
        }

        try {
            if( !$token = JWTAuth::fromUser($newuser)) {
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
            'user' => $newuser,
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
