<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cms_item_comments}}`.
 */
class m210812_133732_create_cms_item_comments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
      
        $this->createTable('{{%cms_item_comments}}', [
            'id' => $this->primaryKey(),
            'vote' => $this->integer(),
            'text' => $this->text(),
            'item_id' => $this->integer(),
            'username' => $this->string(255),
            'user_id' => $this->integer(),
            'parent_id' => $this->integer(),
            'level' => $this->tinyInteger(),
            'status' => $this->tinyInteger(),
            'created_at' => $this->integer()->defaultValue(time()),
            'updated_at' => $this->integer()->defaultValue(time()),
        ], $tableOptions);

        $this->createIndex('index-cms_item_comments-cms_item_id', 'cms_item_comments', 'item_id');
        $this->addForeignKey('fkey-cms_item_comments-cms_item_id', 'cms_item_comments', 'item_id', 'cms_items', 'id', 'SET NULL', 'RESTRICT');

        $this->createIndex('index-cms_item_comments-parent_id', 'cms_item_comments', 'parent_id');
        $this->addForeignKey('fkey-cms_item_comments-parent_id', 'cms_item_comments', 'parent_id', 'cms_item_comments', 'id', 'SET NULL', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cms_item_comments}}');
    }
}
