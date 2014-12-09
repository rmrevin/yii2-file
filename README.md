Yii 2 module for file management
===============================
[![License](https://poser.pugx.org/rmrevin/yii2-file/license.svg)](https://packagist.org/packages/rmrevin/yii2-file)
[![Latest Stable Version](https://poser.pugx.org/rmrevin/yii2-file/v/stable.svg)](https://packagist.org/packages/rmrevin/yii2-file)
[![Latest Unstable Version](https://poser.pugx.org/rmrevin/yii2-file/v/unstable.svg)](https://packagist.org/packages/rmrevin/yii2-file)
[![Total Downloads](https://poser.pugx.org/rmrevin/yii2-file/downloads.svg)](https://packagist.org/packages/rmrevin/yii2-file)

Installation
------------
Add in `composer.json`:
```
{
    "require": {
        "rmrevin/yii2-file": "*"
    }
}
```

Usage
-----
In config
```php
<?
// ...

return [
    // ...
    'modules' => [
        // ...
        'file' => [
            'class' => '\rmrevin\yii\module\File\Module',
            'upload_alias' => '@app/web/upload',
            'upload_web_alias' => '/upload',
            'storage_alias' => '@app/web/storage',
            'storage_web_alias' => '/storage',
            'max_upload_file_size' => 10, // megabytes
        ],
    ],
];

```

Save file into database
```php
use rmrevin\yii\module\File;

// external resource
$File = File\models\File::push(new File\component\ExternalResource('https://www.google.ru/images/srpr/logo11w.png'));

// internal resource
$File = File\models\File::push(new File\component\InternalResource('/var/www/images/pick.png'));

// uploaded resource
$File = File\models\File::push(new File\component\UploadedResource(UploadedFile::getInstance($model, 'file')));
```

Manipulation with images
```php
use rmrevin\yii\module\File;

$File = File\models\File::find()->one();
echo Html::img((string)$File->image()
    ->resizeByWidth(100));
// available methods: resize, resizeByWidth, resizeByHeight, crop, thumbnail, watermark, text
```