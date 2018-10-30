<?php

use think\migration\Migrator;
use think\migration\db\Column;

class News extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('news',array('engine'=>config('custom.table_engine'),'comment'=>'新闻'));
        $table->addColumn('title', 'string',array('limit' => 512,'default'=>'','comment'=>'标题'))
            ->addColumn('author', 'string',array('limit' => 191,'default'=>'','comment'=>'作者'))
            ->addColumn('category_id', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'分类id'))
            ->addColumn('pic', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'图片'))
            ->addColumn('content', 'text',array('comment'=>'内容'))
            ->addColumn('is_hot_recomment', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'是否热门推荐'))
            ->addColumn('click', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'阅读次数'))
            ->addColumn('status', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'状态0:隐藏 1:显示'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(array('title'))
            ->create();
    }
}
