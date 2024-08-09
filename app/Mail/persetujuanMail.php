<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class persetujuanMail extends Mailable
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
        $nama           = $this->data1['nama'];
        $code           = $this->data1['code'];
        $gender         = $this->data1['gender'];
        $tgl_lahir      = $this->data1['tgl_lahir'];
        $phone_number   = $this->data1['phone_number'];
        $alamat         = $this->data1['alamat'];
        $email          = $this->data1['email'];        
        $attach         = $this->data1['attach'];
        
        return $this    
                ->from('hendikedison09@gmail.com')
                ->view('membership.mail')
                ->subject('Konfirmasi Persetujuan Keanggotaan')
                ->attach($attach)
                ->with([
                    'nama'          => $nama,
                    'code'          => $code,
                    'gender'        => $gender,
                    'tgl_lahir'     => $tgl_lahir,
                    'phone_number'  => $phone_number,
                    'alamat'        => $alamat,
                    'email'         => $email
                ]);
    }
}
