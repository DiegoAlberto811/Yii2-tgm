<?php
namespace app\components\helpers;

use yii\web\Cookie;

class LanguageHelper
{
    public static function identifyUserLanguage()
    {
        $languages = TranslateMessage::getLanguages('lang');

        $getLang = \Yii::$app->request->get('lang');

        if ($getLang && in_array($getLang, $languages)) {
            \Yii::$app->response->cookies->add(new Cookie([
                'name' => 'RMS_LANG',
                'value' => $getLang,
            ]));
            $language = $getLang;
        } else {
            $defaultLanguage = \Yii::$app->request->getPreferredLanguage($languages);
            $language = \Yii::$app->request->cookies->getValue('RMS_LANG') ?: $defaultLanguage;
        }

        \Yii::$app->language = $language;
    }
}