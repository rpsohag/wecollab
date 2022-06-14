<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Commerciale\Jobs\OfferteDaInviare;
use Modules\Commerciale\Jobs\OfferteRifiutate;
use Modules\Commerciale\Jobs\ScadenzeFatturazioni;
use Modules\Tasklist\Jobs\Rinnovi;
use Modules\Tasklist\Jobs\Timesheets;
use Modules\Assistenza\Jobs\TicketByEmail;
use Modules\Statistiche\Jobs\WeeklyReport;
use Modules\Statistiche\Jobs\CacheRichiesteIntervento;
use Modules\Wecore\Jobs\Holidays;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();

        // Offerte non risposte => rifiutata
        $schedule->job(new OfferteRifiutate)->daily();
        
        // Offerte da inviare => non risposte
        $schedule->job(new OfferteDaInviare)->daily();

        // Scadenze fatturazioni
        $schedule->job(new ScadenzeFatturazioni)->daily();

        // Report Settimanale
        $schedule->job(new WeeklyReport)->weekly();

        // Rinnovi
        $schedule->job(new Rinnovi)->everyMinute();

        // Timesheet
        $schedule->job(new Timesheets)->weeklyOn(1, '9:00');

        // Holidays
        $schedule->job(new Holidays)->weeklyOn(1, '9:00');

        // Cache Richieste Intervento
        $schedule->job(new CacheRichiesteIntervento)->everyFiveMinutes();

        // Controllo delle Email
        //$schedule->job(new TicketByEmail)->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
