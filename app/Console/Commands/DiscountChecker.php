<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Discount;
use Carbon\Carbon;

class DiscountChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discount:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks discount expiration date for all users';

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
        $today = Carbon::today();
        $discounts = Discount::all();
        foreach ($discounts as $key) {
            $discount = Discount::find($key->id);
            if($today >= $key->start && $today <= $key->end ) {
                $discount->active = '1';
            }elseif($today > $key->end) {
                $discount->active = '0';
            }else{
                $discount->active = '2';
            }
            $discount->update();
        }
        $this->info('Discount Expiration Checked');
    }
}
