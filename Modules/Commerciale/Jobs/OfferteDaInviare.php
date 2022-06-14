<?php

namespace Modules\Commerciale\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Modules\Commerciale\Entities\Offerte;
use Modules\Tasklist\Entities\Attivita;

class OfferteDaInviare implements ShouldQueue
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
        $offerte = Offerta::where('stato', 5)->get();

        if (!empty($offerte)) {
            foreach ($offerte as $key => $offerta) {
                $offerta_log = get_activities($offerta)->where('properties.stato', 5)->last();
                $ctrl_date = $offerta_log->updated_at->addDays(15)->toDateString();

                if ($ctrl_date == date('Y-m-d')) {
                    // Cambia stato da inviare => non risposta
                    $offerta->stato = 8;
                    $offerta->save();

                    // Log
                    activity($offerta->azienda)
                        ->performedOn($offerta)
                        ->withProperties(['stato' => $offerta->stato])
                        ->log('updated');

                    // Attività
                    $insert = [
                        'azienda' => $offerta->azienda,
                        'oggetto' => 'OFFERTA NON RISPOSTA - ' . $offerta->oggetto,
                        'cliente_id' => $offerta->cliente_id,
                        'categoria' => 'Segnalazione Commerciale',
                        'richiedente_id' => settings('admin::direttore_commerciale'),
                        'priorita' => 8,
                        'durata_tipo' => 0,
                        'durata_valore' => 0,
                        'stato' => 0,
                    ];

                    $attivita = Attivita::create($insert);

                    $assegnatari_ids = [$offerta->analisi_vendita->commerciale->id];
                    $attivita->users()->sync($assegnatari_ids);

                    $attivita->attivitable()->associate($offerta)->save();

                    // Log
                    activity($attivita->azienda)
                        ->performedOn($attivita)
                        ->withProperties($insert + ['assegnatari_ids' => $assegnatari_ids])
                        ->log('created');

                    // Email
                    $senders = $offerta->analisi_vendita->commerciale()->pluck('email')->toArray();

                    $oggetto = 'OFFERTA NON RISPOSTA - ' . $offerta->oggetto;
                    $testo = 'Salve,<br>l\'offerta in oggetto non ha ricevuto risposta.'
                    . '<br><br>Puoi visualizzare il dettaglio al seguente link:'
                    . '<br><a href="' . route('admin.commerciale.offerta.edit', $offerta->id) . '">' . route('admin.commerciale.offerta.edit', $offerta->id) . '</a>'
                    . '<br><br>Inoltre ti è stata assegnata un\'attività visibile all\'indirizzo:'
                    . '<br><a href="' . route('admin.tasklist.attivita.edit', $attivita->id) . '">' . route('admin.tasklist.attivita.edit', $attivita->id) . '</a>';

                    $result['error'] = mail_send($senders, $oggetto, $testo);

                    // Log
                    $result['data'] = [
                        'offerta' => [
                            'stato' => 8,
                        ],
                        'attivita' => [
                            $insert,
                            'assegnatari_ids' => $assegnatari_ids,
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
