<?php
/**
 * MainTest.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\tests\unit\fontawesome;

use rmrevin\yii\module\File;

/**
 * Class MainTest
 * @package rmrevin\yii\fontawesome\tests\unit\fontawesome
 */
class MainTest extends File\tests\unit\TestCase
{

    public function testMain()
    {
        $this->assertInstanceOf('\rmrevin\yii\module\File\Module', File\Module::module());
    }
}