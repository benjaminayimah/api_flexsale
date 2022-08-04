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
                    $user2 = User::find($user->id);
                    $user2->current = $request['storeID'];
                    $user2->update();
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
            'address' => 'required',
            'city' => 'required',
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
    public function submitStImage(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $store_id = $user->current;
            $storeID = $request['storeID'];
            if($storeID == '') {
                $storeID = $store_id;
            } 
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['image'])) {
                Storage::makeDirectory('public/'.$userAdminID.'/'.$storeID);
                Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['image'], $userAdminID.'/'.$storeID.'/'.$request['image']);
                $store = Store::findOrFail($storeID);
                $store->image = $request['image'];
                $store->update();
                Storage::deleteDirectory('public/'.$userAdminID.'/temp');
            };
            return response()->json([
                'message' => 'Store created!',
                'store' => $store
            ], 200);
            
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
    public function updateStoreImage(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            if($request['image'] == null) { 
                $store = Store::find($store_id);
                $oldImage = $store->image;
                if(!$oldImage == null) {
                    if(Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$oldImage)) {
                        Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$oldImage);
                    }
                }
                $store->image = null;
                $store->update();
            }else{
                if (Storage::disk('public')->exists($userAdminID.'/temp'.'/'.$request['image'])) {
                    Storage::disk('public')->move($userAdminID.'/temp'.'/'.$request['image'], $userAdminID.'/'.$store_id.'/'.$request['image']);
                    $store = Store::find($store_id);
                    $oldImage = $store->image;
                    $store->image = $request['image'];
                    $store->update();
                    Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$oldImage);
                };
            }
            Storage::deleteDirectory('public/'.$userAdminID.'/temp');
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!'
            ], 500);
        }
        return response()->json([
            'message' => 'Store image is updated',
            'store' => $store
        ], 200);
        
    }


    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',  
        ]);
        try {
            $store = Store::findOrFail($id);
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
        $thisStore = Store::findOrFail($id);
        return response()->json([
            'message' => 'Store is updated!',
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
