<?php
/**
 * ResourceInterface.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\resources;

/**
 * Interface ResourceInterface
 * @package rmrevin\yii\module\File\resources
 */
interface ResourceInterface
{

    /**
     * @param mixed $source
     */
    public function setSource($source);

    /**
     * @return string
     */
    public function getName();

    public function getSize();

    public function getMime();

    /**
     * @return string
     */
    public function getTemp();

    /**
     * @return string
     */
    public function getSha1();

    /**
     * @return string|false
     */
    public function moveToUpload();
}