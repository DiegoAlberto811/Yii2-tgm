<?php
/**
 * Front-end routes
 */

return [
    // user portal
    '/login' => '/user/security/login',
    '/logout' => '/user/security/logout',
    '/sign-up' => '/registration/index',
    '/js/messages-<lang:\w+>' => '/site/messages',
];