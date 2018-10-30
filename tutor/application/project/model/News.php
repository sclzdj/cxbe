<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 15:50
 */

namespace app\project\model;
use think\Model;

class News extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__NEWS__';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 字段限制
     */
    public static function fieldLimit($expect=''){
        $fields=['id','title','category_id','pic','author','is_hot_recomment','click','content','status','create_time','update_time'];
        if($expect!==''){
            $expect=is_array($expect)?$expect:explode(',',$expect);
            $fields=array_diff($fields,$expect);
        }
        return self::field($fields);
    }
    /**
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('app\project\model\Category');
    }
}