<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class CalculateCustomerCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:credit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Customer Credit';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = Customer::all();

        foreach ($customers as $customer)
        {
            $customer->updateCreditBalance();
        }

        $this->info('Customer Credit Balance has been updated successfully');

        return Command::SUCCESS;
    }
}
