<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class newTicketAssegnatari extends Notification
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
                    ->view('mails.richiesteintervento_nuovo_ticket', ['assegnato_da' => $this->data->assegnato_da, 'codice' => $this->data->codice, 'cliente' => $this->data->cliente->ragione_sociale, 'gruppo' => $this->data->gruppo->nome, 'descrizione' => $this->data->descrizione_richiesta, 'oggetto' => $this->data->oggetto, 'url' => route('admin.assistenza.richiesteintervento.read', $this->data->id)]);
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
            'assegnato_da' => $this->data->assegnato_da,
            'cliente' => $this->data->cliente->ragione_sociale,
            'url' => route('admin.assistenza.richiesteintervento.read', $this->data->id),
        ];
    }
}
