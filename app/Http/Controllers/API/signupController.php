<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SebastianBergmann\Environment\Console;
use Tymon\JWTAuth\Facades\JWTAuth;


class signupController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|min:5',
        ]);
        


        try {
            $newuser = new User();
            $newuser->name = $request['name'];
            $newuser->email = $request['email'];
            $newuser->password = bcrypt($request['password']);
            $newuser->save();

            $newuser->admin_id = $newuser->id;
            $newuser->update();

            // $newuser = User::create(request(['name', 'email', 'password']));

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create user.'
            ], 500);
        }
        return response()->json([
            'title' => 'Success!',
            'status' => 'Account successfully created.'
        ], 200);

    }
    public function createAdminUser(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|min:5',
        ]);
        try {
            $newuser = new User();
            $newuser->name = $request['name'];
            $newuser->email = $request['email'];
            $newuser->password = bcrypt($request['password']);
            $newuser->role = '2';
            $newuser->admin_id = $user->id;
            $newuser->save(); 
            
            $num = count($request['store']);
            if($num == 2){
                $store1 = $request['store'][0]['id'];
                $store2 = $request['store'][1]['id'];
                $updatestore = User::findOrFail($newuser->id);
                $updatestore->store_1 = $store1;
                $updatestore->store_2 = $store2;
                $updatestore->current = $store1;
                $updatestore->update(); 
            }elseif ($num == 1) {
                $store = $request['store'][0]['id'];
                $updatestore = User::findOrFail($newuser->id);
                $updatestore->store_1 = $store;
                $updatestore->current = $store;
                $updatestore->update(); 
            }
            $admin = User::findOrFail($newuser->id);

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create user.'
            ], 500);
        }
        return response()->json([
            'title' => 'Successful!',
            'message' => 'New user is created.',
            'admin' => $admin
        ], 200);

    }
    public function editAdminUser(Request $request, $id) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'email' => 'required|email',
            'name' => 'required'
        ]);
        $newuser = User::findOrFail($id);
        if($request['email'] != $newuser->email) {
            $this->validate($request, [
                'email' => 'unique:users'
            ]);
        }
        try {
            $newuser->name = $request['name'];
            $newuser->email = $request['email'];
            $newuser->update();

            $num = count($request['store']);
            if($num > 0) {
                if($num == 2){
                    $store1 = $request['store'][0]['id'];
                    $store2 = $request['store'][1]['id'];
                    $updatestore = User::findOrFail($id);
                    $updatestore->store_1 = $store1;
                    $updatestore->store_2 = $store2;
                    $updatestore->current = $store1;
                    $updatestore->update(); 
                }elseif ($num == 1) {
                    $store = $request['store'][0]['id'];
                    $updatestore = User::findOrFail($id);
                    $updatestore->store_1 = $store;
                    $updatestore->store_2 = null;
    
                    $updatestore->current = $store;
                    $updatestore->update(); 
                }
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'status' => 'Could not create user.'
            ], 500);
        }
        $newadmin = User::findOrFail($id);
        return response()->json([
            'title' => 'Successful!',
            'message' => 'User is updated.',
            'admin' => $newadmin
        ], 200);
    }
    public function resetPassword(Request $request, $id) {

        return response()->json([
            'pass' => $request['password'],
            'id' => $id
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
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $admin = User::findOrFail($id);
        if($admin->admin_id == $user->id){
            try {
                $admin->delete();
    
            } catch (\Throwable $th) {
                return response()->json(['status' => 'An error has occured!'], 500);
            }
        }else{
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'User deleted successfully.',
            'id' => $id
        ], 200);
    }
}
