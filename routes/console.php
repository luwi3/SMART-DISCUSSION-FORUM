<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Student;
use Illuminate\Support\Facades\Schedule;

// Run the blacklisting logic automatically every day at midnight 🕛
Schedule::call(function () {
    $blacklistCutoff = today()->subDays(3)->toDateString(); 
    $warningCutoff = today()->subDays(2)->toDateString();   

    // ⚠️ Warnings
    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $warningCutoff)
        ->whereDate('lastCommDate', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->whereDate('created_at', '<', $warningCutoff)
        ->whereDate('created_at', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    // 🔴 Blacklists
    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $blacklistCutoff)
        ->update(['status' => 'blacklisted']);

    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->whereDate('created_at', '<', $blacklistCutoff)
        ->update(['status' => 'blacklisted']);
})->daily();