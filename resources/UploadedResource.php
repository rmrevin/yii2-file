<?php
/**
 * UploadedResource.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\resources;

use yii\helpers\FileHelper;

/**
 * Class UploadedResource
 * @package rmrevin\yii\module\File\resources
 */
class UploadedResource extends AbstractResource implements ResourceInterface
{

    /** @var \yii\web\UploadedFile */
    private $file;

    /**
     * @param \yii\web\UploadedFile $source
     */
    public function setSource($source)
    {
        $this->file = $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->file->name;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return filesize($this->file->tempName);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getMime()
    {
        return FileHelper::getMimeType($this->file->tempName);
    }

    /**
     * @return string
     */
    public function getTemp()
    {
        return $this->file->tempName;
    }
}