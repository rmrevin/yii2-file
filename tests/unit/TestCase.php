<?php
/**
 * TestCase.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\file\tests\unit;

use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class TestCase
 * @package rmrevin\yii\fontawesome\tests\unit
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    public static $params;

    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();

        $lines = explode(';', file_get_contents(\Yii::getAlias('@yiiunit/data/schema.sql')));

        foreach ($lines as $line) {
            if (trim($line) !== '') {
                \Yii::$app->getDb()->createCommand($line)->execute();
            }
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        FileHelper::removeDirectory(\Yii::$app->getModule('file')->upload_path);
        FileHelper::removeDirectory(\Yii::$app->getModule('file')->storage_path);

        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param string $appClass
     */
    protected function mockApplication($appClass = '\yii\console\Application')
    {
        // for update self::$params
        $this->getParam('id');

        /** @var \yii\console\Application $app */
        new $appClass(self::$params);
    }

    /**
     * Returns a test configuration param from /data/config.php
     * @param string $name params name
     * @param mixed $default default value to use when param is not set.
     * @return mixed the value of the configuration param
     */
    public function getParam($name, $default = null)
    {
        if (self::$params === null) {
            self::$params = require(__DIR__ . '/config/main.php');
            $main_local = __DIR__ . '/config/main-local.php';
            if (file_exists($main_local)) {
                self::$params = ArrayHelper::merge(self::$params, require($main_local));
            }
        }

        return isset(self::$params[$name]) ? self::$params[$name] : $default;
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        \Yii::$app = null;
    }
}
