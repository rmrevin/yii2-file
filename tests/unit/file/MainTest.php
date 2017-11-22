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
        $Module = File\Module::module();

        $this->assertInstanceOf(File\Module::className(), $Module);
        $this->assertNotEmpty($Module->upload_alias);
        $this->assertNotEmpty($Module->upload_path);
        $this->assertNotEmpty($Module->upload_web_alias);
        $this->assertNotEmpty($Module->upload_web_path);
        $this->assertNotEmpty($Module->storage_alias);
        $this->assertNotEmpty($Module->storage_path);
        $this->assertNotEmpty($Module->storage_web_alias);
        $this->assertNotEmpty($Module->storage_web_path);
        $this->assertNotEmpty($Module->max_upload_file_size);

        $this->assertTrue(file_exists($Module->upload_path));
        $this->assertTrue(is_dir($Module->upload_path));
        $this->assertTrue(file_exists($Module->storage_path));
        $this->assertTrue(is_dir($Module->storage_path));
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testBadUploadPath()
    {
        $Module = clone  File\Module::module();

        $Module->upload_alias = null;
        $Module->upload_path = null;
        $Module->init();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotReadableUploadPath()
    {
        $Module = clone File\Module::module();

        $Module->upload_alias = '/root';
        $Module->upload_path = '/root';
        $Module->init();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotWritableUploadPath()
    {
        $Module = clone  File\Module::module();

        $Module->upload_alias = '/usr';
        $Module->upload_alias = '/usr';
        $Module->init();
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testBadStoragePath()
    {
        $Module = clone File\Module::module();

        $Module->storage_alias = null;
        $Module->storage_path = null;
        $Module->init();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotReadableStoragePath()
    {
        $Module = clone File\Module::module();

        $Module->storage_alias = '/root';
        $Module->storage_path = '/root';
        $Module->init();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotWritableStoragePath()
    {
        $Module = clone File\Module::module();

        $Module->storage_alias = '/usr';
        $Module->storage_path = '/usr';
        $Module->init();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadInternalResource()
    {
        new File\component\InternalResource('unknown file');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadExternalResource()
    {
        new File\component\ExternalResource('unknown file');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadMoveTempFile()
    {
        $file = \Yii::getAlias('@yiiunit/data/for-move.txt');
        File\models\File::push(new File\tests\unit\resources\FakeResource($file));
    }

    public function testModel()
    {
        $File = new File\models\File();
        $this->assertInstanceOf(File\models\File::className(), $File);

        $FileNoImage = File\models\File::getNoImage();
        $this->assertInstanceOf(File\models\File::className(), $FileNoImage);
        $this->assertTrue($FileNoImage->isImage());
        $this->assertNotEmpty((string)$FileNoImage);
        $this->assertNotEmpty($FileNoImage->getWebPath());
        $this->assertNotEmpty($FileNoImage->getAbsolutePath());
        $this->assertEquals($FileNoImage->getSha1(true), '32e4319511b2e71471dd4a92c770eae7d5bece79');
        $this->assertEquals($FileNoImage->getSha1(false), '32e4319511b2e71471dd4a92c770eae7d5bece79');
        $this->assertEquals($FileNoImage->getSize(true), 21967);
        $this->assertEquals($FileNoImage->getSize(false), 21967);
        $this->assertEquals($FileNoImage->getMime(true), 'image/png');
        $this->assertEquals($FileNoImage->getMime(false), 'image/png');

        $FileNoImage = File\models\File::getNoImage();
        $this->assertInstanceOf(\rmrevin\yii\module\File\models\File::className(), $FileNoImage);

        $file = \Yii::getAlias('@yiiunit/data/text.txt');
        $File = File\models\File::push(new File\resources\InternalResource($file));
        $this->assertInstanceOf(File\models\File::className(), $File);
        $this->assertFalse((bool)$File->image_bad);
        $this->assertInstanceOf(File\ImageWrapper::className(), $File->image());
        $this->assertTrue((bool)$File->image_bad);

        $File->refresh();

        /** @var File\models\File $F */
        $F = File\models\File::find()
            ->byId($File->id)
            ->one();

        $this->assertEquals($F->getAbsolutePath(), $File->getAbsolutePath());

        /** @var File\models\File $F */
        $F = File\models\File::find()
            ->bySha1($File->sha1)
            ->one();

        $this->assertEquals($F->getAbsolutePath(), $File->getAbsolutePath());
    }

    public function testExternalResource()
    {
        $Resource = new File\resources\ExternalResource('https://en.wikipedia.org/static/images/project-logos/enwiki.png');

        $this->assertEquals($Resource->getMime(), 'image/png');
        $this->assertEquals($Resource->getSize(), 20616);
        $this->assertNotEmpty($Resource->getTemp());

        $File = File\models\File::push($Resource);

        $this->assertInstanceOf(File\models\File::className(), $File);

        $Image = $File->image();

        $this->assertInstanceOf(File\ImageWrapper::className(), $Image);
    }

    public function testInternalResource()
    {
        $file = \Yii::getAlias('@yiiunit/data/phptime.ru.png');
        $Resource = new File\component\InternalResource($file);

        $this->assertEquals($Resource->getMime(), 'image/png');
        $this->assertEquals($Resource->getSize(), 5873);
        $this->assertNotEmpty($Resource->getTemp());

        $File = File\models\File::push($Resource);

        $this->assertInstanceOf(File\models\File::className(), $File);

        $Image = $File->image();

        $this->assertInstanceOf(File\ImageWrapper::className(), $Image);
    }

    public function testUploadedResource()
    {
        $file = \Yii::getAlias('@yiiunit/data/uploaded.txt');

        $temp = tempnam(sys_get_temp_dir(), 'f');

        copy($file, $temp);

        $_FILES = [
            'file' => [
                'name' => 'uploaded.txt',
                'type' => 'text/plain',
                'tmp_name' => $temp,
                'error' => 0,
                'size' => filesize($file),
            ],
        ];

        $UploadedFile = \yii\web\UploadedFile::getInstanceByName('file');
        $Resource = new File\resources\UploadedResource($UploadedFile);

        $this->assertEquals($Resource->getMime(), 'text/plain');
        $this->assertEquals($Resource->getSize(), 21);
        $this->assertNotEmpty($Resource->getTemp());

        $File = File\models\File::push($Resource);
        $this->assertInstanceOf(File\models\File::className(), $File);
    }

    public function testManipulations()
    {
        $Image = File\models\File::getNoImage()->image();

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

        list($absolute_path, $web_path) = $Image->watermark(\Yii::getAlias('@yiiunit/data/watermark.png'))->result;
        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);

        list($absolute_path, $web_path) = $Image->frame()->result;
        $size = getimagesize($absolute_path);

        $this->assertNotEmpty($absolute_path);
        $this->assertNotEmpty($web_path);
        $this->assertEquals($size[0], 552);
        $this->assertEquals($size[1], 552);
    }
}