<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Tasklist\Entities\Timesheet;


class TimesheetsExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($timesheets)
  {
    $this->timesheets = $timesheets;
  }

  public function headings(): array
  {
    return ['CLIENTE', 'PROCEDURA', 'AREA DI INTERVENTO', 'ATTIVITA', 'ORDINATIVO', 'TASKLIST ATTIVITA',  'NOTA', 'TIPOLOGIA', 'DATA INIZIO', 'DURATA'];
  }

  public function array(): array
  {
    $row = 1;

    foreach($this->timesheets as $key => $timesheet)
    {
      $timesheets_array[++$row] = [
        $timesheet->cliente->ragione_sociale,
        $timesheet->procedura->titolo,
        $timesheet->area->titolo,
        get_if_exist($timesheet->gruppo, 'nome'),
        get_if_exist($timesheet->ordinativo, 'oggetto'),
        get_if_exist($timesheet->attivita, 'oggetto'),
        $timesheet->nota,
        $timesheet->tipologia(),
        get_date_hour_ita(date('Y-m-d H:i:s', strtotime($timesheet->dataora_inizio))),
        $timesheet->durataRaw()
      ];
    }

    return $timesheets_array;
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