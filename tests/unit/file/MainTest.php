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

    /**
     * @expectedException \RuntimeException
     */
    public function testBadInternalResource()
    {
        new File\component\InternalResource('unknown file');
    }

    public function testExternalResource()
    {
        $Resource = new File\component\ExternalResource('https://www.google.ru/images/srpr/logo11w.png');

        $this->assertEquals($Resource->getMime(), 'image/png');
        $this->assertEquals($Resource->getSize(), 14022);
        $this->assertNotEmpty($Resource->getTemp());

        $File = File\models\File::push($Resource);

        $this->assertInstanceOf('\rmrevin\yii\module\File\models\File', $File);

        $Image = $File->image();

        $this->assertInstanceOf('\rmrevin\yii\module\File\ImageWrapper', $Image);
    }

    public function testInternalResource()
    {
        $file = \Yii::getAlias('@yiiunit/data/phptime.ru.png');
        $Resource = new File\component\InternalResource($file);

        $this->assertEquals($Resource->getMime(), 'image/png');
        $this->assertEquals($Resource->getSize(), 5873);
        $this->assertNotEmpty($Resource->getTemp());

        $File = File\models\File::push($Resource);

        $this->assertInstanceOf('\rmrevin\yii\module\File\models\File', $File);

        $Image = $File->image();

        $this->assertInstanceOf('\rmrevin\yii\module\File\ImageWrapper', $Image);

        return $Image;
    }

    /**
     * @depends testInternalResource
     * @param \rmrevin\yii\module\File\ImageWrapper $Image
     */
    public function testManipulations(File\ImageWrapper $Image)
    {
        list($absolute_path, $web_path) = $Image->crop(10, 20)->result;

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);

        $this->assertTrue(file_exists($absolute_path));

        $size = getimagesize($absolute_path);
        $this->assertNotEmpty($size);
        $this->assertEquals($size[0], 10);
        $this->assertEquals($size[1], 20);

        $this->assertEquals((string)$Image->crop(10, 20), $web_path);

        list($absolute_path, $web_path) = $Image->resize(30, 40)->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 30);
        $this->assertEquals($size[1], 40);

        list($absolute_path, $web_path) = $Image->resizeByWidth(50)->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 50);
        $this->assertEquals($size[1], 50);

        list($absolute_path, $web_path) = $Image->resizeByHeight(100)->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 100);
        $this->assertEquals($size[1], 100);

        list($absolute_path, $web_path) = $Image->thumbnail(120, 120)->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 120);
        $this->assertEquals($size[1], 120);

        list($absolute_path, $web_path) = $Image->text('test', \Yii::getAlias('@yiiunit/data/DejaVuSans.ttf'))->result;
        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);

        list($absolute_path, $web_path) = $Image->watermark(\Yii::getAlias('@yiiunit/data/watermark.png'))->result;
        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);

        list($absolute_path, $web_path) = $Image->frame()->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 190);
        $this->assertEquals($size[1], 190);
    }
}