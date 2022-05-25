<?php

use yii\helpers\ArrayHelper;
use app\models\User as PanelistIdentity;
use app\modules\manage\models\UserIdentity as AdminIdentity;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

list($subDomain,) = explode('.', $_SERVER['HTTP_HOST']);
$isManagementPanel = 'access' === $subDomain;

if ($isManagementPanel) {
    $envRoutes = require __DIR__ . '/routes-access.php';
} else {
    $envRoutes = require __DIR__ . '/routes-fe.php';
}

$config = [
    'id' => 'tgm-panel',
    'name' => 'TGM Panel',
    'version' => '1.4.0',
    'sourceLanguage' => 'en',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Dubai',
    'components' => [
        'cint' => [
            'class' => \app\components\integration\Cint::class,
            'url' => ArrayHelper::getValue($params, 'cint.url'),
            'apiKey' => ArrayHelper::getValue($params, 'cint.apiKey'),
            'apiSecret' => ArrayHelper::getValue($params, 'cint.apiSecret'),
        ],
        'authClientCollection' => [
            'class'   => \yii\authclient\Collection::class,
            'clients' => [
                'facebook' => [
                    'class' => 'dektrium\user\clients\Facebook',
                    'clientId' => '118859595426684',
                    'clientSecret' => 'b5706b0b9c7ad152c8bd7cab74369956',
                    'attributeNames' => ['name', 'email', 'first_name', 'last_name'],
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'L9UE-sG-hGwUd2n_XQ1tuszCFVAX9W-u',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'formatters' => [
                'javascript' => 'app\components\formatters\JavascriptFormatter',
            ],
        ],
        'session' => [
            'timeout' => 24*60*60,
        ],
        'formatter' => [
            'nullDisplay' => '',
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => ArrayHelper::getValue($params, 'recaptcha.siteKey'),
            'secret' => ArrayHelper::getValue($params, 'recaptcha.secret'),
        ],
        'transferTo' => [
            'class' => 'app\components\TransferTo',
            'login' => ArrayHelper::getValue($params, 'transfer-to.login'),
            'token' => ArrayHelper::getValue($params, 'transfer-to.token'),
        ],
        'app_i18n' => [
            'class' => 'yii\i18n\I18N',
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceLanguage' => 'en',
                    'sourceMessageTable'=>'{{%source_message}}',
                    'messageTable'=>'{{%message}}',
                    'enableCaching' => false,
                    //'cachingDuration' => 10,
                    'forceTranslation'=>true,
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => $isManagementPanel ? AdminIdentity::class: PanelistIdentity::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/login'],
        ],
        'errorHandler' => [
            'errorAction' => $isManagementPanel ? 'manage/site/error' : 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $envRoutes,
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        'jquery.min.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        'js/bootstrap.min.js',
                    ],
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/public/dektrium'
                ],
            ],
        ],
    ],
    'modules' => [
        'manage' => [
            'class' => 'app\modules\manage\Module',
            'adminToken' => 'aTrn2fH3ZjPkGrkxFSVsJB920ekFXEdf',
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['podroze'],
            'mailer' => [
                'class' => \app\components\dektrium\Mailer::class,
                'sender' => ['mobi.tgm@gmail.com' => 'TGM Panel'],
            ],
            'enableFlashMessages' => false,
            'enableGeneratingPassword' => false,
            'enableConfirmation' => false,
            'modelMap' => [
                'LoginForm' => 'app\models\forms\LoginForm',
                'RecoveryForm' => 'app\models\forms\RecoveryForm',
                'RegistrationForm' => 'app\models\forms\RegistrationForm',
                'Profile' => 'app\models\Profile',
                'User' => 'app\models\User',
            ],
            'controllerMap' => [
                'recovery' => [
                    'class' => 'app\controllers\DektriumRecoveryController',
                    'layout' => '@app/views/layouts/index',
                ],
                'registration' => [
                    'class' => 'app\controllers\DektriumRegistrationController',
                    'layout' => '@app/views/layouts/index',
                    'on '. \dektrium\user\controllers\RegistrationController::EVENT_AFTER_REGISTER => function($e) {
                        Yii::$app->session->destroy();
                        $user = \dektrium\user\models\User::findOne(['username'=>$e->form->username, 'email'=>$e->form->email]);

                        if ($user) {
                            Yii::$app->user->switchIdentity($user);
                        }
                        \Yii::$app->response->redirect(\Yii::$app->user->returnUrl);
                    },
                ],
                'security' => [
                    'class' => 'app\controllers\DektriumSecurityController',
                    'layout' => '@app/views/layouts/index',
                    'on '. \dektrium\user\controllers\SecurityController::EVENT_BEFORE_LOGIN => function($e) {
                        $model = \Yii::createObject(\app\models\forms\LoginForm::class);
                        $model->login = \Yii::$app->session->get('full_phone', null);
                    },
                ],
            ],
        ],
        'rbac' => 'dektrium\rbac\RbacWebModule',
    ],
    'params' => $params,
];

return $config;
