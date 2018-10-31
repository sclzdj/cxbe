<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/25
 * Time: 14:46
 */

namespace app\project\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\admin\model\Module as ModuleModel;
use app\project\model\Category as CategoryModel;
use app\user\model\Role as RoleModel;
use think\Cache;

/**
 * 分类
 * Class Category
 *
 * @package app\project\admin
 */
class Category extends Admin
{
    /**
     * 列表
     * @return mixed
     */
    public function index()
    {
        // 获取数据
        $data_list = CategoryModel::order('sort,id')->column('name,pid', 'id');
        $max_level = $this->request->get('max', 0);
        $this->assign('categorys', $this->getNest($data_list, $max_level));
        $this->assign('page_title', '分类管理');
        return $this->fetch();
    }
    /**
     * 新增
     * @param string $pid 父级ID
     *
     * @return mixed
     * @throws \think\Exception
     */
    public function add($pid = '')
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'app\project\validate\Category');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if ($category = CategoryModel::create($data)) {
                // 记录行为
                action_log('category_add', 'category', $category['id'], UID, '新增分类：'.$data['name']);
                $this->success('新增成功','index');
            } else {
                $this->error('新增失败');
            }
        }
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('新增分类')
            ->addFormItems([
                ['select', 'pid', '父级分类', '所属上级分类', CategoryModel::getCategoryTree(0, ''), $pid],
                ['text', 'name', '分类标题'],
            ])
            ->addText('sort', '排序', '', 100)
            ->fetch();
    }
    /**
     * 编辑
     * @param int $id
     *
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit($id = 0)
    {
        if ($id === 0) $this->error('缺少参数');
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'app\project\validate\Category');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if (CategoryModel::update($data)) {
                // 记录行为
                action_log('category_edit', 'category', $id, UID, '编辑分类：'.$data['name']);
                $this->success('编辑成功', 'index');
            } else {
                $this->error('编辑失败');
            }
        }
        // 获取数据
        $info = CategoryModel::get($id);
        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑分类')
            ->addFormItem('hidden', 'id')
            ->addFormItems([
                ['select', 'pid', '父级分类', '所属上级分类', CategoryModel::getCategoryTree($id, '')],
                ['text', 'name', '分类标题'],
            ])
            ->addText('sort', '排序', '', 100)
            ->setFormData($info)
            ->fetch();
    }
    /**
     * 删除
     * @param array $record
     *
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($record = [])
    {
        $id = $this->request->param('id');
        $category = CategoryModel::where('id', $id)->find();
        // 获取该所有后辈id
        $category_childs = CategoryModel::getChildsId($id);
        // 要删除的所有id
        $all_ids = array_merge([(int)$id], $category_childs);
        // 删除节点
        if (CategoryModel::destroy($all_ids)) {
            // 记录行为
            action_log('category_delete', 'category', $id, UID, '删除分类：'.$category['name']);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    /**
     * 保存排序
     */
    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!empty($data)) {
                $categorys = $this->parseData($data['data']);
                foreach ($categorys as $category) {
                    if ($category['pid'] == 0) {
                        continue;
                    }
                    CategoryModel::update($category);
                }
                $this->success('保存成功');
            } else {
                $this->error('没有需要保存的分类');
            }
        }
        $this->error('非法请求');
    }
    /**
     * 递归解析节点
     * @param array $data 数据
     * @param int $pid 父级id
     *
     * @return array 解析成可以写入数据库的格式
     */
    private function parseData($data = [], $pid = 0)
    {
        $sort   = 1;
        $result = [];
        foreach ($data as $d) {
            $result[] = [
                'id'   => (int)$d['id'],
                'pid'  => (int)$pid,
                'sort' => $sort,
            ];
            if (isset($d['children'])) {
                $result = array_merge($result, $this->parseData($d['children'], $d['id']));
            }
            $sort ++;
        }
        return $result;
    }
    /**
     * 获取嵌套式
     * @param array $lists 数据
     * @param int   $max_level 最大显示几级
     * @param int   $pid
     * @param int   $curr_level
     *
     * @return string
     */
    private function getNest($lists = [], $max_level = 0, $pid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['pid'] == $pid) {
                // 组合
                $result .= '<li class="dd-item dd3-item '.'" data-id="'.$value['id'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content">'.$value['name'];
                $result .= '<div class="action">';
                $result .= '<a href="'.url('add', ['pid' => $value['id']]).'" data-toggle="tooltip" data-original-title="新增子分类"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('edit', ['id' => $value['id']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                $result .= '<a href="'.url('delete', ['id' => $value['id'], 'table' => 'categorys']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';
                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级
                    $children = $this->getNest($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }
                $result .= '</li>';
            }
        }
        return $result;
    }
}