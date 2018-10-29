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
use app\project\model\Category as CategoryModel;
use app\project\model\Goods as GoodsModel;
use think\Cache;
use think\Db;
use think\Request;

/**
 * 商品
 * Class Category
 *
 * @package app\project\admin
 */
class Goods extends Admin
{
    /**
     * 列表
     * @return mixed
     */
    public function index()
    {
        // 获取排序
        $order = $this->getOrder();
        if (!$order){
            $order='create_time desc';
        }
        // 获取筛选
        $map = $this->getMap();
        //读取数据
        $data_list = Db::name('goods')->where($map)->order($order)->paginate(config('custom.pageSize'));
        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder构建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('商品列表')
            ->addOrder('id,price,stock,create_time')// 添加排序
            ->setSearch(['id' => 'ID', 'name' => '名称'],'请输入关键字','',true) // 设置搜索参数
            ->addTimeFilter('create_time')
            ->addTopSelect('status', '全部状态', ['0'=>'已下架', '1'=>'已上架'])
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['name', '名称'],
                ['main_pic', '主图', 'picture'],
                ['category_id', '分类','callback',function($value){
                    return CategoryModel::where('id',$value)->value('name');
                }],
                ['price', '价格','callback',function($value){
                    return '￥'.number_format($value/100,2,'.','');
                }],
                ['stock', '库存'],
                ['create_time', '创建时间', 'datetime'],
                ['status', '状态', ['已下架', '已上架']],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons([
                'add' => ['href'  => url('add'),'title' => '新增'],
                'delete'=> ['href'  => url('delete'),'title' => '批量删除','data-title' => '真的要删除吗','data-tips' => '删除了就无法恢复了','class' => 'btn btn-danger ajax-post confirm'],

            ])// 添加编辑和删除按钮，并重新定义编辑按钮的table属性
            ->addRightButtons([
                'edit' => ['href'  => url('edit', ['id' => '__id__']),'title' => '编辑'],
                'delete'=> ['href'  => url('delete', ['id' => '__id__']),'title' => '删除','data-title' => '真的要删除吗','data-tips' => '删除了就无法恢复了','class' => 'btn btn-xs btn-default ajax-get confirm'],
            ])// 添加编辑和删除按钮，并重新定义编辑按钮的table属性
            ->setRowList($data_list)// 设置表格数据
            ->setPages($page)// 设置分页数据
            ->setTableName('goods')//  指定数据表名
            ->setHeight('auto')
            ->fetch();
    }
    /**
     * 新增
     * @return mixed
     */
    public function add()
    {
        if (Request::instance()->isPost()){
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'app\project\validate\Goods');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['price']=$data['price']*100;
            GoodsModel::create($data);
            $this->success('新增成功','index');
        }
        return ZBuilder::make('form')
            ->setPageTitle('新增商品')
            ->addFormItems([
                ['text', 'name', '名称','必填，长度2-20位'],
                ['select', 'category_id', '分类','必选',CategoryModel::getCategoryTree(0, false)],
                ['image', 'main_pic', '主图','必传'],
                ['text', 'price', '价格','必填，最多精确到分'],
                ['text', 'stock', '库存','必填'],
                ['textarea', 'remark', '描述','必填，长度2-200位'],
                ['images', 'pics', '图册'],
                ['ueditor', 'content', '详情','必填'],
                ['radio', 'status', '状态', '必选', ['0' => '下架', '1' => '上架'],'1'],
                ['text', 'sort', '排序','','100'],
            ])
            ->fetch();
    }
    /**
     * 新增
     * @param string $id
     * @return mixed
     */
    public function edit($id='')
    {
        if (Request::instance()->isPost()){
            $data = $this->request->post('', null, 'trim');
            // 验证
            $result = $this->validate($data, 'app\project\validate\Goods');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            $data['price']=$data['price']*100;
            GoodsModel::update($data);
            $this->success('修改成功','index');
        }
        $goods=GoodsModel::find($id);
        if(!$goods){
            $this->error('请求错误');
        }
        $formData=$goods;
        $formData['price']=number_format($formData['price']/100,2,'.','');
        return ZBuilder::make('form')
            ->setPageTitle('新增商品')
            ->setUrl(url('edit',['id'=>$id]))
            ->addFormItems([
                ['hidden','id'],
                ['text', 'name', '名称','必填，长度2-20位'],
                ['select', 'category_id', '分类','必选',CategoryModel::getCategoryTree(0, false)],
                ['image', 'main_pic', '主图','必传'],
                ['text', 'price', '价格','必填，最多精确到分'],
                ['text', 'stock', '库存','必填'],
                ['textarea', 'remark', '描述','必填，长度2-200位'],
                ['images', 'pics', '图册'],
                ['ueditor', 'content', '详情','必填'],
                ['radio', 'status', '状态', '必选', ['0' => '下架', '1' => '上架'],'1'],
                ['text', 'sort', '排序','','100'],
            ])
            ->setFormData($goods)
            ->fetch();
    }
    /**
     * 删除
     * @param array    $record
     * @return mixed|void
     */
    public function delete($record = [])
    {
        $ids   = Request::instance()->isPost() ? $this->request->post('ids/a') : Request::instance()->param('id');
        $ids   = (array)$ids;
        empty($ids) && $this->error('缺少主键');
        GoodsModel::where('id','in',$ids)->delete();
        $this->success('删除成功','index');
    }

}