<?php

namespace Modules\Tasklist\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Tasklist\Entities\Timesheet;
use Modules\User\Entities\Sentinel\User;

class Timesheets implements ShouldQueue
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
        $result = [];
        $users_hour = [];
        $previous_week_start = date('Y-m-d', strtotime("-1 week")) . ' 00:00:00';
        $previous_week_end = date('Y-m-d', strtotime($previous_week_start . " +6 days")) . ' 23:59:59';
        $work_hours = working_time($previous_week_start, $previous_week_end, 'hours');

        $users = User::where('timesheets_report', 1)->get();

        foreach($users as $user)
        {
            $user_timesheets = $user->tempo_timesheets($user->id, null, null, $previous_week_start, $previous_week_end);
            $hours = round($user_timesheets->sum('diff') / 3600);

            if($hours < $work_hours)
                $users_hour[$user->id] = $hours;
        }

        if(!empty($users_hour))
        {
            $senders = ['edoardofederici@icloud.com', 'm.marras@we-com.it'];
            $oggetto = 'Riepilogo settimanale ore dipendenti - Timesheet dal ' . get_date_ita($previous_week_start) . ' al ' . get_date_ita($previous_week_end);
            $testo = 'Salve,<br>
                        di seguito il riepilogo degli utenti che non hanno inserito ' . $work_hours . ' ore nel proprio timesheet settimanale:<br><br>
                        <ul>';

            foreach($users_hour as $user_id => $hours)
                $testo .= '<li><strong>' . user($user_id)->full_name . '</strong> - ' . $hours . ' ore</li>';

            $testo .= '</ul>';

            $result['error'] = mail_send($senders, $oggetto, $testo);

            $result['data'] = [
                'senders' => $senders,
                'oggetto' => $oggetto,
                'testo' => $testo,
            ];

            // Log
            activity('We-COM')
                ->performedOn(new Timesheet())
                ->withProperties($result)
                ->log('schedule');
        }
    }
}
