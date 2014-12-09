<?php
/**
 * InternalResource.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\component;

use yii\helpers\FileHelper;

/**
 * Class InternalResource
 * @package rmrevin\yii\module\File\component
 */
class InternalResource extends AbstractResource implements ResourceInterface
{

    /** @var string */
    private $file;

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->file = $source;

        if (!file_exists($this->file)) {
            throw new \RuntimeException('Источник ресурса недоступен');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return basename($this->file);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return filesize($this->file);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getMime()
    {
        return FileHelper::getMimeType($this->file);
    }

    /**
     * @return string
     */
    public function getTemp()
    {
        return $this->file;
    }

    public function clear()
    {
        // override deleting original file
    }
}