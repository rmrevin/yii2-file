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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        ini_set('upload_max_filesize', $this->max_upload_file_size . 'M');
        ini_set('post_max_size', $this->max_upload_file_size . 'M');

        if ((int)ini_get('upload_max_filesize') < $this->max_upload_file_size) {
            \Yii::warning(sprintf('Параметр `%s` в php.ini должен быть равен `%s`', 'upload_max_filesize',
                $this->max_upload_file_size . 'M'), __METHOD__);
        }

        if ((int)ini_get('post_max_size') < $this->max_upload_file_size) {
            \Yii::warning(sprintf('Параметр `%s` в php.ini должен быть равен `%s`', 'post_max_size',
                $this->max_upload_file_size . 'M'), __METHOD__);
        }

        if (empty($this->storage_path) && !empty($this->storage_alias)) {
            $this->storage_path = \Yii::getAlias($this->storage_alias);
        }

        if (empty($this->storage_web_path) && !empty($this->storage_web_alias)) {
            $this->storage_web_path = \Yii::getAlias($this->storage_web_alias);
        }

        if (empty($this->upload_web_path) && !empty($this->upload_web_alias)) {
            $this->upload_web_path = \Yii::getAlias($this->upload_web_alias);
        }

        if (empty($this->upload_path) && !empty($this->upload_alias)) {
            $this->upload_path = \Yii::getAlias($this->upload_alias);
        }

        if (empty($this->upload_path)) {
            throw new InvalidConfigException('Не удалось определить путь к директории с загруженными файлами (FileService::$upload_path).');
        }

        if (!file_exists($this->upload_path)) {
            FileHelper::createDirectory($this->upload_path);
        }

        if (!is_readable($this->upload_path)) {
            throw new \RuntimeException('Директория с загруженными файлами не доступна для чтения (FileService::$upload_path).');
        }

        if (!is_writable($this->upload_path)) {
            throw new \RuntimeException('Директория с загруженными файлами не доступна для записи (FileService::$upload_path).');
        }

        if (empty($this->storage_path)) {
            throw new InvalidConfigException('Не удалось определить путь к директории с файловым кэшем (FileService::$storage_path).');
        }

        if (!file_exists($this->storage_path)) {
            FileHelper::createDirectory($this->storage_path);
        }

        if (!is_readable($this->storage_path)) {
            throw new \RuntimeException('Директория с файловым кэшем не доступна для чтения (FileService::$storage_path).');
        }

        if (!is_writable($this->storage_path)) {
            throw new \RuntimeException('Директория с файловым кэшем не доступна для записи (FileService::$storage_path).');
        }
    }

    /**
     * @return static
     */
    public static function module()
    {
        return \Yii::$app->getModule(static::MODULE);
    }

    const MODULE = 'file';
}