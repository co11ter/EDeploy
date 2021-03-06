<?php

use yii\db\Schema;
use yii\db\Migration;

class m141212_110359_User extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . ' NOT NULL',
            'is_admin' => "enum('1','0') NOT NULL DEFAULT '0'"
        ], $tableOptions);

        // create default user
        $this->insert('{{%user}}', [
            'username'      => 'admin',
            'password'      => '$2y$13$HYg5DZXSIkzUmdYUwNSTW.pqqsmrOeQdgrQcoeBpjkvdn3UFKxdmO',
            'auth_key'      => md5(str_shuffle('admin')),
            'is_admin'      => '1'
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
