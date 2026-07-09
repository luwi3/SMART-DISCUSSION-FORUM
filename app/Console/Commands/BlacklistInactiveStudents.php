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
 // Define the cutoff date (Yesterday at midnight)
$cutoff = today()->subDays(1)->toDateString();

// 1️⃣ Case A: Blacklist students who haven't communicated since before yesterday
Student::where('status', 'active')
    ->whereNotNull('lastCommDate')
    ->whereDate('lastCommDate', '<', $cutoff)
    ->update(['status' => 'blacklisted']);

// 2️⃣ Case B: Blacklist students who never communicated and registered over 5 days ago
Student::where('status', 'active')
    ->whereNull('lastCommDate')
    ->whereDate('created_at', '<', today()->subDays(5)->toDateString())
    ->update(['status' => 'blacklisted']);
}
}
