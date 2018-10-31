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
use app\project\model\News as NewsModel;
use think\Request;

/**
 * 新闻
 * Class Category
 *
 * @package app\project\admin
 */
class News extends Admin
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
        $data_list = NewsModel::fieldLimit('content')->where($map)->order($order)->paginate(config('custom.pageSize'));
        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder构建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('新闻列表')
            ->addOrder('id,click,create_time')// 添加排序
            ->setSearch(['id' => 'ID', 'title' => '标题'],'请输入关键字','',true) // 设置搜索参数
            ->addTimeFilter('create_time')
            ->addTopSelect('status', '全部状态', ['0'=>'隐藏', '1'=>'显示'])
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['title', '标题'],
                ['author', '作者'],
                ['pic', '主图', 'picture'],
                ['category_id', '分类','callback',function($value){
                    return NewsModel::find($value)->category->name;//CategoryModel::where('id',$value)->value('name');
                }],
                ['click', '点击数'],
                ['create_time', '创建时间', 'datetime'],
                ['is_hot_recomment', '热门推荐', ['否','是']],
                ['status', '状态', 'switch'],
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
            ->setTableName('news')//  指定数据表名
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
            $result = $this->validate($data, 'app\project\validate\News');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            NewsModel::create($data);
            $this->success('新增成功','index');
        }
        return ZBuilder::make('form')
            ->setPageTitle('发布新闻')
            ->addFormItems([
                ['text', 'title', '标题','必填，长度2-20位'],
                ['select:12|12|6|6', 'category_id', '分类','必选',CategoryModel::getCategoryTree(0, false)],
                ['text:12|12|6|6', 'author', '作者','必填，长度1-8位'],
                ['image', 'pic', '主图','必传'],
                ['ueditor', 'content', '内容','必填'],
                ['radio:12|12|6|6', 'is_hot_recomment', '热门推荐', '', ['0' => '否', '1' => '是'],'0'],
                ['radio:12|12|6|6', 'status', '状态', '', ['0' => '隐藏', '1' => '显示'],'1'],
                ['text', 'click', '点击数','','0'],
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
            $result = $this->validate($data, 'app\project\validate\News');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            NewsModel::update($data);
            $this->success('修改成功','index');
        }
        $news=NewsModel::find($id);
        if(!$news){
            $this->error('请求错误');
        }
        $formData=$news;
        $formData['price']=number_format($formData['price']/100,2,'.','');
        return ZBuilder::make('form')
            ->setPageTitle('编辑新闻')
            ->setUrl(url('edit',['id'=>$id]))
            ->addFormItems([
                ['hidden','id'],
                ['text', 'title', '标题','必填，长度2-20位'],
                ['select:12|12|6|6', 'category_id', '分类','必选',CategoryModel::getCategoryTree(0, false)],
                ['text:12|12|6|6', 'author', '作者','必填，长度1-8位'],
                ['image', 'pic', '主图','必传'],
                ['ueditor', 'content', '内容','必填'],
                ['radio:12|12|6|6', 'is_hot_recomment', '热门推荐', '必选', ['0' => '否', '1' => '是'],'0'],
                ['radio:12|12|6|6', 'status', '状态', '必选', ['0' => '隐藏', '1' => '显示'],'1'],
                ['text', 'click', '点击数','','0'],
            ])
            ->setFormData($formData)
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
        NewsModel::where('id','in',$ids)->delete();
        $this->success('删除成功','index');
    }

}