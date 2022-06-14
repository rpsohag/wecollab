<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Fatturazione;


class FatturazioneExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($fatture)
  {
    $this->fatture = $fatture;
  }

  public function headings(): array
  {
    return ['OGGETTO', 'CLIENTE', 'DATA', 'N. FATTURA', 'CIG', 'ACCONTO',  'NETTO', 'IVA', 'TOT FATTURA', 'DOVUTO', 'STATO', 'SCADUTA'];
  }

  public function array(): array
  {
    $fatture_array = [];
    $row = 1;

    foreach($this->fatture as $key => $fattura)
    {
      $fatture_array[++$row] = [
        $fattura->oggetto,
        $fattura->cliente->ragione_sociale,
        $fattura->data,
        $fattura->get_numero_fattura(),
        $fattura->cig,
        $fattura->acconto,
        $fattura->totale_netto,
        $fattura->iva,
        $fattura->totale_fattura,
        get_currency(clean_currency($fattura->totale_fattura) - clean_currency($fattura->acconto)),
        $fattura->stato(),
        ($fattura->scaduta() ? 'SI' : 'NO')
      ];
    }

    return $fatture_array;
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
