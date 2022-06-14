<?php

namespace Modules\Assistenza\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

use Modules\Assistenza\Entities\RichiesteIntervento;
use Modules\Profile\Entities\Area;
use Modules\Profile\Entities\Gruppo;
use Modules\Amministrazione\Entities\Clienti;

use PhpImap\Exceptions\ConnectionException;
use PhpImap\Mailbox;

class TicketByEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $_ASSISTENZA_EMAIL_HOST = '{imaps.aruba.it:993/ssl}INBOX';
        $debug = true;
        
        $caselle = Gruppo::whereNotNull('email')->whereNotNull('password')->where('email', '!=', '')->where('password', '!=', '')->groupBy('email')->get();
        foreach ($caselle as $key => $casella) {

            $mailbox = new Mailbox(
                $_ASSISTENZA_EMAIL_HOST,
                $casella['email'],
                $casella['password']
            );

            try {
                $mail_ids = $mailbox->searchMailbox('ALL');
            } catch (ConnectionException $ex) {
                die('IMAP connection failed: '.$ex->getMessage());
            }catch (Exception $ex) {
                die('An error occured: '.$ex->getMessage());
            }

            if ($debug)
                print_r($mail_ids);

            foreach ($mail_ids as $mail_id) {
                if ($debug)
                    echo "+------ P A R S I N G   E M A I L   N.$mail_id from ".$casella['email']." ------+\n";

                // get email from id
                $email = $mailbox->getMail(
                    $mail_id,
                    !$debug // Do NOT mark emails as seen (optional)
                ); 
                
                // get body
                if ($email->textHtml) {
                    $body_mail = strip_tags($email->textHtml, '<br><p><li>');
                    $body_mail = preg_replace ('/<[^>]*>/', PHP_EOL, $body_mail);
                } else {
                    $body_mail = $email->textPlain;
                }
                $body_mail = str_replace("\r", '', $body_mail);
                $body_mail = str_replace("\n", chr(10), $body_mail);

                // get other email field
                $from_name = (string) (isset($email->fromName) ? ucwords($email->fromName) : $email->fromAddress);
                $from_email = (string) $email->fromAddress;
                $to = (string) $email->toString;
                $subject = trim(str_replace(["Fw:", "Fwd:","Rif:","Re:"], "", (string) $email->subject));
                $message_id = (string) $email->messageId;
                
                // prende i destinatari in base al gruppo
                $gruppo = Gruppo::find($casella->id);
                $destinatari_id = collect($gruppo->users)->pluck('id')->toArray();
                $procedura_id = Area::find($casella->area_id)->procedura_id;
                $cliente_id = Clienti::find(81)->id;

                $insert['cliente_id'] = $cliente_id;
                $insert['procedura_id'] = $procedura_id;
                $insert['area_id'] = $casella->area_id;
                $insert['gruppo_id'] = $casella->id;
                $insert['ordinativo_id'] = 0;
                $insert['oggetto'] = $subject;
                $insert['descrizione_richiesta'] = $body_mail;
                $insert['livello_urgenza'] = 0;
                $insert['motivo_urgenza'] = '';
                $insert['richiedente'] = (!isset($from_name) || trim($from_name) === '' ? $from_email : $from_name);
                $insert['numero_da_richiamare'] = '';
                $insert['email'] = $from_email;
                $insert['destinatario_id'] = json_encode($destinatari_id);
                
                // create a request from insert
                $req = new Request($insert);
                $req->setMethod('POST');

                // check attachments
                $ff = [];
                $filebag = new FileBag();
                if ($email->hasAttachments()) {
                    echo "Yes, mail has attachments\n";
                    echo count($email->getAttachments())." attachements\n";
                    
                    // crea cartella temporanea per salvare gli allegati
                    if (!file_exists('tmp')) {
                        mkdir('tmp');
                        //mkdir('tmp', 0775, true);
                        //chmod('tmp', 0775);
                    }

                    // Save attachments one by one
                    if (!$mailbox->getAttachmentsIgnore()) {
                        $attachments = $email->getAttachments();

                        foreach ($attachments as $attachment) {
                            if($attachment->sizeInBytes >= 1) {
                                echo '--> Saving '.(string) $attachment->name.' ...';

                                $attachment->setFilePath('tmp/'.$attachment->name);

                                if ($attachment->saveToDisk()) {
                                    echo "OK, saved!\n";
                                    $f = new UploadedFile(
                                        $attachment->filePath,
                                        $attachment->name,
                                        $attachment->mime,
                                        $attachment->sizeInBytes,
                                        TRUE
                                    );
                                    $ff[] = $f;
                                } else {
                                    echo "ERROR, could not save!\n";
                                }
                            }
                        }
                    }
                }

                if ($debug)
                    print_r($ff);

                // inserisce i files nella richiesta
                if(!empty($ff)){
                    $filebag->add(['files' => $ff]);
                    $req->merge(['files' => $ff]);
                    $req->files = $filebag;
                }
                    
                if ($debug)
                    print_r($req);

                // setta l'azienda 
                set_azienda('we-com');

                // crea ticket
                $res = RichiesteIntervento::create_ticket_web($req, true);
                if ($debug)
                    print_r($res);

                //if (!empty($email->autoSubmitted)) {
                //    // Mark email as "read" / "seen"
                //    $mailbox->markMailAsRead($mail_id);
                //    echo "+------ IGNORING: Auto-Reply ------+\n";
                //}
                //if (!empty($email_content->precedence)) {
                //    // Mark email as "read" / "seen"
                //    $mailbox->markMailAsRead($mail_id);
                //    echo "+------ IGNORING: Non-Delivery Report/Receipt ------+\n";
                //}
                
                if (!$debug)
                    $mailbox->deleteMail($mail_id);

            }


            // remove folder and files
            if (!$debug)
                rmdir_recursive('tmp');

            // remove email and close connection
            if (!$debug)
                $mailbox->expungeDeletedMails();
            
            $mailbox->disconnect();
        }
    }
}
