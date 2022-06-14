<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AnalisiVenditaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($riepilogo)
  {
    $this->riepilogo = $riepilogo;
  }

  public function headings(): array
  {
    return ['AREA', 'ATTIVITA', 'RISORSA', 'ORE REMOTO', 'ORE CLIENTE', 'ORE CONFIGURAZIONE',  'ORE FORMAZIONE REMOTO', 'ORE FORMAZIONE CLIENTE'];
  }

  public function array(): array
  {
    $riepilogo_array = [];
    $row = 1;

    foreach($this->riepilogo as $riep)
    {
      $riepilogo_array[++$row] = [
        $riep['area_titolo'],
        $riep['gruppo_nome'],
        $riep['risorsa_nome'],
        $riep['ore_remoto'],
        $riep['ore_cliente'],
        $riep['ore_configurazione'],
        $riep['ore_formazione_remoto'],
        $riep['ore_formazione_cliente'],
      ];
    }

    return $riepilogo_array;
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
