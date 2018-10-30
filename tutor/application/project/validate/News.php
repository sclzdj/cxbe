<?php

namespace app\project\validate;

use think\Validate;

class News extends Validate
{
    protected $rule
        = [
            'title|标题' => 'require|min:2|max:20',
            'category_id|分类' => 'require|number',
            'author|作者' => 'require|min:1|max:8',
            'pic|图片' => 'require|number',
            'content|内容' => 'require',
            'is_hot_recomment|热门推荐' => 'in:0,1',
            'status|状态' => 'in:0,1',
            'click|点击数' => 'number',
            '__token__' => 'token',
        ];
}