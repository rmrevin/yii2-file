<?php
/**
 * main.php
 * @author Roman Revin http://phptime.ru
 */

return [
    'id' => 'testapp',
    'basePath' => realpath(__DIR__ . '/..'),
    'aliases' => [
        '@vendor/rmrevin/yii2-file' => realpath(__DIR__ . '/../../..'),
    ],
    'bootstrap' => [
        '\rmrevin\yii\module\File\Bootstrap',
    ],
    'modules' => [
        'file' => '\rmrevin\yii\module\File\Module',
    ],
    'components' => [
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'sqlite::memory:',
        ],
    ],
];