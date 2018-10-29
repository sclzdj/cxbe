<?php

use think\migration\Seeder;

class Goods extends Seeder
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
                'name'=>zh_random(mt_rand(2,10)),
                'category_id'=>mt_rand(1,125),
                'main_pic'=>mt_rand(1,10),
                'price'=>mt_rand(1,10000),
                'stock'=>mt_rand(1,10000),
                'remark'=>zh_random(mt_rand(10,100)),
                'pics'=>mt_rand(1,3).','.mt_rand(4,6).','.mt_rand(7,10),
                'content'=>zh_random(mt_rand(100,1000)),
                'status'=>mt_rand(0,1),
            ];
            \app\project\model\Goods::create($insert);
        }

    }
}