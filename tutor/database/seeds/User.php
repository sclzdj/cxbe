<?php

use think\migration\Seeder;

class User extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        \think\Db::execute('truncate table '.config('database.prefix').'users');
        for($i=0;$i<50;$i++){
            $insert=['username'=>random(mt_rand(2,10)),'nickname'=>zh_random(mt_rand(2,6)),'password'=>random(323),'status'=>mt_rand(0,1)];
            \app\project\model\User::create($insert);
        }
        $user = \app\project\model\User::find(1);
        $user->username    = 'sclzdj';
        $user->nickname    = '四川阆中';
        $user->password    = md5(md5('sclzdj').config('custom.password_hash'));
        $user->status=1;
        $user->save();
        $user = \app\project\model\User::find(2);
        $user->username    = 'dujun';
        $user->nickname    = '杜生';
        $user->password    = md5(md5('dujun').config('custom.password_hash'));
        $user->status=0;
        $user->save();
    }
}