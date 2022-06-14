<?php

namespace Modules\Wecore\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailApprovazioni extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The subject instance.
     *
     * @var Subject
     */
    public $subject;

    /**
     * The content instance.
     *
     * @var Content
     */
    protected $content;

    /**
     * The attach instance. ['path_file', 'name']
     *
     * @var Attach
     */
    protected $attach;

    /**
     * The azienda instance.
     *
     * @var Azienda
     */
    protected $azienda;

    /**
     * The azienda instance.
     *
     * @var FromEmail
     */
    protected $from_email;

    /**
     * The azienda instance.
     *
     * @var FromName
     */
    protected $from_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $attach = null, $azienda = null, $from_email = null, $from_name = null)
    {
        $azienda = ($azienda === null) ? session('azienda') : $azienda;
        set_azienda($azienda);

        $this->subject = $subject . ' - ' . $azienda;
        $this->content = $content;
        $this->attach = $attach;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        $this->azienda = get_azienda_dati();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $header = view('wecore::emails.header', ['azienda' => $this->azienda]);
        $footer = view('wecore::emails.footer', ['azienda' => $this->azienda]);

        $build_msg = $this->subject($this->subject)
                          ->from($this->from_email, $this->from_name)
                          ->view('wecore::emails.email_approvazioni')
                          ->with([
							  	'azienda' => $this->azienda,
						  		'header' => $header,
								'content' => $this->content,
								'footer' => $footer]);

        return $build_msg;
    }
}
