<?php
namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationMail extends Mailable
{

    use Queueable,
        SerializesModels;

    public function __construct($user)
    {
        $this->user = $user;
    }

    //build the message.
    public function build()
    {
        $locale = app('translator')->getLocale();
        if ($locale === 'es') {
            return $this->view('mails.confirmation_es', [
                'user' => $this->user
            ]);    
        }
        
        return $this->view('mails.confirmation', [
            'user' => $this->user
        ]);
    }
}
