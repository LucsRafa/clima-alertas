<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWeatherAlert;
use App\Models\Alert;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class DispatchAlerts extends Command
{
    /** @var string */
    protected $signature = 'alerts:dispatch';

    /** @var string */
    protected $description = 'Dispatch weather alerts scheduled for now or earlier';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = CarbonImmutable::now();

        $ids = Alert::query()
            ->where('active', true)
            ->whereNull('dispatched_at')
            ->where('notify_at', '<=', $now)
            ->orderBy('id')
            ->limit(500)
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('Dispatched 0 alerts');
            return self::SUCCESS;
        }

        // Mark as dispatched to prevent duplicate enqueues if the command runs again
        Alert::whereIn('id', $ids)->update(['dispatched_at' => $now->toDateTimeString()]);

        foreach ($ids as $id) {
            ProcessWeatherAlert::dispatch($id);
        }

        $this->info(sprintf('Dispatched %d alerts', $ids->count()));

        return self::SUCCESS;
    }
}
