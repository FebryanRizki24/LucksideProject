<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use App\Models\OperationalHour;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheclHolidayAndUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holiday:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek apakah hari ini libur dan update is_open';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $isHoliday = Holiday::where('date', $today)->exists();

        OperationalHour::query()->update([
            'is_open' => !$isHoliday
        ]);

        $this->info('Operational status updated. is_open = ' . (!$isHoliday ? 'true' : 'false'));
    }
}
