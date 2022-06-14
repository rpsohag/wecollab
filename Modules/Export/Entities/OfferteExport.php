<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Offerta;


class OfferteExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($offerte)
  {
    $this->offerte = $offerte;
  }

  public function headings(): array
  {
    return ['NUMERO OFFERTA', 'DATA', 'IMPORTO', 'CLIENTE', 'OGGETTO', 'STATO',  'FATTURATA', 'ORDINATIVO', 'COMMERCIALE'];
  }

  public function array(): array
  {
    $stati = config('commerciale.offerte.stati');
    $sn = config('wecore.sn');
    $row = 1;

    foreach($this->offerte as $key => $offerta)
    {
      $offerte_array[++$row] = [
        $offerta->numero_offerta(),
        $offerta->data_offerta,
        $offerta->importo_iva,
        !empty($offerta->cliente->ragione_sociale) ? $offerta->cliente->ragione_sociale : '',
        $offerta->oggetto,
        $stati[$offerta->stato],
        $sn[$offerta->fatturata()],
        (!empty($offerta->ordinativo) ? $offerta->ordinativo->oggetto : ''),
        optional(optional(optional($offerta->analisi_vendita()->first())->commerciale())->first())->full_name,
      ];
    }

    return $offerte_array;
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