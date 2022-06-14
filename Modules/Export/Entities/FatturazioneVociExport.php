<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Fatturazione;


class FatturazioneVociExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($fatture_voci)
  {
    $this->fatture = $fatture_voci;
  }

  public function headings(): array
  {
    return ['CLIENTE', 'OGGETTO FATTURA', 'DESCRIZIONE', 'QUANTITA', 'IMPORTO SINGOLO', 'IVA', 'IMPORTO', 'IMPORTO IVA', 'ATTIVITA', 'N. R. FATTURA', 'DATA FATTURA'];
  }

  public function array(): array
  {
    $fatture_array = [];
    $row = 1;

    foreach($this->fatture as $key => $fattura)
    {
      $fatture_array[++$row] = [
        $fattura->fattura->cliente->ragione_sociale,
        $fattura->fattura->oggetto,
        $fattura->descrizione,
        $fattura->quantita,
        $fattura->importo_singolo,
        $fattura->iva,
        $fattura->importo,
        $fattura->importo_iva,
        $fattura->attivita_svolta,
        $fattura->fattura->get_numero_fattura(),
        get_date_ita($fattura->fattura->created_at)
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
