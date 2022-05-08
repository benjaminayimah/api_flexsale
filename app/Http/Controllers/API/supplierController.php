<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Supplier;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class supplierController extends Controller
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

    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'name' => 'required'
        ]);
        try {
            $userAdminID = $user->id;
            if($user->role != 1) {
                $userAdminID = $user->admin_id;
            }
            $supplier = New Supplier();
            $supplier->name = $request['name'];
            $supplier->phone = $request['phone'];
            $supplier->email = $request['email'];
            $supplier->location = $request['location'];
            $supplier->user_id = $userAdminID;
            $supplier->store_id = $user->current;
            $supplier->save();

            } catch (\Throwable $th) {
                return response()->json([
                    'title' => 'Error!'
                ], 500);
            }
            return response()->json([
                'message' => 'Supplier created!',
                'supplier' => $supplier
            ], 200);
    }

    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'name' => 'required'
        ]);
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->name = $request['name'];
            $supplier->phone = $request['phone'];
            $supplier->email = $request['email'];
            $supplier->location = $request['location'];
            $supplier->update();

            } catch (\Throwable $th) {
                return response()->json([
                    'title' => 'Error!'
                ], 500);
            }
            return response()->json([
                'message' => 'Supplier is updated!',
                'supplier' => $supplier
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
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try{
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'Supplier is deleted.',
            'id' => $id
        ], 200);
    }
}
