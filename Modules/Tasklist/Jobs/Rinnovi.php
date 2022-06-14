<?php

namespace Modules\Tasklist\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Tasklist\Entities\RinnovoNotifica;

class Rinnovi implements ShouldQueue
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
        $rinnovo_types = config('tasklist.rinnovi.types');
        $types = config('tasklist.notifiche.types');
        $notifiche = RinnovoNotifica::with(['rinnovo' => function($q){
                                            $q->with('cliente');
                                        }])
                                    ->where('notifica', 'email')->get();

        if(!empty($notifiche) && count($notifiche) > 0)
        {
            foreach($notifiche as $key => $notifica)
            {
                $ctrl_send = false;

                // Tipo rinnovo
                $rinnovo_tipo = $rinnovo_types[$notifica->rinnovo->tipo];
                $data_sent = date($rinnovo_tipo, strtotime($notifica->sent_at));

                $ctr_rinnovo = ($data_sent == date($rinnovo_tipo) && !empty($notifica->sent_at)) ? false : true;

                if ($ctr_rinnovo)
                {
                    // Notifica
                    $rinnovo_datetime = set_date_hour_ita($notifica->rinnovo->data);
                    $cadenza = $notifica->cadenza;
                    $tipo = $types[$notifica->tipo];

                    $time = strtotime("$rinnovo_datetime - $cadenza $tipo");

                    if ($notifica->tipo < 2) // Minuti | Ore
                    {
                        $ora_notifica = strtotime(date("H:i", $time));
                        $current = strtotime(date("H:i"));
                        $ora_scadenza = strtotime(date("H:i", strtotime($rinnovo_datetime)));

                        if ($current >= $ora_notifica && $current <= $ora_scadenza)
                            $ctrl_send = true;

                    }
                    elseif ($notifica->tipo >= 2 && $notifica->tipo < 4) // Giorni | Mesi
                    {
                        $data = date("Y-m-d", $time);

                        if ($data == date("Y-m-d"))
                            $ctrl_send = true;
                    }
                    elseif ($notifica->tipo >= 5) // Anni
                    {
                        $data = date("y", $time);

                        if ($data == date("y"))
                            $ctrl_send = true;
                    }
                }

                if ($ctrl_send)
                {
                    $senders = $notifica->rinnovo
                                        ->utenti()
                                        ->pluck('email')
                                        ->toArray();
                    $oggetto = 'RINNOVO - ' . get_if_exist($notifica->rinnovo, 'titolo') . ' (' . get_if_exist($notifica->rinnovo->cliente, 'ragione_sociale') . ')';
                    $testo = '<h3>' . $notifica->rinnovo->titolo . '</h3>'
                    . 'Cliente: <strong>' . get_if_exist($notifica->rinnovo->cliente, 'ragione_sociale') . '</strong><br>'
                    . nl2br(get_if_exist($notifica->rinnovo, 'descrizione'))
                    . '<br><br> La data di scadenza per il rinnovo è fissata per il giorno '
                    . '<strong>' . get_if_exist($notifica->rinnovo, 'data') . '</strong>.';
                    if(!empty($notifica->rinnovo->ordinativo))
                        $testo .= '<br>Oggetto ordinativo: <strong>' . $notifica->rinnovo->ordinativo->oggetto . '<strong>';
                    $testo .= '<br><br> Puoi visualizzare i dettagli al seguente link: <a href="' . route('admin.tasklist.rinnovo.edit', $notifica->rinnovo->id) . '">' . route('admin.tasklist.rinnovo.edit', $notifica->rinnovo->id) . '</a>';

                    if(!mail_send($senders, $oggetto, $testo))
                    {
                        $notifica->sent_at = date('Y-m-d H:i:s');
                        $notifica->save();
                    }

                    $result['error'] = mail_send($senders, $oggetto, $testo);

                    $result['data'] = [
                        'senders' => $senders,
                        'oggetto' => $oggetto,
                        'testo' => $testo,
                    ];

                    // Log
                    activity($notifica->rinnovo->azienda)
                        ->performedOn($notifica->rinnovo)
                        ->withProperties($result)
                        ->log('schedule');
                }
            }
        }
    }
}
