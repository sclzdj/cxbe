<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 15:50
 */

namespace app\project\model;
use think\Model;

class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__USERS__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 字段限制
     * @return Category
     */
    public static function fieldLimit($expect=''){
        $fields=['id','username','identity','head_img','sex','birthday','mobile','qq','wechat','email','personality_sign','city','address','occupation','hobby','speciality','status','create_time','update_time'];
        if($expect!==''){
            $expect=is_array($expect)?$expect:explode(',',$expect);
            $fields=array_diff($fields,$expect);
        }
        return self::field($fields);
    }
}