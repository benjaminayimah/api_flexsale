<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Unit;

class StockAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks product expiration date for all users';

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
        $now = Carbon::today();
        $products = Unit::all();
        foreach ($products as $key) {
            if($now->gt($key->expiry_date) ) {
                $key->active = 0;
                $key->update();
            }
        }
        $this->info('Stoct Alerts has been set');
    }
}
