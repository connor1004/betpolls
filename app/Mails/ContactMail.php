<?php
namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{

    use Queueable,
        SerializesModels;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    //build the message.
    public function build()
    {
        return $this->view('mails.contact', [
            'contact' => $this->contact
        ]);
    }
}
