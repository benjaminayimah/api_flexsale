<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\Store;
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
        $store_id = $user->current;
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
            $supplier->store_id = $store_id;
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
    public function fetchThisSupplier(Request $request) {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $store_id = $user->current;
        try {
            $suppliers = Store::find($store_id)->getSuppliers()
            ->where('id', $request['id'])
            ->first();
            $products = Store::find($store_id)->getProducts()
            ->where([
                ['deleted', '=', false ],
                ['supplier_id', '=', $request['id']]
            ])->get();
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!'
            ], 500);
        }
        return response()->json([
            'supplier' => $suppliers,
            'products' => $products
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
            $products = Store::find($user->current)->getProducts()
            ->where([
                ['deleted', '=', false ],
                ['supplier_id', '=', $id]
            ])->get();
            if(count($products) > 0) {
                foreach ($products as $key) {
                    $key->supplier_id = null;
                    $key->update();
                }
            }
        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json([
            'status' => 'Supplier is deleted.',
            'id' => $id
        ], 200);
    }
}
