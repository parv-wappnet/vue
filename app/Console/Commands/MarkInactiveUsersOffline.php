<?php

namespace App\Console\Commands;

use App\Events\UserStatusChanged;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkInactiveUsersOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:mark-offline {--minutes=5 : Minutes of inactivity before marking as offline}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark users as offline after a period of inactivity';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = $this->option('minutes');
        $cutoffTime = Carbon::now()->subMinutes($minutes);

        $inactiveUsers = User::where('status', 'online')
            ->where('last_seen_at', '<', $cutoffTime)
            ->get();

        $count = 0;
        foreach ($inactiveUsers as $user) {
            $user->update(['status' => 'offline']);
            broadcast(new UserStatusChanged($user, 'offline'));
            $count++;
        }

        $this->info("Marked {$count} users as offline after {$minutes} minutes of inactivity.");

        return Command::SUCCESS;
    }
}
