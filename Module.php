<?php
/**
 * Module.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File;

use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * Class Module
 * @package rmrevin\yii\module\File
 */
class Module extends \yii\base\Module
{

    /** @var string */
    public $upload_alias = '@app/web/upload';

    /** @var string */
    public $upload_path = null;

    /** @var string */
    public $upload_web_alias = '/upload';

    /** @var string */
    public $upload_web_path = null;

    /** @var string */
    public $storage_alias = '@app/web/storage';

    /** @var string */
    public $storage_path = null;

    /** @var string */
    public $storage_web_alias = '/storage';

    /** @var string */
    public $storage_web_path = null;

    /** @var int */
    public $max_upload_file_size = 10; // megabytes

    /** @var string */
    public $no_image_alias = '@vendor/rmrevin/yii2-file/assets/no-image.png';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->checkIniSizeParam('upload_max_filesize');

        $this->checkIniSizeParam('post_max_size');

        $this->reinitAliases();

        if (empty($this->upload_path)) {
            throw new InvalidConfigException(\Yii::t('service-file', 'Unable to determine the path to the upload directory (FileService::$upload_path).'));
        }

        if (empty($this->storage_path)) {
            throw new InvalidConfigException(\Yii::t('service-file', 'Unable to determine the path to the storage directory (FileService::$storage_path).'));
        }

        $this->createDirectory($this->upload_path);

        $this->createDirectory($this->storage_path);

        if (!is_readable($this->upload_path)) {
            throw new \RuntimeException(\Yii::t('service-file', 'Upload directory not available for reading (FileService::$upload_path).'));
        }

        if (!is_writable($this->upload_path)) {
            throw new \RuntimeException(\Yii::t('service-file', 'Upload directory not available for writing (FileService::$upload_path).'));
        }

        if (!is_readable($this->storage_path)) {
            throw new \RuntimeException(\Yii::t('service-file', 'Storage directory not available for reading (FileService::$storage_path).'));
        }

        if (!is_writable($this->storage_path)) {
            throw new \RuntimeException(\Yii::t('service-file', 'Storage directory not available for writing (FileService::$storage_path).'));
        }
    }

    public function reinitAliases()
    {
        $this->storage_path = \Yii::getAlias($this->storage_alias);
        $this->storage_web_path = \Yii::getAlias($this->storage_web_alias);
        $this->upload_web_path = \Yii::getAlias($this->upload_web_alias);
        $this->upload_path = \Yii::getAlias($this->upload_alias);
    }

    /**
     * @return static
     */
    public static function module()
    {
        return \Yii::$app->getModule(static::MODULE);
    }

    /**
     * @param string $path
     */
    private function createDirectory($path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            FileHelper::createDirectory($path);
        }
    }

    /**
     * @param string $param
     */
    private function checkIniSizeParam($param)
    {
        if ((int)ini_get($param) < $this->max_upload_file_size) {
            \Yii::warning(sprintf('The parameter `%s` in php.ini must be equal to`%s`', $param,
                $this->max_upload_file_size . 'M'), __METHOD__);
        }
    }

    const MODULE = 'file';
}