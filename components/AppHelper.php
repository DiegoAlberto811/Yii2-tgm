<?php
/**
 * Application Helper Class
 *
 */

namespace app\components;

use app\components\helpers\TranslateMessage;
use app\models\Info;
use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class AppHelper
{
    /**
     * Returns environment title prefix
     *
     * @return string
     */
    public static function environmentPreffix()
    {
        $preffix = \Yii::$app->params['environment']['title-prefix'];

        return $preffix ? "[{$preffix}] " : "";
    }

    /**
     * Returns environment badge for NavBar
     *
     * @return string
     */
    public static function environmentBadge()
    {
        $preffix = \Yii::$app->params['environment']['title-prefix'];

        if ($preffix) {
            return Nav::widget([
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => $preffix,
                        'url' => null,
                    ],
                ],
                'options' => [
                    'class' => 'navbar-nav btn-danger',
                ],
            ]);
        }

        return '';
    }

    public static function total($models, $attributes)
    {
        $total=0;

        foreach($models as $item) {
            $total+= $item->{$attributes};
        }

        return $total;
    }

    public static function timeUtc($time = null)
    {
        return ($time ?: time()) - date('Z');
    }

    public static function balance()
    {
        return \Yii::$app->formatter->asCurrency(
            Info::value(Info::TRANSFERTO_BALANCE),
            Info::value(Info::TRANSFERTO_CURRENCY)
        );
    }

    public static function getLanguagesItem()
    {
        $languages = TranslateMessage::getLanguages('native_name');

        /** @todo improve default language detection */
        $defaultLanguage = \Yii::$app->request->getPreferredLanguage(array_keys($languages));

        $userLanguage = ArrayHelper::getValue($languages, \Yii::$app->language, $languages[$defaultLanguage]);

        ArrayHelper::removeValue($languages, $userLanguage);

        $item = [
            'label' => $userLanguage,
            'items' => [

            ],
        ];

        ksort($languages);

        foreach($languages as $la => $language) {
            $item['items'][] = [
                'label' => $language,
                'url' => Url::to(['/', 'lang' => $la]),
            ];
        }

        return $item;
    }

    public static function ip2long($ip)
    {
        $ip = explode(".", $ip);

        return ($ip[3] + $ip[2] * 256 + $ip[1] * 256 * 256 + $ip[0] * 256 * 256 * 256);
    }
}
