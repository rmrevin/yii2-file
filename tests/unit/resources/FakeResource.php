<?php
/**
 * FakeResource.php
 * @author Roman Revin http://phptime.ru
 */

namespace rmrevin\yii\module\File\tests\unit\resources;

/**
 * Class FakeResource
 * @package rmrevin\yii\module\File\tests\unit\resources
 */
class FakeResource extends \rmrevin\yii\module\File\resources\InternalResource
{

    /**
     * @inheritdoc
     */
    public function moveToUpload()
    {
        return false;
    }
}