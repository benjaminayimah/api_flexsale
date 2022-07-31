<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class statsController extends Controller
{
    public function index()
    {
        //
    }
    public function store(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $type = $request['type'];
        $interval = $request['interval'];
        $prodID = $request['id'];
        $result = array();
        $start_date = '';
        $end_date = '';
        $store_id = $user->current;
        //to today interval
        $start_date = Carbon::today()->subDays($interval)->toDateTimeString();
        $end_date = Carbon::today()->addDays(1)->toDateTimeString();
        
        //inbetween range
        if($type == 2) {
            $start_date = \Carbon\Carbon::parse($request['start'])->toDateTimeString();
            $end_date = \Carbon\Carbon::parse($request['end'])->addDays(1)->toDateTimeString();
        }
        $result = Store::find($store_id)->getSalesItem()
            ->where('product_id', $prodID)
            ->whereBetween('created_at',[
            $start_date, $end_date
            ])
        ->get();
        $end_date = \Carbon\Carbon::parse($end_date)->subDays(1)->toDateTimeString();
        if($start_date == $end_date) {
            $start_date = '';
        }
        return response()->json([
            'stats' => $result,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $type
        ], 200);
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }
}
