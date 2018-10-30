<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Users extends Migrator
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
        $table = $this->table('users',array('engine'=>config('custom.table_engine'),'comment'=>'用户'));
        $table->addColumn('username', 'string',array('limit' => 191,'default'=>'','comment'=>'用户名'))
            ->addColumn('password', 'string',array('limit' => 191,'default'=>'','comment'=>'密码'))
            ->addColumn('nickname', 'string',array('limit' => 191,'default'=>'','comment'=>'昵称'))
            ->addColumn('status', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'账号状态0:禁用 1:开启'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(array('username'), array('unique' => true))
            ->create();
    }
}
