<?php
namespace app\components;

use app\components\helpers\TranslateMessage;
use app\models\VerificationCode;
use Yii;

class SendVerificationSms
{
    const SESSION_SMS = 'sms';

    /**
     * Renders new code and send a SMS
     * @param $phone_number
     * @return VerificationCode
     */
    public static function send($phone_number)
    {
        $verCode = VerificationCode::generate($phone_number);

        if (!$verCode->hasErrors()) {
            Yii::$app->session->set(self::SESSION_SMS, $verCode->code);

            $message = TranslateMessage::t('user', 'Hi, TGM Panel here. Thank you for join us! Your activation code is: {code}', ['code' => $verCode->code]);

            SmsSender::send($verCode->phone, $message);
        }

        return $verCode;
    }
}