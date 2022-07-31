<?php

namespace App\Http\Controllers\API;

use App\Email;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class forgotpassController extends Controller
{
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        try {
            $email = $request['email'];
            if($this->validateEmail($email)) {
                $this->sendMail($email);
                return response()->json([
                    'title' => 'Successful!',
                    'message' => 'Check your inbox, we have sent a link to reset email.',
                    'email' => $email
                ], 200);
            }else {
                return response()->json([
                    'title' => 'Error!',
                    'email' => $email,
                ], 404);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }
        
    }

    public function validateEmail($email) {
        $user = User::where('email', $email)->first();
        if(isset($user))
        return true;
        else return false;
    }
    public function sendMail($email){
        $token = $this->generateToken($email);
        $data = new Email();
        $data->title = 'Reset Password';
        $data->token = $token;
        $data->email = $email;
        $data->hideme = Carbon::now();
        Mail::to($email)
        ->send(new PasswordResetRequest($data));
    }
    public function generateToken($email){
        $isOtherToken = DB::table('recover_password')->where('email', $email)->first();
        if($isOtherToken) {
          return $isOtherToken->token;
        }
        $token = Str::random(80);
        $this->storeToken($token, $email);
        return $token;
    }
    public function storeToken($token, $email){
        DB::table('recover_password')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()            
        ]);
    }
    public function ResetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);
        try {
            return $this->updatePasswordRow($request)->count() > 0 ? $this->doResetPassword($request) : $this->tokenNotFoundError();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
            ], 500);
        }

    }
    private function updatePasswordRow($request){
        return DB::table('recover_password')->where([
            'email' => $request->email,
            'token' => $request->token
        ]);
    }
    private function tokenNotFoundError() {
        return response()->json([
            'title' => 'Error!',
            'status' => 'Either your email or token is invalid. Please try resending a new `forgot password` link.'
        ], 401);
    }
    private function doResetPassword($request) {
        $userData = User::whereEmail($request->email)->first();
        $userData->update([
        'password'=>bcrypt($request->password)
        ]);
        $this->updatePasswordRow($request)->delete();
        if($this->SignInUser($request)) {
            return response()->json([
                'token' => $this->SignInUser($request)
            ], 200);
        }else{
            $this->tokenNotFoundError();
        }
    }
    public function SignInUser($request) {
        $credentials = $request->only('email', 'password');
        if( !$token = JWTAuth::attempt($credentials)) {
            return '';
        }
        return $token;
    }

    public function destroy($id)
    {
        //
    }
}
