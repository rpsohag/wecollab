<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Tasklist\Entities\Attivita;
use Modules\Wecore\Entities\Meta;
use Modules\Wecore\Entities\Metagable;



class AttivitaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($attivita)
  {
    $this->attivita = $attivita;
  }

  public function headings(): array
  {
    return ['PRIORITA', 'CLIENTE', 'OGGETTO', 'ATTIVITA',  'COMPLETAMENTO', 'STATO', 'RICHIEDENTE', 'ULTIMA NOTA', 'DATA CREAZIONE', 'DATA INIZIO', 'DATA FINE'];
  }

  public function array(): array
  {
    $stati = [-1 => 'Tutte', -2 => 'Supervisionati'];
    $stati = $stati + config('tasklist.attivita.stati');
    $priorita = config('tasklist.attivita.priorita_testi');
    $row = 1;

    foreach($this->attivita as $key => $attivita)
    {

      $last_nota = !empty(json_decode($attivita->notes->last())) ? json_decode($attivita->notes->last())->value : '';

      $attivita_array[++$row] = [
        $priorita[$attivita->priorita],
        !empty($attivita->cliente->ragione_sociale) ? $attivita->cliente->ragione_sociale : '',
        $attivita->oggetto,
        !empty($attivita->gruppo) ? $attivita->gruppo->nome : '',
        $attivita->percentuale_completamento . '%',
        $stati[$attivita->stato],
        !empty($attivita->richiedente) ? $attivita->richiedente->full_name : '',
        $last_nota,
        get_date_hour_ita($attivita->created_at),
        $attivita->data_inizio,
        $attivita->data_fine
      ];
    }

    return $attivita_array;
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function(AfterSheet $event) {
        $cellRange = 'A1:W1'; // All headers
        $event->sheet->getDelegate()->getStyle($cellRange)
                                      ->getFont()
                                        ->setSize(14)
                                        ->setBold('100');
      },
    ];
  }
}