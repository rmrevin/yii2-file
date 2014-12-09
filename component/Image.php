<?php
/**
 * Image.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\component;

use Imagine;

/**
 * Class Image
 * @package rmrevin\yii\module\File\component
 */
class Image extends \yii\imagine\Image
{

    /**
     * Resize an image by width.
     *
     * For example,
     *
     * ~~~
     * $obj->resizeByWidth('path\to\image.jpg', 200);
     * $obj->resizeByWidth('path\to\image.jpg', 150, \Imagine\Image\ImageInterface::FILTER_UNDEFINED);
     * ~~~
     *
     * @param string $filename the image file path or path alias.
     * @param integer $width the resize width
     * @param string $filter
     * @return \Imagine\Image\ImageInterface
     */
    public static function resizeByWidth($filename, $width, $filter = Imagine\Image\ImageInterface::FILTER_UNDEFINED)
    {
        $img = static::getImagine()
            ->open(\Yii::getAlias($filename));

        $height = $img->getSize()->getHeight() / $img->getSize()->getWidth() * $width;

        return $img
            ->copy()
            ->resize(new Imagine\Image\Box($width, $height), $filter);
    }

    /**
     * Resize an image by height.
     *
     * For example,
     *
     * ~~~
     * $obj->resizeByHeight('path\to\image.jpg', 200);
     * $obj->resizeByHeight('path\to\image.jpg', 250, \Imagine\Image\ImageInterface::FILTER_UNDEFINED);
     * ~~~
     *
     * @param string $filename the image file path or path alias.
     * @param integer $height the resize height
     * @param string $filter
     * @return \Imagine\Image\ImageInterface
     */
    public static function resizeByHeight($filename, $height, $filter = Imagine\Image\ImageInterface::FILTER_UNDEFINED)
    {
        $img = static::getImagine()
            ->open(\Yii::getAlias($filename));

        $width = $img->getSize()->getWidth() / $img->getSize()->getHeight() * $height;

        return $img
            ->copy()
            ->resize(new Imagine\Image\Box($width, $height), $filter);
    }

    /**
     * Strict resize an image.
     *
     * For example,
     *
     * ~~~
     * $obj->resize('path\to\image.jpg', 200, 200);
     * $obj->resize('path\to\image.jpg', 150, 250, \Imagine\Image\ImageInterface::FILTER_UNDEFINED);
     * ~~~
     *
     * @param string $filename the image file path or path alias.
     * @param integer $width the resize width
     * @param integer $height the resize height
     * @param string $filter
     * @return \Imagine\Image\ImageInterface
     */
    public static function resize($filename, $width, $height, $filter = Imagine\Image\ImageInterface::FILTER_UNDEFINED)
    {
        return static::getImagine()
            ->open(\Yii::getAlias($filename))
            ->copy()
            ->resize(new Imagine\Image\Box($width, $height), $filter);
    }
}