<?php
/**
 * Routes for Management panel
 */

return [
    'login' => 'manage/site/login',
    'logout' => 'manage/site/logout',
    '<controller:(.+)>/<action:(.+)>' => 'manage/<controller>/<action>',
    '<controller:(.+)>' => 'manage/<controller>/index',
    '' => 'manage/site/index',

    /** Panel Manage API */
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'manage/user',
        ],
        'extraPatterns' => [
            'GET delete-all' => 'delete-all',
            'DELETE all' => 'delete-all',
        ],
        'except' => ['create', 'update', 'delete'],
    ],
];