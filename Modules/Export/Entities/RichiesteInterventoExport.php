<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Fatturazione;
use Modules\Amministrazione\Entities\ClienteIndirizzi;
use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Assistenza\Entities\RichiesteInterventoAzione;
use Carbon\Carbon;



class RichiesteInterventoExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($richieste)
  {
    $this->richieste = $richieste;
  }

  public function headings(): array
  {
    return ['CODICE', 'CLIENTE', 'FASCIA ABITANTI', 'NUMERO DIPENDENTI', 'INDIRIZZO', 'AREA DI INTERVENTO', 'RICHIEDENTE', 'NUMERO DA RICHIAMARE', 'DATA APERTURA',  'OGGETTO', 'DESCRIZIONE', 'ASSEGNATARI', 'ULTIMA AZIONE', 'TEMPO DI RISOLUZIONE (SECONDI)', 'TEMPO DI RISOLUZIONE (FORMULA H:M:S)', 'TEMPO DI LAVORAZIONE (SECONDI)', 'TEMPO DI LAVORAZIONE (FORMULA H:M:S)', 'DATA CHIUSURA', 'CHIUSO DA'];
  }

  public function array(): array
  {
    $richieste_array = [];
    $row = 1;
    $totali = array();
    $totali['tempo_di_lavorazione'] = 0;

    foreach($this->richieste as $key => $richiesta)
    {
        if(!empty($richiesta->indirizzo)){
           $indirizzo = (!empty($richiesta->indirizzo->denominazione) ? $richiesta->indirizzo->denominazione . ' | ' : '') . $richiesta->indirizzo->indirizzo . ', ' . $richiesta->indirizzo->cap . ' ' . $richiesta->indirizzo->citta . ' (' . $richiesta->indirizzo->provincia . ')';
        } else {
          $indirizzo = '';
        }

        // tempo di lavorazione totale dell'export
        $totali['tempo_di_lavorazione'] += !empty($richiesta->statistiche) ? $richiesta->statistiche['tempo_lavorazione_totale'] : 0;

        if($richiesta->get_stato_integer() == 3){
          $azione = $richiesta->azioni->last();
          $ultima_azione = $azione->descrizione;
          $chiuso_da = (!empty($azione->created_user) ? $azione->created_user->full_name : ' ');
          $data_chiusura = $azione->updated_at;
          $data_apertura = $richiesta->created_at;

          // Tempo di risoluzione della richiesta in secondi
          $tempo_di_risoluzione_secondi = !empty($richiesta->statistiche) ? $richiesta->statistiche['tempo_risoluzione'] : '';

          // Tempo di risoluzione della richiesta in formula
          $tempo_di_risoluzione_formula = !empty($richiesta->statistiche) ? secondsToTime($richiesta->statistiche['tempo_risoluzione']) : '';

        } else {
            $ultima_azione = '';
            $tempo_di_risoluzione_secondi = 0;
            $tempo_di_risoluzione_formula = '';
            $data_chiusura = '';
            $chiuso_da = '';
        }

        // Tempo di lavorazione della richiesta in secondi
        $tempo_di_lavorazione_secondi = !empty($richiesta->statistiche) ? $richiesta->statistiche['tempo_lavorazione_totale'] : '';

        // Tempo di lavorazione della richiesta in formula
        $tempo_di_lavorazione_formula = !empty($richiesta->statistiche) ? secondsToTime($richiesta->statistiche['tempo_lavorazione_totale']) : '';        

        $richieste_array[++$row] = [
            $richiesta->codice,
            optional($richiesta->cliente)->ragione_sociale,
            optional(optional(optional($richiesta->cliente)->censimento())->first())->fascia_abitanti(),
            optional(optional(optional($richiesta->cliente)->censimento())->first())->numero_dipendenti,
            $indirizzo,
            $richiesta->area->titolo,
            $richiesta->richiedente,
            $richiesta->numero_da_richiamare,
            get_date_hour_ita($richiesta->created_at),
            $richiesta->oggetto,
            preg_replace('/[^a-zA-Z0-9_ -]/s','',$richiesta->descrizione_richiesta),
            $richiesta->destinatari()->get()->pluck('full_name', 'full_name')->implode(', '),
            $ultima_azione,
            $tempo_di_risoluzione_secondi,
            $tempo_di_risoluzione_formula,
            $tempo_di_lavorazione_secondi,
            $tempo_di_lavorazione_formula,
            $data_chiusura,
            $chiuso_da,
        ];
    }
    $richieste_array[++$row] = [
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      '',
      !empty($totali['tempo_di_lavorazione']) ? get_seconds_to_hours($totali['tempo_di_lavorazione']) . ' (Ore)' : '',
      !empty($totali['tempo_di_lavorazione']) ? secondsToTime($totali['tempo_di_lavorazione']) : '',   
      '',
      '',   
    ];

    return $richieste_array;
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function(AfterSheet $event) {
        $cellRange = 'A1:W1';
        $event->sheet->getDelegate()->getStyle($cellRange)
                                      ->getFont()
                                        ->setSize(14)
                                        ->setBold('100');
        $event->sheet->getStyle('L')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);
        $event->sheet->getStyle('N')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);
      },
    ];
  }
}
