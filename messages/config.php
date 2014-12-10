<?php
/**
 * config.php
 * @author Roman Revin http://phptime.ru
 */

return [
    'sourcePath' => __DIR__ . '/..',
    'messagePath' => __DIR__,
    'languages' => ['en', 'ru'],
    'translator' => 'Yii::t',
    'sort' => false,
    'overwrite' => true,
    'removeUnused' => true,
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/vendor',
    ],
    'only' => ['*.php'],
    'format' => 'php',
];