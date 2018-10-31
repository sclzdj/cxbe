<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\index\controller;
use app\project\model\User as UserModel;
use think\Request;
use think\Session;

/**
 * 前台登录控制器，需要登录才能执行的控制器需要继承本控制器
 * @package app\index\controller
 */
class Login extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $controller=strtolower(Request::instance()->controller());
        $action=strtolower(Request::instance()->action());
        //排除要验证登录的方法
        $except=array_map('strtolower',['index','logout','register','loginMobile']);
        //登录后不让访问的方法
        $disable=array_map('strtolower',['index','loginMobile','register']);
        if(!$this->_check()){
            //检查是否登录
            if(!($controller=='login' && in_array($action,$except))){
                if(Request::instance()->isAjax()){
                    $this->_error('未登录');
                }else{
                    $this->redirect('index/login/index');
                }
            }
        }else{
            if($controller=='login' && in_array($action,$disable)){
                if(Request::instance()->isAjax()){
                    $this->_error('错误请求');
                }else{
                    $this->redirect('index/index/index');
                }
            }
        }
    }
    /**
     * 登录
     * @return \think\response\Json|\think\response\View
     */
    public function index()
    {
        if(Request::instance()->isAjax()){
            $data = $this->request->post('', null, 'trim');
            //尝试登录
            $attempt=$this->_attempt($data);
            if($attempt['status_code']!=200){
                $this->_error($attempt);
            }
            return $this->_success($attempt);
        }
        $page_title='用户登录';
        return view('mobile/login/index',compact('page_title'));
    }
    /**
     * 短信登录
     * @return \think\response\Json|\think\response\View
     */
    public function loginMobile()
    {
        if(Request::instance()->isAjax()){
            $data = $this->request->post('', null, 'trim');
            // 验证
            $validate=new \app\project\validate\User();
            $result = $validate->scene('loginMobile')->check($data);
            // 验证失败 输出错误信息
            if(true !== $result){
                $this->_error($validate->getError());
            }
            //直接登录
            $directLogin=$this->_directLogin($data['user'],false);
            if($directLogin['status_code']!=200){
                $this->_error($directLogin);
            }
            return $this->_success($directLogin);
        }
        $page_title='用户短信登录';
        return view('mobile/login/loginMobile',compact('page_title'));
    }
    /**
     * 退出
     */
    public function logout(){
        if($this->_check()){
            Session::delete('auth_id','index');
        }
        return $this->_success(['message'=>'退出成功']);
    }
    /**
     * 注册
     * @return \think\response\Json|\think\response\View
     */
    public function register(){
        if(Request::instance()->isAjax()){
            $data = $this->request->post('', null, 'trim');
            // 验证
            $validate=new \app\project\validate\User();
            $result = $validate->scene('register')->check($data);
            // 验证失败 输出错误信息
            if(true !== $result){
                $this->_error($validate->getError());
            }
            $password=$data['password'];
            $data['password']=md5(md5($data['password']).config('custom.password_hash'));
            UserModel::create($data);
            //尝试登录
            $attempt=$this->_attempt(['user'=>$data['username'],'password'=>$password]);
            if($attempt['status_code']!=200){
                $this->_error($attempt);
            }
            return $this->_success(['message'=>'注册成功']);
        }
        $page_title='用户注册，马上跳转至个人中心';
        return view('mobile/login/register',compact('page_title'));
    }
    /**
     * 尝试登录
     * @param $username
     * @param $password
     * @return array
     */
    protected function _attempt($data){
        // 验证
        $validate=new \app\project\validate\User();
        $result = $validate->scene('login')->check($data);
        // 验证失败 输出错误信息
        if(true !== $result){
            $this->_error($validate->getError());
        }
        $user=UserModel::fieldLimit()->where('username|mobile',$data['user'])->find();
        if($user){
            Session::set('auth_id',$user['id'],'index');
            return ['message'=>'登录成功，马上跳转至个人中心','status_code'=>200];
        }
        return ['message'=>'用户丢失，登录失败','status_code'=>400];
    }
    /**
     * 直接登录
     * @param $user_id
     * @return array
     */
    protected function _directLogin($data,$type=true){
        if($type){
            $user=UserModel::fieldLimit()->find($data);
        }else{
            $user=UserModel::fieldLimit()->where('username|mobile',$data)->find();
        }
        if($user){
            Session::set('auth_id',$user['id'],'index');
            return ['message'=>'登录成功，马上跳转至个人中心','status_code'=>200];
        }
        return ['message'=>'用户丢失，登录失败','status_code'=>400];
    }
    /**
     * 获取登录用户信息
     * @return array|false|\PDOStatement|string|\think\Model
     */
    protected function _auth_user(){
        return UserModel::fieldLimit()->find(session('user_id'));
    }
    /**
     * 判断有没有登录
     * @return bool
     */
    protected function _check(){
        if(Session::has('auth_id','index')){
            return true;
        }else{
            return false;
        }
    }
}
