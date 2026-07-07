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
        $cutoff = now()->subMinutes(2);

$updatedCount = Student::where('status', 'active')
    ->where(function ($query) use ($cutoff) {
        $query->where('lastCommDate', '<', $cutoff)
              ->orWhere(function ($subQuery) use ($cutoff) {
                  $subQuery->whereNull('lastCommDate')
                           ->where('created_at', '<', $cutoff);
              });
    })
    ->update(['status' => 'blacklisted']);
    }
}
