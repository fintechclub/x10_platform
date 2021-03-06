<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordRecovery extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
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

        $data['url'] = env('APP_URL') . '/auth/activate/' . $this->user->id . '/' . $this->user->token;
        $data['name'] = $this->user->name;

        return $this->markdown('emails.user.password_recovery', $data)->subject('Восстановление пароля для X10.Fund');

    }

}
