<?php

use yii\db\Migration;

/**
 * Table for api test.
 *
 * Class m190226_224904_createTableTest
 */
class m190226_224904_createTableTest extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file_list_test}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'type' => $this->string()->null(),
            'size' => $this->integer()->null(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%file_list_test}}');
    }
}
