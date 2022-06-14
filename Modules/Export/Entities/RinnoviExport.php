<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Tasklist\Entities\Rinnovo;


class RinnoviExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($rinnovi)
  {
    $this->rinnovi = $rinnovi;
  }

  public function headings(): array
  {
    return ['TITOLO', 'CLIENTE', 'DESCRIZIONE', 'DATA RINNOVO'];
  }

  public function array(): array
  {
    $row = 1;

    foreach($this->rinnovi as $key => $rinnovo)
    {
      $rinnovi_array[++$row] = [
        $rinnovo->titolo,
        $rinnovo->cliente()->first()->ragione_sociale,
        $rinnovo->descrizione,
        $rinnovo->getDataRinnovo()
      ];
    }

    return $rinnovi_array;
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