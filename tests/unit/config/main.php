<?php
/**
 * main.php
 * @author Roman Revin http://phptime.ru
 */

return [
	'id' => 'testapp',
	'basePath' => realpath(__DIR__ . '/..'),
    'modules' => [
        'file' => [
            'class' => '\rmrevin\yii\module\File\Module',
        ]
    ],
    'components' => [
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'sqlite::memory:',
        ],
    ],
];