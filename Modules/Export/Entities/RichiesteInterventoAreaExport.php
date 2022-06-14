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



class RichiesteInterventoAreaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($dettaglio)
  {
    $this->dettaglio = $dettaglio;
  }

  public function headings(): array
  {
    return ['AREA', 'APERTI', 'CHIUSI', 'TOTALI', 'TEMPO DI LAVORAZIONE MEDIO (SECONDI)', 'TEMPO DI LAVORAZIONE MEDIO (FORMULA)', 'TEMPO DI LAVORAZIONE TOTALE (SECONDI)', 'TEMPO DI LAVORAZIONE TOTALE (FORMULA)', 'TEMPO DI RISOLUZIONE MEDIO (SECONDI)', 'TEMPO DI RISOLUZIONE MEDIO (FORMULA)', 'TEMPO DI RISOLUZIONE CON SOSPENSIONI MEDIO (SECONDI)', 'TEMPO DI RISOLUZIONE CON SOSPENSIONI MEDIO (FORMULA)' ];
  }

  public function array(): array
  {
    $dettaglio_array = [];
    $row = 1;

    foreach($this->dettaglio as $key => $area)
    {
      if(!empty($area['titolo']))
      {
        $dettaglio_array[++$row] = [
          $area['titolo'],
          $area['aperti'],
          $area['chiusi'],
          $area['tickets'],
          $area['tempo_lavorazione_media'],
          secondsToTime($area['tempo_lavorazione_media']),
          $area['tempo_lavorazione_totale'],
          secondsToTime($area['tempo_lavorazione_totale']),
          $area['tempo_risoluzione_media'],
          secondsToTime($area['tempo_risoluzione_media']),
          $area['tempo_risoluzione_con_sospensioni_media'],
          secondsToTime($area['tempo_risoluzione_con_sospensioni_media'])
        ];        
      }
    }

    return $dettaglio_array;
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
      },
    ];
  }
}
