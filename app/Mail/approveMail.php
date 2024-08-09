<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class approveMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data1;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data1 = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $nama   = $this->data1['nama'];
        $attach = $this->data1['attach'];
        
        return $this    
                ->from('hendikedison09@gmail.com')
                ->view('membership.mailApprove')
                ->subject('Persetujuan Keanggotaan')
                ->attach($attach)
                ->with([
                    'nama' => $nama
                ]);
    }
}
