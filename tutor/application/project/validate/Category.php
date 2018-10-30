<?php

namespace app\project\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule
        = [
            'pid|父级分类'  => 'number',
            'name|名称' => 'require|min:2|max:5|unique:categorys',
            'sort|排序' => 'number',
        ];
}