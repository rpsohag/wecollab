<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Commerciale\Entities\Ordinativo;


class OrdinativiExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($ordinativi)
  {
    $this->ordinativi = $ordinativi;
  }

  public function headings(): array
  {
    return ['NUMERO ORDINATIVO', 'OGGETTO', 'CLIENTE', 'STATO', 'INIZIO', 'SCADENZA', 'SENZA CATEGORIA', 'CDM', 'CANONI DA SERVIZI CONSULENZIALI', 'CANONI DA SISTEMISTICA', 'ATTIVAZIONI PARCO', 'ATTIVAZIONI PROSPECT', 'SERVIZI URBI', 'SERVIZI CONSULENZIALI', 'SERVIZI SISTEMISTICI', 'FORNITURA HARDWARE E SOFTWARE TERZE PARTI', 'ALTRO', 'IMPORTO TOTALE'];
  }

  public function array(): array
  {
    $row = 1;
    $categorie = [0 => 'SENZA CATEGORIA', 1 => 'CDM', 2 => 'CANONI DA SERVIZI CONSULENZIALI', 3 => 'CANONI DA SISTEMISTICA', 4 => 'ATTIVAZIONI PARCO', 5 => 'ATTIVAZIONI PROSPECT', 6 => 'SERVIZI URBI', 7 => 'SERVIZI CONSULENZIALI', 8 => 'SERVIZI SISTEMISTICI', 9 => 'FORNITURA HARDWARE E SOFTWARE TERZE PARTI', 10 => 'ALTRO'];

    foreach($this->ordinativi as $key => $ordinativo)
    {

      for ($i = 0; $i <= 10; $i++)
      {
        $importo[$i] = 0;
      }

      if(!empty($ordinativo->voci_economiche)) {
        foreach($ordinativo->voci_economiche as $voce)
        {
          if(empty($voce->categoria) || $voce->categoria == 0)
            $importo[0] += $voce->importo;
  
          elseif($voce->categoria == 1)
            $importo[1] += $voce->importo;
  
          elseif($voce->categoria == 2)
            $importo[2] += $voce->importo;
    
          elseif($voce->categoria == 3)
            $importo[3] += $voce->importo;
  
          elseif($voce->categoria == 4)
            $importo[4] += $voce->importo;
  
          elseif($voce->categoria == 5)
            $importo[5] += $voce->importo;
  
          elseif($voce->categoria == 6)
            $importo[6] += $voce->importo;
  
          elseif($voce->categoria == 7)
            $importo[7] += $voce->importo;
  
          elseif($voce->categoria == 8)
            $importo[8] += $voce->importo;
  
          elseif($voce->categoria == 9)
            $importo[9] += $voce->importo;
  
          elseif($voce->categoria == 10)
            $importo[10] += $voce->importo;
        }
      }

      $ordinativi_array[++$row] = [
        $ordinativo->numero_ordinativo(),
        $ordinativo->oggetto,
        optional($ordinativo->cliente())->ragione_sociale,
        $ordinativo->stato(),
        $ordinativo->data_inizio,
        $ordinativo->data_fine,
        get_currency($importo[0]),
        get_currency($importo[1]), 
        get_currency($importo[2]),
        get_currency($importo[3]),   
        get_currency($importo[4]),
        get_currency($importo[5]),   
        get_currency($importo[6]),
        get_currency($importo[7]),   
        get_currency($importo[8]),
        get_currency($importo[9]),
        get_currency($importo[10]),           
        get_currency($ordinativo->importo()),
      ];
    }

    return $ordinativi_array;
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