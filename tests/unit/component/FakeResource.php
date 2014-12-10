<?php
/**
 * FakeResource.php
 * @author Roman Revin http://phptime.ru
 */

namespace rmrevin\yii\module\File\tests\unit\component;

use rmrevin\yii\module\File\component\InternalResource;

/**
 * Class FakeResource
 * @package rmrevin\yii\module\File\tests\unit\component
 */
class FakeResource extends InternalResource
{

    /**
     * @inheritdoc
     */
    public function moveToUpload()
    {
        return false;
    }
}