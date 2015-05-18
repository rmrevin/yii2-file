<?php
/**
 * File.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\models;

use yii\base\ModelEvent;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

/**
 * Class File
 * @package rmrevin\yii\module\File\models
 *
 * @property integer $id
 * @property string $mime
 * @property integer $size
 * @property string $name
 * @property string $origin_name
 * @property string $sha1
 * @property boolean $image_bad
 *
 * @method \rmrevin\yii\module\File\models\queries\FileQuery hasMany($class, $link)
 * @method \rmrevin\yii\module\File\models\queries\FileQuery hasOne($class, $link)
 */
class File extends \yii\db\ActiveRecord
{

    /** @var string */
    public $path;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->events();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getWebPath();
    }

    /**
     * @param bool $recalculate
     * @return integer
     */
    public function getSize($recalculate = false)
    {
        return true === $recalculate ? filesize($this->getAbsolutePath()) : $this->size;
    }

    /**
     * @param bool $recalculate
     * @return string
     * @throws \yii\base\InvalidConfigException.
     */
    public function getMime($recalculate = false)
    {
        return true === $recalculate ? FileHelper::getMimeType($this->getAbsolutePath()) : $this->mime;
    }

    /**
     * @param bool $recalculate
     * @return string
     */
    public function getSha1($recalculate = false)
    {
        return true === $recalculate ? sha1_file($this->getAbsolutePath()) : $this->sha1;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        $upload_path = \rmrevin\yii\module\File\Module::module()->upload_web_path;

        $p1 = StringHelper::byteSubstr($this->name, 0, 2);
        $p2 = StringHelper::byteSubstr($this->name, 2, 2);

        return $upload_path . '/' . $p1 . '/' . $p2 . '/' . $this->name;
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        $upload_path = \rmrevin\yii\module\File\Module::module()->upload_path;

        $p1 = StringHelper::byteSubstr($this->name, 0, 2);
        $p2 = StringHelper::byteSubstr($this->name, 2, 2);

        return $upload_path . DIRECTORY_SEPARATOR . $p1 . DIRECTORY_SEPARATOR . $p2 . DIRECTORY_SEPARATOR . $this->name;
    }

    /**
     * @param \rmrevin\yii\module\File\resources\ResourceInterface $Resource
     * @return static
     * @throws \Exception
     */
    public static function push(\rmrevin\yii\module\File\resources\ResourceInterface $Resource)
    {
        $id = basename($Resource->getTemp());
        \Yii::beginProfile('pushing file `' . $id . '`', __METHOD__);

        $NewModel = new static();
        $NewModel->path = $Resource->getTemp();

        $Model = static::find()->bySha1($Resource->getSha1())->one();

        if ($Model instanceof static) {
            $NewModel = $Model;
        } else {
            $result = $Resource->moveToUpload();
            if (false !== $result) {
                $NewModel->name = basename($result);
                $NewModel->origin_name = $Resource->getName();
                $NewModel->path = $result;
                $NewModel->insert();
            } else {
                throw new \RuntimeException(\Yii::t('service-file', 'Failed to bring the resource directory of uploaded files.'));
            }
        }
        \Yii::endProfile('pushing file `' . $id . '`', __METHOD__);

        return $NewModel;
    }

    /**
     * @return static
     */
    public static function getNoImage()
    {
        $image = \Yii::getAlias(\rmrevin\yii\module\File\Module::module()->no_image_alias);
        $Resource = new \rmrevin\yii\module\File\resources\InternalResource($image);

        return static::push($Resource);
    }

    /**
     * @return \rmrevin\yii\module\File\ImageWrapper
     * @throws \Exception
     */
    public function image()
    {
        \Yii::beginProfile('manipulating with file `' . $this->id . '`', 'services\File\models\File');

        $result = null;
        if ($this->isImage()) {
            $result = \rmrevin\yii\module\File\ImageWrapper::load($this);
        } else {
            $File = static::getNoImage();
            $result = \rmrevin\yii\module\File\ImageWrapper::load($File);
            $this->image_bad = true;
            $this->update();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return @getimagesize($this->getAbsolutePath()) !== false;
    }

    /**
     * @return \rmrevin\yii\module\File\models\queries\FileQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new queries\FileQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * Events
     */
    private function events()
    {
        $this->on(static::EVENT_BEFORE_INSERT, function (ModelEvent $Event) {
            /** @var static $Model */
            $Model = $Event->sender;
            $Model->size = filesize($Model->path);
            $Model->mime = FileHelper::getMimeType($Model->path);
            $Model->sha1 = sha1_file($Model->path);
        });
    }
}