<?php

use think\migration\Seeder;

class News extends Seeder
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
        \think\Db::execute('truncate table '.config('database.prefix').'news');
        for($i=0;$i<200;$i++) {
            $insert = [
                'title' => zh_random(mt_rand(2, 20)),
                'category_id' => mt_rand(1, 25),
                'author' => zh_random(mt_rand(2, 8)),
                'pic' => mt_rand(1, 10),
                'click' => mt_rand(1, 10000),
                'content' => zh_random(mt_rand(100, 1000)),
                'is_hot_recomment' => mt_rand(0, 1),
                'status' => mt_rand(0, 1),
            ];
            \app\project\model\News::create($insert);
        }
    }
}