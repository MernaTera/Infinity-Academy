<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeadService;

class ReleaseExpiredLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release leads that exceeded 4 days without update';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(LeadService::class)->releaseExpiredLeads();

        $this->info('Expired leads released successfully.');

        return Command::SUCCESS;
    }
}
