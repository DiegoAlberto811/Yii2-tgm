<?php
namespace app\assets;

use app\components\helpers\LanguageHelper;
use yii\web\AssetBundle;

class PublicAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [

    ];

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        LanguageHelper::identifyUserLanguage();

        $ver = \Yii::$app->version;

        $module = \Yii::$app->controller->module->id;
        $contrl = \Yii::$app->controller->id;
        $action = \Yii::$app->controller->action->id;

        $this->css[] = "css/public.css?v={$ver}";

        $jsActionFile = "js/{$module}/{$contrl}/{$action}.js";
        if (file_exists(\Yii::getAlias("@app/web/$jsActionFile"))) {
            $this->js[] = "$jsActionFile";
        }

        $this->js[] = 'js/messages-' . \Yii::$app->language;

        foreach ($this->js as $i => $jsFile) {
            $this->js[$i] = "$jsFile?v={$ver}";
        }

        parent::init();
    }
}