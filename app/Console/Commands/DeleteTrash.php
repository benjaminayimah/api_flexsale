<?php

namespace App\Console\Commands;

use App\Product;
use App\Store;
use App\Trash;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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
        $now = Carbon::today();
        $products = Product::where('deleted', true)->get();
        foreach ($products as $key => $value) {
            $expired_date = \Carbon\Carbon::parse($value->updated_at)->addDays(30)->toDateTimeString();
            if ($now > $expired_date) {
                $id = $value->id;
                $store_id = $value->store_id;
                $userAdminID = Store::findOrFail($store_id)->user_id;
                $tagItems = Store::find($store_id)->getFilters()
                ->where('product_id', $id)->get();
                if(count($tagItems) > 0) {
                    foreach($tagItems as $item) {
                        $item->delete();
                    }
                }
                $units = Product::find($id)->getUnits;
                if(count($units) > 0) {
                    foreach($units as $unit) {
                        $unit->delete();
                    }
                }
                $image = $value->image;
                if (Storage::disk('public')->exists($userAdminID.'/'.$store_id.'/'.$image)) {
                    Storage::disk('public')->delete($userAdminID.'/'.$store_id.'/'.$image);
                }
                $value->delete();
            }
        }
        $this->info('Trash Item is Deleted');

    }
}
