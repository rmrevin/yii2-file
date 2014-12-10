<?php
/**
 * Bootstrap.php
 * @author Roman Revin http://phptime.ru
 */

namespace rmrevin\yii\module\File;

/**
 * Class Bootstrap
 * @package rmrevin\yii\module\File
 */
class Bootstrap implements \yii\base\BootstrapInterface
{

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getI18n()->translations['service-file'] = \Yii::createObject([
            'class' => \yii\i18n\PhpMessageSource::className(),
            'basePath' => '@vendor/rmrevin/yii2-file/messages',
        ]);
    }
}