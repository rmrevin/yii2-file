<?php
/**
 * AbstractResource.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\resources;

use rmrevin\yii\module\File;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

/**
 * Class AbstractResource
 * @package rmrevin\yii\module\File\resources
 */
abstract class AbstractResource implements ResourceInterface
{

    /**
     * @param string|boolean $source
     */
    public function __construct($source)
    {
        $this->setSource($source);
    }

    /**
     * @return string
     */
    public function getSha1()
    {
        return sha1_file($this->getTemp());
    }

    /**
     * @return string|false
     */
    public function moveToUpload()
    {
        $filename = sha1(microtime());
        $ext = pathinfo($this->getName(), PATHINFO_EXTENSION);

        $upload_path = File\Module::module()->upload_path;
        $p1 = StringHelper::byteSubstr($filename, 0, 2);
        $p2 = StringHelper::byteSubstr($filename, 2, 2);
        $path = $upload_path . DIRECTORY_SEPARATOR . $p1 . DIRECTORY_SEPARATOR . $p2;
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }

        $file_path = $path . DIRECTORY_SEPARATOR . $filename . '.' . $ext;

        $result = copy($this->getTemp(), $file_path);
        $this->clear();

        chmod($file_path, 0664);

        return $result === true ? $file_path : false;
    }

    public function clear()
    {
        unlink($this->getTemp());
    }
}