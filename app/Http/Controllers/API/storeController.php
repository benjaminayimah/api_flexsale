<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class storeController extends Controller
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
    public function switchStore(Request $request) {
        $user = JWTAuth::parseToken()->toUser(); 
        $stores = '';
        if($user->role == '1') {
            $stores = User::find($user->id)->getStores;
        }else if($user->role == '2'){
            $stores = User::find($user->admin_id)->getStores;
        }
        foreach ($stores as $store) {
            try{
                 if ($store->id == $request['storeID']) {
                    $user = User::find($user->id);
                    $user->current = $request['storeID'];
                    $user->update();
                    return response()->json([
                        'status' => 1,
                        'message' => 'successful',
                    ], 200);
                }
           
            }catch(JWTException $e) {
                return response()->json([
                    'title' => 'Error!'
                ], 500);
            }
            
        }
    }
    public function store(Request $request)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->toUser();
        $this->validate($request, [
            'name' => 'required',
            'phone1' => 'required',
            'address' => 'required',
            'city' => 'required',
            'region' => 'required',
            'country' => 'required',  
        ]);
        try {
        $store = Store::findOrFail($user->current);
        $store->name = $request['name'];
        $store->phone_1 = $request['phone1'];
        $store->phone_2 = $request['phone2'];
        $store->address = $request['address'];
        $store->city = $request['city'];
        $store->region = $request['region'];
        $store->country = $request['country'];
        $store->update();
            
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!'
            ], 500);
        }
        $thisStore = Store::findOrFail($user->current);
        return response()->json([
            'message' => 'Details Updated!',
            'id' => $id,
            'store' => $thisStore
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
        //
    }
}
