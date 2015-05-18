<?php
/**
 * ImageWrapper.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File;

use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use rmrevin\yii\module\File\component\Image;
use rmrevin\yii\module\File\models\File;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;

/**
 * Class ImageWrapper
 * @package rmrevin\yii\module\File
 */
class ImageWrapper extends \yii\base\Object
{

    /** @var \rmrevin\yii\module\File\models\File */
    public $File = null;

    /** @var array */
    public $result = [null, null];

    /** @var array */
    private $mark = [];

    /**
     * @param \rmrevin\yii\module\File\models\File $File
     * @return self
     */
    public static function load(File $File)
    {
        return new self(['File' => $File]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->result[1];
    }

    /**
     * @param callable $handler
     */
    public function save($handler)
    {
        $mark = $this->calculateMark();

        $this->result = $this->getMarkedFilePath($mark);

        if (!file_exists($this->result[0])) {
            \Yii::trace('create new file cache:' . $this->File->id, __METHOD__);
            $this->createMarkedFile($handler(), $mark);
        } else {
            \Yii::trace('file already cached:' . $this->File->id . ' (' . Json::encode($this->mark) . ')', __METHOD__);
        }

        \Yii::endProfile('manipulating with file `' . $this->File->id . '`', 'services\File\models\File');
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param string $filter
     * @return self
     */
    public function resize($width, $height, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        \Yii::trace('resize file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($width, $height, $filter) {
            return Image::resize($this->File->getAbsolutePath(), $width, $height, $filter);
        });

        return $this;
    }

    /**
     * @param integer $width
     * @param string $filter
     * @return self
     */
    public function resizeByWidth($width, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        \Yii::trace('resizeByWidth file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($width, $filter) {
            return Image::resizeByWidth($this->File->getAbsolutePath(), $width, $filter);
        });

        return $this;

    }

    /**
     * @param integer $height
     * @param string $filter
     * @return self
     */
    public function resizeByHeight($height, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        \Yii::trace('resizeByHeight file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($height, $filter) {
            return Image::resizeByHeight($this->File->getAbsolutePath(), $height, $filter);
        });

        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param array $start
     * @return self
     */
    public function crop($width, $height, array $start = [0, 0])
    {
        \Yii::trace('crop file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($width, $height, $start) {
            return Image::crop($this->File->getAbsolutePath(), $width, $height, $start);
        });

        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param string $mode
     * @return self
     */
    public function thumbnail($width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
    {
        \Yii::trace('thumbnail file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($width, $height, $mode) {
            return Image::thumbnail($this->File->getAbsolutePath(), $width, $height, $mode);
        });

        return $this;
    }

    /**
     * @param string $watermarkFilename
     * @param array $start
     * @return self
     */
    public function watermark($watermarkFilename, array $start = [0, 0])
    {
        \Yii::trace('watermark file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($watermarkFilename, $start) {
            return Image::watermark($this->File->getAbsolutePath(), $watermarkFilename, $start);
        });

        return $this;
    }

    /**
     * @param string $text
     * @param string $fontFile
     * @param array $start
     * @param array $fontOptions
     * @return self
     */
    public function text($text, $fontFile, array $start = [0, 0], array $fontOptions = [])
    {
        \Yii::trace('text file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($text, $fontFile, $start, $fontOptions) {
            return Image::text($this->File->getAbsolutePath(), $text, $fontFile, $start, $fontOptions);
        });

        return $this;
    }

    /**
     * @param int $margin
     * @param string $color
     * @param int $alpha
     * @return self
     */
    public function frame($margin = 20, $color = '666', $alpha = 100)
    {
        \Yii::trace('frame file', __METHOD__);

        $this->mark(__METHOD__, func_get_args());
        $this->save(function () use ($margin, $color, $alpha) {
            return Image::frame($this->File->getAbsolutePath(), $margin, $color, $alpha);
        });

        return $this;
    }

    /**
     * @return string[]
     */
    private function getPath()
    {
        $filename = basename($this->File->name);
        $p1 = StringHelper::byteSubstr($filename, 0, 2);
        $p2 = StringHelper::byteSubstr($filename, 2, 2);
        $p = DIRECTORY_SEPARATOR . $p1 . DIRECTORY_SEPARATOR . $p2;

        return [
            Module::module()->storage_path . $p,
            Module::module()->storage_web_path . $p
        ];
    }

    /**
     * @param string $mark
     * @return string[]
     */
    private function getMarkedFilePath($mark)
    {
        \Yii::trace('calculate mark file path', __METHOD__);

        $ext = pathinfo($this->File->name, PATHINFO_EXTENSION);

        list($path, $web_path) = $this->getPath();
        $mark_file_path = $path . DIRECTORY_SEPARATOR . $mark . '.' . $ext;
        $mark_file_web_path = $web_path . '/' . $mark . '.' . $ext;

        return [$mark_file_path, $mark_file_web_path];
    }

    /**
     * @param \Imagine\Image\ImageInterface $Image
     * @param string $mark
     */
    private function createMarkedFile(ImageInterface $Image, $mark)
    {
        \Yii::beginProfile('create cache file:' . $this->File->id, __METHOD__);

        list($mark_file_path, $mark_file_web_path) = $this->getMarkedFilePath($mark);

        $mark_dir_path = dirname($mark_file_path);
        if (!file_exists($mark_dir_path) || !is_dir($mark_dir_path)) {
            FileHelper::createDirectory($mark_dir_path);
        }

        $Image->save($mark_file_path, ['quality' => 90]);
        @chmod($mark_file_path, 0664);

        \Yii::endProfile('create cache file:' . $this->File->id, __METHOD__);
    }


    /**
     * @param string $method
     * @param array $data
     */
    private function mark($method, array $data)
    {
        $this->mark = func_get_args();
    }

    /**
     * @return string
     */
    private function calculateMark()
    {
        return sha1(Json::encode($this->mark));
    }
}