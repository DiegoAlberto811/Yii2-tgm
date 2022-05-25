<?php
namespace app\controllers;

use app\components\helpers\TranslateMessage;
use app\models\translation\SourceMessage;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    use UserLanguageTrait;

    public $layout = 'index';

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['/profile']);
        }

        return $this->redirect(['/login']);
    }

    /**
     * Processes /js/messages.{lang}.js request
     * @param string $lang
     * @return string
     */
    public function actionMessages($lang = 'en')
    {
        $categories = ['js-app', 'js-reg'];
        $cacheDuration = 1; // 30 * 24 * 60 * 60;
        $cacheKey = "js/message.$lang.js";

        \Yii::$app->language = $lang;

        $translations =  \Yii::$app->cache->getOrSet($cacheKey, function() use($categories) {
            $sourceMessages = SourceMessage::find()->where(['IN', 'category', $categories])->asArray()->all();
            $sourceMessages = ArrayHelper::map($sourceMessages, 'id', 'message', 'category');

            $translations = [];
            foreach ($sourceMessages as $lang => $messages) {
                $translations[$lang] = [];
                foreach ($messages as $message) {
                    $translations[$lang][$message] = TranslateMessage::t($lang, $message);
                }
            }
            return $translations;
        }, $cacheDuration);

        return 'window.tgmMessages = ' . Json::encode($translations) .';';
    }
}