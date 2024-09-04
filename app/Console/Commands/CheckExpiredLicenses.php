<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\License;
use Carbon\Carbon;

class CheckExpiredLicenses extends Command
{
    protected $signature = 'licenses:check-expired';
    protected $description = 'Check for expired licenses and update their status';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $licenses = License::where('license_expiration_date', '<', Carbon::now())
                           ->where('status', 'active')
                           ->get();

        foreach ($licenses as $license) {
            $license->update(['status' => 'expired']);
        }

        $this->info('Checked and updated expired licenses.');
    }
}