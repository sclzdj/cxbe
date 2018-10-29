<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 15:50
 */

namespace app\project\model;
use think\Model;

class Goods extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__GOODS__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 字段限制
     * @return Category
     */
    public static function fieldLimit(){
        return self::field('id,name,category_id,main_pic,price,stock,remark,pics,content,create_time,update_time');
    }
}