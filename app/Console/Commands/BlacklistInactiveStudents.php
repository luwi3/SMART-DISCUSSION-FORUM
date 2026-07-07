<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Student;

#[Signature('blacklist:inactive')]
#[Description('Command description')]
class BlacklistInactiveStudents extends Command
{
    /**
     * Execute the console command.
     */
 public function handle()
{
    $cutoff = \Carbon\Carbon::now()->subDays(5)->toDateTimeString();

    // 1️⃣ Case A: Blacklist students whose last communication is older than 5 days
    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->where('lastCommDate', '<', $cutoff)
        ->update(['status' => 'blacklisted']);

    // 2️⃣ Case B: Blacklist students who never communicated and registered over 5 days ago
    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->where('created_at', '<', $cutoff)
        ->update(['status' => 'blacklisted']);
        
    $this->info('Active student scan complete.');
}
}
