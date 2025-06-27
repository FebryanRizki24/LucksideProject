<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendGeneratedPassword extends Mailable
{
    use SerializesModels;

    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Akun Anda Berhasil Dibuat')
                    ->view('vendor.notifications.generated_password')
                    ->with(['password' => $this->password]);
    }
}
