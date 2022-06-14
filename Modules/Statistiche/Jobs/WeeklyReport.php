<?php

namespace Modules\Statistiche\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\Sentinel\User;

class WeeklyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = settings('statistiche::reports_responsabile');
        $user = User::find($email);
        $email = $user->email;
        $oggetto = 'REPORT SETTIMANALE';
        $testo = 'Puoi visualizzare il report settimanale al seguente link: <a href="' . route('admin.statistiche.reports.index') . '">';

        mail_send($email, $oggetto, $testo);
    }
}
