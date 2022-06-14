<?php

namespace Modules\Commerciale\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Commerciale\Entities\FatturazioneScadenze;

class ScadenzeFatturazioni implements ShouldQueue
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
        $fatturazioni = FatturazioneScadenze::where('data_avviso', date('Y-m-d'))->get();

        if (!empty($fatturazioni)) {
            foreach ($fatturazioni as $key => $ft) {
                $senders = json_decode(setting('commerciale::fatturazione::scadenze_notifica'));

                $oggetto = 'SCADENZA FATTURAZIONE - ' . $ft->ordinativo->oggetto;
                $testo = '<h3>' . $ft->ordinativo->oggetto . '</h3>'
                . '<br>Descrizione: <strong>' . nl2br($ft->descrizione) . '</strong>'
                . '<br>IMPORTO: <strong>' . $ft->importo . '</strong>'
                . '<br><br><br>La data fissata per la fatturazione è per il giorno '
                . '<strong>' . $ft->data . '<strong>'
                . '<br><br>Puoi visualizzare i dettagli al seguente link: <a href="' . route('admin.commerciale.ordinativo.edit', $ft->ordinativo_id) . '">' . route('admin.commerciale.ordinativo.edit', $ft->ordinativo_id) . '</a>';

                $result['error'] = mail_send($senders, $oggetto, $testo);

                $result['data'] = [
                    'senders' => $senders,
                    'oggetto' => $oggetto,
                    'testo' => $testo,
                ];

                // Log
                activity($ft->ordinativo->azienda)
                    ->performedOn($ft)
                    ->withProperties($result)
                    ->log('schedule');
            }
        }
    }
}
