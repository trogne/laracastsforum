<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; //that'll ensure that for production we'll fire this email through a queue

class PleaseConfirmYourEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user; //the way mailable works : all public properties will be available to the view that you load
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->markdown('email.confirm-email')->with('user', $this->user);
        return $this->markdown('email.confirm-email'); //were leveraging a markdown view, scaffold this for us...  au lieu de $this->view('view.name');
    }
}
