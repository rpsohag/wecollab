<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class newTicketCliente extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($richiestaintervento)
    {
        $this->data = $richiestaintervento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject($this->data->email_oggetto)
        ->from('noreply@wecollab.it', $name = 'Wecollab')
        ->view('mails.richiesteintervento_nuovo_ticket_cliente', ['codice' => $this->data->codice, 'cliente' => $this->data->cliente->ragione_sociale, 'gruppo' => $this->data->gruppo->nome, 'descrizione' => $this->data->descrizione_richiesta, 'oggetto' => $this->data->oggetto]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toArray($notifiable)
    {
        return [
            'tipologia' => 'Nuovo Ticket',
            'codice' => $this->data->codice,
            'cliente' => $this->data->cliente->ragione_sociale,
        ];
    }
}
