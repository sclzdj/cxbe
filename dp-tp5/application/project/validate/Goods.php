<?php

namespace app\project\validate;

use think\Request;
use think\Validate;

class Goods extends Validate
{
    protected $rule
        = [
            'name' => 'require|min:2|max:20|unique:goods',
            'main_pic' => 'require|number',
            'category_id' => 'number',
            'remark' => 'require|min:2|max:200',
            'price' => 'require|regex:/^\d+(\.\d{1,2})?$/',
            'stock' => 'require|number',
            'content' => 'require',
            'status' => 'require|in:0,1',
            'sort' => 'number',
            '__token__' => 'token',
        ];
    protected $message
        = [
            'name.require' => '名称不能为空',
            'name.min' => '名称最小2位',
            'name.max' => '名称最大20位',
            'name.unique' => '名称不能重复',
            'main_pic.require' => '主图必传',
            'category_id.require' => '分类必选',
            'remark.require' => '描述不能为空',
            'price.require' => '价格不能为空',
            'price.regex' => '价格最多只能精确到分',
            'stock.require' => '库存不能为空',
            'stock.number' => '库存应该是个自然数',
            'content.require' => '内容不能为空',
            'status.require' => '状态必选',
        ];
}