<?php

namespace Modules\Wecore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class Holidays implements ShouldQueue
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
        $holidayDays = [];

        $apiKey = 'AIzaSyDODnZGHn2cC0zqr358vubG-mdH4TUYuyw';
        //$from_api = date('Y-m-d', strtotime($from)).'T00:00:00-00:00';
        //$to_api = date('Y-m-d', strtotime($to)).'T00:00:00-00:00';

        $api_url = "https://content.googleapis.com/calendar/v3/calendars/it.italian%23holiday%40group.v.calendar.google.com/events".
               '?singleEvents=false'.
               //"&timeMax={$to_api}".
               //"&timeMin={$from_api}".
               "&key={$apiKey}";

        $response = json_decode(curl_get_contents($api_url));

        if (isset($response->items)) {
            foreach ($response->items as $holiday) {
                $holidayDays[] = $holiday->start->date;
            }
            sort($holidayDays);
        }

        config(['holidays.holidays' => $holidayDays]);
        $text = '<?php return ' . var_export(config('holidays'), true) . ';';
        file_put_contents(config_path('holidays.php'), $text);

    }

}
