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
use app\project\model\User as UserModel;
use think\Request;

/**
 * 用户
 * Class Category
 *
 * @package app\project\admin
 */
class User extends Admin
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
        $data_list = UserModel::fieldLimit()->where($map)->order($order)->paginate(config('custom.pageSize'));
        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder构建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('用户列表')
            ->addOrder('id,birthday,create_time')// 添加排序
            ->setSearch(['id' => 'ID','username' => '用户名','mobile' => '手机号'],'请输入关键字','',true) // 设置搜索参数
            ->addTimeFilter('create_time')
            ->addTopSelect('status', '全部状态', ['0'=>'隐藏', '1'=>'显示'])
            ->addFilter('sex', ['0'=>'女','1'=>'男'])
            ->addFilter('identity', ['0'=>'学生','1'=>'老师'])
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['username', '用户名'],
                ['identity', '身份', ['学生','老师']],
                ['mobile', '手机号'],
                ['head_img', '头像', 'picture'],
                ['sex', '性别', ['女','男']],
                ['birthday', '生日'],
                ['create_time', '创建时间', 'datetime'],
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
            ->setTableName('users')//  指定数据表名
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
            $result = $this->validate($data, 'app\project\validate\User');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
            if(UserModel::where(['mobile',$data['username']])->find()){
                $this->error('该手机号已被其它用户注册成用户名了');
            }
            if(UserModel::where(['username',$data['mobile']])->find()){
                $this->error('该用户名已被其它用户注册成手机号了');
            }
            $data['password']=md5(md5($data['password']).config('custom.password_hash'));
            UserModel::create($data);
            $this->success('新增成功','index');
        }
        return ZBuilder::make('form')
            ->setPageTitle('新增用户')
            ->addFormItems([
                ['text:12|12|6|6', 'username', '用户名','必填，长度5-10位'],
                ['select:12|12|6|6', 'identity', '身份','必选',['0'=>'学生','1'=>'老师'],'0'],
                ['text', 'password', '登录密码','必填，长度5-18位'],
                ['image', 'head_img', '头像',''],
                ['radio:12|12|6|6', 'sex', '性别','',['0' => '女', '1' => '男'],'0'],
                ['date:12|12|6|6', 'birthday', '生日','', '', 'yyyy-mm-dd'],
                ['text:12|12|6|6', 'mobile', '手机号','必填'],
                ['text:12|12|6|6', 'qq', 'QQ号','长度5-11位'],
                ['text:12|12|6|6', 'wechat', '微信号','长度5-20位'],
                ['text:12|12|6|6', 'email', '邮箱','长度5-50位'],
                ['text', 'personality_sign', '个性签名', '长度1-20位'],
                ['text', 'city', '所在地', '长度1-20位'],
                ['text', 'address', '详细地址', '长度1-50位'],
                ['text:12|12|6|6', 'occupation', '职业', '长度1-20位'],
                ['text:12|12|6|6', 'hobby', '兴趣爱好', '长度1-20位'],
                ['textarea', 'speciality', '特长', '长度1-50位'],
                ['radio:12|12|6|6', 'status', '状态', '', ['0' => '隐藏', '1' => '显示'],'1'],
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
            $validate=new \app\project\validate\User();
            $result = $validate->scene('edit')->check($data);
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($validate->getError());
            if(UserModel::where(['mobile',$data['username'],'id'=>['neq',$data['id']]])->find()){
                $this->error('该手机号已被其它用户注册成用户名了');
            }
            if(UserModel::where(['username',$data['mobile'],'id'=>['neq',$data['id']]])->find()){
                $this->error('该用户名已被其它用户注册成手机号了');
            }
            if($data['password']!=''){
                $data['password']=md5(md5($data['password']).config('custom.password_hash'));
            }else{
                unset($data['password']);
            }
            UserModel::update($data);
            $this->success('修改成功','index');
        }
        $user=UserModel::fieldLimit()->find($id);
        if(!$user){
            $this->error('请求错误');
        }
        $formData=$user;
        return ZBuilder::make('form')
            ->setPageTitle('编辑用户')
            ->setUrl(url('edit',['id'=>$id]))
            ->addFormItems([
                ['hidden','id'],
                ['text:12|12|6|6', 'username', '用户名','必填，长度5-10位'],
                ['select:12|12|6|6', 'identity', '身份','必选',['0'=>'学生','1'=>'老师'],'0'],
                ['text', 'password', '登录密码','不填写则不修改密码，长度5-18位'],
                ['image', 'head_img', '头像',''],
                ['radio:12|12|6|6', 'sex', '性别','',['0' => '女', '1' => '男'],'0'],
                ['date:12|12|6|6', 'birthday', '生日','', '', 'yyyy-mm-dd'],
                ['text:12|12|6|6', 'mobile', '手机号','必填'],
                ['text:12|12|6|6', 'qq', 'QQ号','长度5-11位'],
                ['text:12|12|6|6', 'wechat', '微信号','长度5-20位'],
                ['text:12|12|6|6', 'email', '邮箱','长度5-50位'],
                ['text', 'personality_sign', '个性签名', '长度1-20位'],
                ['text', 'city', '所在地', '长度1-20位'],
                ['text', 'address', '详细地址', '长度1-50位'],
                ['text:12|12|6|6', 'occupation', '职业', '长度1-20位'],
                ['text:12|12|6|6', 'hobby', '兴趣爱好', '长度1-20位'],
                ['textarea', 'speciality', '特长', '长度1-50位'],
                ['radio:12|12|6|6', 'status', '状态', '', ['0' => '隐藏', '1' => '显示'],'1'],
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
        UserModel::where('id','in',$ids)->delete();
        $this->success('删除成功','index');
    }

}