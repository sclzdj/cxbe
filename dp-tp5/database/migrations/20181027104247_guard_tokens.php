<?php

use think\migration\Migrator;
use think\migration\db\Column;

class GuardTokens extends Migrator
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
        $table = $this->table('guard_tokens',array('engine'=>config('custom.table_engine'),'comment'=>'=守卫验证'));
        $table->addColumn('access_token', 'string',array('limit' => 323,'default'=>'','comment'=>'验证标志:必须填去掉前缀的表名'))
            ->addColumn('guard', 'string',array('limit' => 191,'default'=>'','comment'=>'守卫'))
            ->addColumn('token_type', 'string',array('limit' => 191,'default'=>'','comment'=>'类型'))
            ->addColumn('expires_in', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'有效期'))
            ->addColumn('relate_id', 'integer',array('limit' => 11,'default'=>0,'comment'=>'关联ID'))
            ->addColumn('is_forever', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>0,'comment'=>'是否永远不过期'))
            ->addColumn('is_logout', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>0,'comment'=>'是否退出'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(array('access_token'), array('unique' => true))
            ->create();
    }
}
