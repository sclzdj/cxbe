<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 15:50
 */

namespace app\project\model;
use think\Model;
use think\Exception;
use util\Tree;

class Category extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CATEGORYS__';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 字段限制
     * @return Category
     */
    public static function fieldLimit(){
        return self::field('id,name,pid,create_time,update_time');
    }
    /**
     * 获取树形节点
     * @param int $id 需要隐藏的节点id
     * @param string $default 默认第一个节点项，默认为“顶级节点”，如果为false则不显示，也可传入其他名称
     * @return mixed
     */
    public static function getCategoryTree($id = 0, $default = '')
    {
        $result[0]       = '顶级分类';
        $where=[];
        // 排除指定节点及其子节点
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], self::getChildsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        
        // 获取节点
        $Tree=new Tree([
            'id'    => 'id',    // id名称
            'pid'   => 'pid',   // pid名称
            'title' => 'name', // 标题名称
            'child' => 'child', // 子元素键名
            'html'  => '┝ ',   // 层级标记
            'step'  => 4,       // 层级步进数量
        ]);
        $categorys = $Tree::toList(self::where($where)->order('pid,id')->column('id,pid,name'));
        foreach ($categorys as $category) {
            $result[$category['id']] = $category['title_display'];
        }
        
        // 设置默认节点项标题
        if ($default != '') {
            $result[0] = $default;
        }
        
        // 隐藏默认节点项
        if ($default === false) {
            unset($result[0]);
        }
        
        return $result;
    }
    
    /**
     * 获取所有子节点id
     * @param int $pid 父级id
     * @return array
     */
    public static function getChildsId($pid = 0)
    {
        $ids = self::where('pid', $pid)->column('id');
        foreach ($ids as $value) {
            $ids = array_merge($ids, self::getChildsId($value));
        }
        return $ids;
    }
    
    /**
     * 获取所有父节点id
     * @param int $id 节点id
     * @return array
     */
    public static function getParentsId($id = 0)
    {
        $pid  = self::where('id', $id)->value('pid');
        $pids = [];
        if ($pid != 0) {
            $pids[] = $pid;
            $pids = array_merge($pids, self::getParentsId($pid));
        }
        return $pids;
    }
    
    /**
     * 根据节点id获取上下级的所有id
     * @param int $id 节点id
     * @return array
     */
    public static function getLinkIds($id = 0)
    {
        $childs  = self::getChildsId($id);
        $parents = self::getParentsId($id);
        return array_merge((array)(int)$id, $childs, $parents);
    }
}