<?php

namespace App\Console\Commands;

use App\Product;
use App\Trash;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all items in trash after 30 days';

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
        //COME BACK TO THIS
        // $now = Carbon::today();
        // $products = Product::where('deleted', true)->get();
        // foreach ($products as $key => $value) {
        //     $expired_date = \Carbon\Carbon::parse($value->updated_at)->addDays(30)->toDateTimeString();
        //     if ($now > $expired_date) {
        //         $value->delete();
        //     }
        // }
        // $this->info('Trash Item is Deleted');

    }
}
