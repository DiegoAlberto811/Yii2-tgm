<?php
namespace app\components\dektrium;

use dektrium\user\models\Token;
use dektrium\user\models\User;
use dektrium\user\Mailer as DektriumMailer;

class Mailer extends DektriumMailer
{
    public $viewPath = '@app/views/mailer';

    /**
     * Sends an email to a user with recovery link.
     *
     * @param User  $user
     * @param Token $token
     *
     * @return bool
     */
    public function sendRecoveryMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->email, $this->getRecoverySubject(), 'recovery_token', [
            'user' => $user,
            'token' => $token,
        ]);
    }
}