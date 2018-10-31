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
            $insert=[
                'username'=>random(mt_rand(5,10)),
                'identity'=>0,
                'head_img'=>mt_rand(1,10),
                'sex'=>mt_rand(0,1),
                'birthday'=>date('Y-m-d','14'.random(8,'number')),
                'mobile'=>'1'.random(10,'number'),
                'qq'=>random(mt_rand(5,11),'number'),
                'wechat'=>random(mt_rand(2,10)),
                'email'=>random(mt_rand(2,10)).'@'.random(mt_rand(2,5)).'.com',
                'personality_sign'=>zh_random(mt_rand(2, 20)),
                'city'=>zh_random(mt_rand(2, 8)),
                'address'=>zh_random(mt_rand(2, 20)),
                'occupation'=>zh_random(mt_rand(2, 20)),
                'hobby'=>zh_random(mt_rand(2, 20)),
                'speciality'=>zh_random(mt_rand(2, 50)),
                'password'=>random(323),
                'status'=>mt_rand(0,1),
            ];
            \app\project\model\User::create($insert);
        }
        $user = \app\project\model\User::find(1);
        $user->username    = 'sclzdj';
        $user->mobile    = '18353621790';
        $user->password    = md5(md5('sclzdj').config('custom.password_hash'));
        $user->status=1;
        $user->save();
        $user = \app\project\model\User::find(2);
        $user->username    = 'dujun';
        $user->mobile    = '17381090721';
        $user->password    = md5(md5('dujun').config('custom.password_hash'));
        $user->status=0;
        $user->save();
    }
}