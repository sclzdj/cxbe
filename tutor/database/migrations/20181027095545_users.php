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
            ->addColumn('identity', 'integer',array('limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>0,'comment'=>'身份0:学生1:老师'))
            ->addColumn('head_img', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'头像'))
            ->addColumn('sex', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>0,'comment'=>'性别0:女 1:男'))
            ->addColumn('birthday', 'date',array('comment'=>'生日'))
            ->addColumn('mobile', 'string',array('limit' => 11,'default'=>'','comment'=>'手机'))
            ->addColumn('qq', 'string',array('limit' => 191,'default'=>'','comment'=>'QQ号'))
            ->addColumn('wechat', 'string',array('limit' => 191,'default'=>'','comment'=>'微信号'))
            ->addColumn('email', 'string',array('limit' => 191,'default'=>'','comment'=>'邮箱'))
            ->addColumn('personality_sign', 'string',array('limit' => 512,'default'=>'','comment'=>'个新签名'))
            ->addColumn('city', 'string',array('limit' => 191,'default'=>'','comment'=>'所在地'))
            ->addColumn('address', 'string',array('limit' => 512,'default'=>'','comment'=>'详细地址'))
            ->addColumn('occupation', 'string',array('limit' => 191,'default'=>'','comment'=>'职业'))
            ->addColumn('hobby', 'string',array('limit' => 191,'default'=>'','comment'=>'兴趣爱好'))
            ->addColumn('speciality', 'string',array('limit' => 512,'default'=>'','comment'=>'特长'))
            ->addColumn('status', 'integer',array('signed'=>false,'limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'账号状态0:禁用 1:开启'))
            ->addColumn('create_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'创建时间'))
            ->addColumn('update_time', 'integer',array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'修改时间'))
            ->addIndex(array('username'), array('unique' => true))
            ->addIndex(array('mobile'), array('unique' => true))
            ->create();
    }
}
