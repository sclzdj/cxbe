<?php

use think\migration\Migrator;
use think\migration\db\Column;

class RequestLogs extends Migrator
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
        $table = $this->table('request_logs',array('engine'=>config('custom.table_engine'),'comment'=>'接口日志'));
        $table->addColumn('url', 'text',array('comment'=>'完整接口地址'))
            ->addColumn('type', 'string',array('limit' => 191,'default'=>'','comment'=>'请求类型'))
            ->addColumn('authorization', 'text',array('comment'=>'认证凭据'))
            ->addColumn('data', 'text',array('comment'=>'请求参数'))
            ->addColumn('sign', 'string',array('limit' => 191,'default'=>'','comment'=>'签名'))
            ->addColumn('ip', 'string',array('limit' => 191,'default'=>'','comment'=>'请求ip'))
            ->addColumn('terminal', 'string',array('limit' => 191,'default'=>'','comment'=>'请求终端'))
            ->addColumn('brower', 'string',array('limit' => 191,'default'=>'','comment'=>'请求浏览器'))
            ->addColumn('agent', 'text',array('comment'=>'代理信息'))
            ->addColumn('response_code', 'string',array('limit' => 3,'default'=>'','comment'=>'返回状态码'))
            ->addColumn('response', 'text',array('comment'=>'返回信息'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->create();
    }
}
