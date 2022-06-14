<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\SegnalazioneOpportunita;


class SegnalazioneOpportunitaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($segnalazioniopportunita)
  {
    $this->segnalazioniopportunita = $segnalazioniopportunita;
  }

  public function headings(): array
  {
    return ['NUMERO SEGNALAZIONE', 'CLIENTE', 'OGGETTO', 'CREATA DA', 'DATA CREAZIONE', 'STATO'];
  }

  public function array(): array
  {
    $stati = config('commerciale.segnalazioneopportunita.stati');
    $row = 1;

    foreach($this->segnalazioniopportunita as $key => $segnalazione)
    {
      $segnalazioniopportunita_array[++$row] = [
        $segnalazione->numero(),
        optional($segnalazione->cliente())->ragione_sociale,
        $segnalazione->oggetto,
        get_if_exist($segnalazione->created_user, 'full_name'),
        get_if_exist($segnalazione, 'created_at'),
        $stati[$segnalazione->stato_id],
      ];
    }

    return $segnalazioniopportunita_array;
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