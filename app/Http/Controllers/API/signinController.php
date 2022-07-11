<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Facades\Socialite;


class signinController extends Controller
{

    public function store(Request $request)
    {
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
    public function verifyToken($id_token) {
        return true;
    }
    public function OAuthSignIn(Request $request) {
        $status = 0;
        $token = null;
        $id_token = 'me';
        if($this->verifyToken($id_token)) {
            $email = $request['email'];
            $id = $request['sub'];
            $user = $this->CheckUser($email, $id);
            try {
                if(isset($user)) {
                    $status = 1;
                    if( !$token = JWTAuth::fromUser($user)) {
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
        }else {
            return response()->json([
                'status' => 'Invalid token!'
            ], 200);
        }

    }
    public function OAuthSignUp(Request $request) {
        $token = null;
        $email = $request['email'];
        $id = $request['sub'];
        $newuser = $this->CheckUser($email, $id);
        if(!isset($newuser)) {
            try {
                $newuser = new User();
                $newuser->name = $request['name'];
                $newuser->email = $email;
                $newuser->email_verified = true;
                $newuser->oauth = true;
                $newuser->oauth_id = $id;
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
    public function CheckUser($email, $id)
    {
        return User::all()
        ->where('email', $email)
        ->where('oauth_id', $id)
        ->first();
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
