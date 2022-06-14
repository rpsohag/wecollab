<?php

namespace Modules\Statistiche\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\Sentinel\User;
use Modules\Statistiche\Entities\ViewRichiesteIntervento;
use Modules\Statistiche\Entities\ViewRichiesteInterventoAzioni;
use Cache;

class CacheRichiesteIntervento implements ShouldQueue
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
        Cache::forget('richiesteintervento_statistiche');
        Cache::add('richiesteintervento_statistiche', ViewRichiesteIntervento::all() , 300);
        Cache::forget('richiesteintervento_statistiche_azioni');
        Cache::add('richiesteintervento_statistiche_azioni', ViewRichiesteInterventoAzioni::all() , 300);
    }
}
