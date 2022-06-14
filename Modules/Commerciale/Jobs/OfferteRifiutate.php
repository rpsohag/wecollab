<?php

namespace Modules\Commerciale\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Modules\Commerciale\Entities\Offerte;

class OfferteRifiutate implements ShouldQueue
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
        $offerte = Offerta::where('stato', 8)->get();

        if (!empty($offerte)) {
            foreach ($offerte as $key => $offerta) {
                $offerta_log = get_activities($offerta)->where('properties.stato', 8)->last();
                $ctrl_date = $offerta_log->updated_at->addDays(15)->toDateString();

                if ($ctrl_date == date('Y-m-d')) {
                    // Cambia stato non risposta => rifiutata
                    $offerta->stato = 2;
                    $offerta->save();

                    // Log
                    activity($offerta->azienda)
                        ->performedOn($offerta)
                        ->withProperties(['stato' => $offerta->stato])
                        ->log('updated');

                    // Email
                    $senders = $offerta->analisi_vendita->commerciale()->pluck('email')->toArray() + setting('admin::direttore_commerciale');

                    $oggetto = 'OFFERTA RIFIUTATA - ' . $offerta->oggetto . ' - ' . $offerta->cliente->ragione_sociale;
                    $testo = 'Salve,<br>l\'offerta in oggetto è stata rifiutata perchè sono trascorsi 30 giorni dall\'invio e non è stata ricevuta risposta.'
                    . '<br><br>Puoi visualizzare il dettaglio al seguente link:'
                    . '<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>';

                    // $result['error'] = mail_send($senders, $oggetto, $testo, null, $offerta->azienda);
                    $result['error'] = mail_send($senders, $oggetto, $testo);

                    // Log
                    $result['data'] = [
                        'offerta' => [
                            'stato' => 2,
                        ],
                        'email' => [
                            'senders' => $senders,
                            'oggetto' => $oggetto,
                            'testo' => $testo,
                        ],
                    ];

                    activity($offerta->azienda)
                        ->performedOn($offerta)
                        ->withProperties($result)
                        ->log('schedule');
                }
            }
        }
    }
}
