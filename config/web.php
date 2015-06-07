<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'models' .
    DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'AntragsgruenApp.php');

/** @var \app\models\settings\AntragsgruenApp $params */
$params = require(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
$common = require(__DIR__ . DIRECTORY_SEPARATOR . 'common.php');

$config = yii\helpers\ArrayHelper::merge(
    $common,
    [
        'id'         => 'basic',
        'components' => [
            'errorHandler' => [
                //'errorAction' => 'manager/error',
            ],
            'user'         => [
                'identityClass'   => 'app\models\db\User',
                'enableAutoLogin' => true,
            ],
            'authClientCollection' => [
                'class' => 'yii\authclient\Collection',
                'clients' => $params->authClientCollection,
            ],
            'request'      => [
                // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
                'cookieValidationKey' => $params->cookieValidationKey,
            ],
        ],
    ]
);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
