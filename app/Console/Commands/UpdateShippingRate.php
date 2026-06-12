<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShippingRate;

class UpdateShippingRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-shipping-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update shipping rates price to random value between 1000 and 1400';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating shipping rates...');
        
        $rates = ShippingRate::all();
        $bar = $this->output->createProgressBar(count($rates));

        $bar->start();

        foreach ($rates as $rate) {
            $newPrice = 1200;
            $rate->update(['price' => $newPrice]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All shipping rates updated successfully.');

        return Command::SUCCESS;
    }
}
