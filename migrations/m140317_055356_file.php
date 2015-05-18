<?php

use yii\db\Schema;

class m140317_055356_file extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file}}', [
            'id' => Schema::TYPE_PK,
            'mime' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_BIGINT . ' NOT NULL DEFAULT 0',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'origin_name' => Schema::TYPE_STRING . ' NOT NULL',
            'sha1' => Schema::TYPE_STRING . '(40) NOT NULL',
            'image_bad' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
        ], $tableOptions);

        $this->createIndex('idx_sha', '{{%file}}', ['sha1']);
    }

    public function down()
    {
        $this->dropTable('{{%file}}');
    }
}