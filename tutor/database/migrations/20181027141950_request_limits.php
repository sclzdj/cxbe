<?php

use think\migration\Migrator;
use think\migration\db\Column;

class RequestLimits extends Migrator
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
        $table = $this->table('request_limits',array('engine'=>config('custom.table_engine'),'comment'=>'接口日志'));
        $table->addColumn('ip', 'string',array('limit' => 191,'default'=>'','comment'=>'请求ip'))
            ->addColumn('module', 'string',array('limit' => 191,'default'=>'','comment'=>'请求模块'))
            ->addColumn('home', 'string',array('limit' => 191,'default'=>'','comment'=>'请求控制器'))
            ->addColumn('action', 'string',array('limit' => 191,'default'=>'','comment'=>'请求动作'))
            ->addColumn('method', 'string',array('limit' => 191,'default'=>'','comment'=>'请求方法'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(['ip'])
            ->create();
    }
}
