<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Offerta;
use Modules\Commerciale\Entities\FatturazioneScadenze;


class FatturazioneScadenzeExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($offerte)
  {
    $this->offerte = $offerte;
  }

  public function headings(): array
  {
    return ['COMMERCIALE', 'CLIENTE', 'DESCRIZIONE', 'IMPORTO', 'N. ORDINATIVO', 'N. OFFERTA', 'DESCRIZIONE OFFERTA', 'IMPORTO IVA OFFERTA', 'N. FATTURA', 'DESCRIZIONE FATTURA', 'IMPORTO FATTURA', 'DATA', 'DATA AVVISO'];
  }

  public function array(): array
  {
    $stati = config('commerciale.offerte.stati');
    $sn = config('wecore.sn');
    $row = 1;

    $scadenze = FatturazioneScadenze::whereIn('offerta_id', $this->offerte->pluck('id')->toArray())->get();

    foreach($scadenze as $scadenza)
    {
      $offerte_array[++$row] = [
        optional(optional(optional(optional($scadenza->offerta())->first()->cliente())->first()->commerciale())->first())->full_name,
        optional(optional($scadenza->offerta())->first()->cliente())->first()->ragione_sociale,
        $scadenza->descrizione,
        get_currency(clean_currency($scadenza->importo)),
        optional(optional($scadenza->ordinativo())->first())->numero_ordinativo(),
        optional(optional($scadenza->offerta())->first())->numero_offerta(),
        optional(optional($scadenza->offerta())->first())->oggetto,
        get_currency(optional(optional($scadenza->offerta())->first())->importo_iva),
        optional(optional($scadenza->fattura())->first())->get_numero_fattura(),
        optional(optional($scadenza->fattura())->first())->oggetto,
        get_currency(clean_currency(optional(optional($scadenza->fattura())->first())->totale_fattura)),
        $scadenza->data,
        $scadenza->data_avviso,
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
