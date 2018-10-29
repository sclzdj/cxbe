<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Goods extends Migrator
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
        // create the table
        $table = $this->table('goods',array('engine'=>config('custom.table_engine'),'comment'=>'商品'));
        $table->addColumn('name', 'string',array('limit' => 191,'default'=>'','comment'=>'名称'))
            ->addColumn('category_id', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'分类id'))
            ->addColumn('main_pic', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'主图'))
            ->addColumn('price', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'价格，单位：分'))
            ->addColumn('stock', 'integer',array('signed'=>false,'limit' => 10,'default'=>0,'comment'=>'总库存'))
            ->addColumn('remark', 'string',array('limit' => 512,'default'=>'','comment'=>'描述'))
            ->addColumn('pics', 'text',array('comment'=>'图集'))
            ->addColumn('content', 'text',array('comment'=>'详情'))
            ->addColumn('status', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'状态0:下架 1:上架'))
            ->addColumn('sort', 'integer',array('signed'=>false,'limit' => 11,'default'=>100,'comment'=>'排序，升序取数据'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(array('name'), array('unique' => true))
            ->create();
    }
}
