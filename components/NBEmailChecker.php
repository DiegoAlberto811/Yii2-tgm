<?php

namespace app\components;

use NeverBounce\Object\VerificationObject;
use NeverBounce\Single as NBClient;
use NeverBounce\Auth as NBAuth;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

/**
 * Class NBEmailChecker
 * @package app\components
 * @return string validation result (`valid`, `catchall`, `unknown`, `disposable` and `invalid`)
 */

class NBEmailChecker extends Component
{
    /**
     * @param $email
     * @return VerificationObject
     * @throws ServerErrorHttpException
     */
    public static function checkEmail($email)
    {
        try {
            $apiKey = ArrayHelper::getValue(\Yii::$app->params, 'neverbounce.apiKey');
            NBAuth::setApiKey($apiKey);
            $verificationObject = NBClient::check($email, true, true);
        } catch (\Throwable $e) {
            \Yii::error('[NBEmailChecker] ' . $e->getMessage());

            throw new ServerErrorHttpException('Internval Server Error');
        }

        return $verificationObject;
    }
}