<?php
namespace app\components\helpers;

use app\models\Profile;
use yii\helpers\Html;

class PublicHelper
{
    public static function getUsername()
    {
        if (\Yii::$app->user->isGuest) {
            return 'Guest';
        }

        return \Yii::$app->user->identity->email;
    }

    public static function facebookButton()
    {
        return Html::tag('span', '', ['class' => 'fa fa-facebook'])
            . TranslateMessage::t('user', 'Sign in with Facebook');
    }

    public static function googleButton()
    {
        return Html::tag('span', '', ['class' => 'fa fa-google'])
            . TranslateMessage::t('user', 'Sign in with Google');
    }
}
