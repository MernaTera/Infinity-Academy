<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckRestrictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-restrictions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() 
    { 
        app(\App\Services\RestrictionService::class)->checkAll(); 
        $this->info('Restrictions checked successfully'); 
    }
}
