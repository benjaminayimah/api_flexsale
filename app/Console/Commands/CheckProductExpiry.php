<?php

namespace App\Console\Commands;

use App\Notification;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckProductExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkExpiry:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks for expired products as well as products expiring in 30 days time period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $today = Carbon::today();
        // $key = 'expired';
        // $store = Unit::all()->where('expiry_date', '<', $today);
        // foreach ($store as $key => $value) {
        //     $old_noti = Notification::all()
        //     ->where([
        //         ['unit_id', '=', $value->id],
        //         ['key', '=', $key]
        //     ])->first();
            
            
        // }

        
        // $expired_prods = Store::find($store)->getProductUnits()
        // ->where('expiry_date', '<', $today)
        // ->get();
        // if (count($expired_prods) > 0) {
        //     $noti_key = 'expired';
        //     $unit = true;
        //     $notification->insertNotifications($expired_prods, $store, $noti_key, $unit);
        // }
        // //expiring soon
        // $expiry_limit = 30;
        // $today_plus_30 = \Carbon\Carbon::parse()->addDays($expiry_limit);
        // $expiring_soon = Store::find($store)->getProductUnits()
        // ->where([
        //     ['expiry_date', '>', $today],
        //     ['expiry_date', '<', $today_plus_30]
        //     ])
        // ->get();
        // if (count($expiring_soon) > 0) {
        //     $noti_key = 'expiring-soon';
        //     $unit = true;
        //     $notification->insertNotifications($expiring_soon, $store, $noti_key, $unit);
        // }
    }
}
