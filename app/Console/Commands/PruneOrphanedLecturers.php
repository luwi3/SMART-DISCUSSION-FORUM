<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;
use App\Models\Lecturer;
use App\Models\Quiz;
use App\Models\Resource;

#[Signature('lecturers:prune-orphaned {--force : Actually delete the rows instead of just reporting them}')]
#[Description('Remove lecturers rows that were auto-created for a user whose role is not lecturer (see the firstOrCreate bug in QuizController/ResourceController)')]
class PruneOrphanedLecturers extends Command
{
    public function handle(): int
    {
        $orphans = Lecturer::whereHas('user', function ($query) {
            $query->where('role', '!=', 'lecturer');
        })->orWhereDoesntHave('user')->get();

        if ($orphans->isEmpty()) {
            $this->info('No orphaned lecturer rows found.');
            return self::SUCCESS;
        }

        $this->warn("Found {$orphans->count()} lecturer row(s) belonging to a non-lecturer user:");

        foreach ($orphans as $lecturer) {
            $user = $lecturer->user;
            $label = $user ? "{$user->name} (user_id={$user->id}, role={$user->role})" : 'no linked user';
            $this->line(" - staffNo={$lecturer->staffNo}: {$label}");

            $quizCount = Quiz::where('staffNo', $lecturer->staffNo)->count();
            $resourceCount = Resource::where('staffNo', $lecturer->staffNo)->count();

            if ($quizCount > 0 || $resourceCount > 0) {
                $this->error("   ⚠ this staffNo has {$quizCount} quiz(zes) and {$resourceCount} resource(s) attached — review manually before deleting, they will NOT be removed by this command");
            }
        }

        if (! $this->option('force')) {
            $this->comment('Dry run only — rerun with --force to delete the lecturer rows listed above.');
            return self::SUCCESS;
        }

        foreach ($orphans as $lecturer) {
            $lecturer->delete();
        }

        $this->info("Deleted {$orphans->count()} orphaned lecturer row(s).");
        return self::SUCCESS;
    }
}
