<?php
/**
 * FileQuery.php
 * @author Revin Roman http://phptime.ru
 */

namespace rmrevin\yii\module\File\models\queries;

/**
 * Class FileQuery
 * @package rmrevin\yii\module\File\models\queries
 */
class FileQuery extends \yii\db\ActiveQuery
{

    /**
     * @param integer|array $id
     * @return static
     */
    public function byId($id)
    {
        $this->andWhere(['id' => $id]);

        return $this;
    }

    /**
     * @param string|array $hash
     * @return static
     */
    public function bySha1($hash)
    {
        $this->andWhere(['sha1' => $hash]);

        return $this;
    }
}