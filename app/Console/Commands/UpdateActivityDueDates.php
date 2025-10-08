<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;

class UpdateActivityDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:update-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing activities to have due dates based on created_at + 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating activities with due dates...');
        
        // Get all activities that don't have a due date set
        $activities = Activity::all();
        
        $this->info("Found {$activities->count()} activities without due dates.");
        
        $updated = 0;
        
        foreach ($activities as $activity) {
            // Set due date to created_at - 7 days
        $dueDate = $activity->created_at->copy()->subDays(7);


            $activity->update(['due_date' => $dueDate]);
            
            $this->line("Updated Activity ID {$activity->id}: '{$activity->title}' - Due: {$dueDate->format('M j, Y g:i A')}");
            $updated++;
        }
        
        $this->info("Successfully updated {$updated} activities with due dates.");
        
        return Command::SUCCESS;
    }
}
