<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'name' => 'required',
            'phone1' => 'required',
            'address' => 'required',
            'city' => 'required',
            'region' => 'required',
            'country' => 'required',  
        ]);
        try {
        $store = new Store();
        $store->user_id = $user->id;
        $store->name = $request['name'];
        $store->phone_1 = $request['phone1'];
        $store->phone_2 = $request['phone2'];
        $store->address = $request['address'];
        $store->city = $request['city'];
        $store->region = $request['region'];
        $store->country = $request['country'];
        $store->save();
        
        $thisStore = '';
        $stores = DB::table('stores')->where('user_id', $user->id)->get();
        if(count($stores) == 1) {
            $userquery = User::findOrFail($user->id);
            $userquery->current = $stores[0]->id;
            $userquery->update();
            $thisStore = Store::findOrFail($stores[0]->id);
            $user = $userquery;
        }else{
            $thisStore = $store;
        }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!'
            ], 500);
        }
        return response()->json([
            'message' => 'Store created!',
            'store' => $thisStore,
            'user' => $user
        ], 200);
    }
    public function updateStoreImage(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {

            $store = '';
            if (Storage::disk('public')->exists($user->id.'/temp'.'/'.$request['image'])) {
                Storage::makeDirectory('public/'.$user->id.'/'.$request['store']);
                Storage::disk('public')->move($user->id.'/temp'.'/'.$request['image'], $user->id.'/'.$request['store'].'/'.$request['image']);
                $store = Store::findOrFail($request['store']);
                $store->image = $request['image'];
                $store->update();
                Storage::deleteDirectory('public/'.$user->id.'/temp');
            };
            
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!'
            ], 500);
        }
        return response()->json([
            'message' => 'Store created!',
            'store' => $store,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
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