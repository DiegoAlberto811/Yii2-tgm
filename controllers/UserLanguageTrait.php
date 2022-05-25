<?php
namespace app\controllers;

use app\components\helpers\LanguageHelper;

trait UserLanguageTrait
{
    public function init()
    {
        parent::init();

        LanguageHelper::identifyUserLanguage();
    }
}