<?php
/**
 * ResourceInterface.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\component;

/**
 * Interface ResourceInterface
 * @package rmrevin\yii\module\File\component
 */
interface ResourceInterface
{

    /**
     * @param mixed $source
     */
    public function setSource($source);

    public function getName();

    public function getSize();

    public function getMime();

    public function getTemp();

    public function getSha1();

    public function moveToUpload();
}