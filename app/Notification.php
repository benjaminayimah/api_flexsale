<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    // public function insertNotifications($prod_units, $store, $noti_key, $unit) {
    //     foreach ($prod_units as $key => $value) {
    //         $batch_no = null;
    //         $unit_id = null;
    //         $expiry_date = null;
    //         $prod_id = $value->id;
    //         $make_unique = 'product_id';
    //         if ($unit == true) {
    //             $prod_id = $value->product_id;
    //             $unit_id = $value->id;
    //             $expiry_date = $value->expiry_date;
    //             $batch_no = $value->batch_no;
    //             $make_unique = 'unit_id';
    //         }
    //         $old_noti = Store::find($store)->getNotifications()
    //         ->where([
    //             [$make_unique, '=', $value->id],
    //             ['key', '=', $noti_key]
    //         ])->first();
    //         if(!isset($old_noti)){
    //             DB::table('notifications')->insert([
    //                 'product_id' => $prod_id,
    //                 'batch_no' => $batch_no,
    //                 'unit_id' => $unit_id,
    //                 'store_id' => $store,
    //                 'key' => $noti_key,
    //                 'expiry_date' => $expiry_date,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]);
                
    //         }
    //     }
    //     return;

    // }
}
