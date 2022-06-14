<?php

namespace Modules\Export\Entities;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Assistenza\Entities\TicketIntervento;
use Modules\Wecore\Entities\Meta;
use Modules\Wecore\Entities\Metagable;



class TicketInterventoExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
  public function __construct($tickets)
  {
    $this->tickets = $tickets;
  }

  public function headings(): array
  {
    return ['DATA INTERVENTO', 'UTENTE', 'CLIENTE', 'ORDINATIVO',  'ATTIVITA', 'SETTORE', 'NUMERO G/H', 'DATA CREAZIONE'];
  }

  public function array(): array
  {

    $settori = config('assistenza.ticket_intervento.settori');
    $intervento_tipi = config('commerciale.interventi.tipi');
    $row = 1;

    foreach($this->tickets as $key => $ticket)
    {

      $voce = $ticket->voci->first();
      $gruppo = $ticket->ordinativo->giornate->where('gruppo_id', $ticket->gruppo_id)->first();
      $intervento_tipo = !empty($gruppo) ? $intervento_tipi[$gruppo->tipo] : '';

      $tickets_array[++$row] = [
        !empty($voce) ? get_date_ita($voce->data_intervento) : '',
        $ticket->created_user->full_name,
        optional($ticket->cliente)->ragione_sociale,
        optional($ticket->ordinativo)->oggetto,
        optional($ticket->gruppo)->nome,
        $settori[$ticket->settore_id],
        optional($ticket->voci)->sum('quantita') . ' ' . $intervento_tipo,
        get_date_hour_ita($ticket->created_at)
      ];
    }

    return $tickets_array;
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