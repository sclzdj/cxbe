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
use think\Validate;

/**
 * 前台登录控制器
 * @package app\index\controller
 */
class Login extends Base
{
    public function index()
    {
        if(Request::instance()->isAjax()){
            $data = $this->request->post('', null, 'trim');
            // 验证
            $validate = new Validate([
                'user'  => 'require',
                'password' => 'require'
            ]);
            // 验证失败 输出错误信息
            if (!$validate->check($data)) {
                $this->_error($validate->getError());
            }
            $attempt=$this->_attempt($data['user'],$data['password']);
            if($attempt['status_code']!=200){
                $this->_error($attempt);
            }
            return $this->_success($attempt);
        }
        $page_title='登录';
        return view('mobile/login/index',compact('page_title'));
    }
    /**
     * 尝试登录
     * @param $username
     * @param $password
     * @return array
     */
    protected function _attempt($username,$password){
        $user=UserModel::fieldLimit()->where('username|mobile',$username)->where('password',md5(md5($password).config('custom.password_hash')))->find();
        if($user){
            session('user_id',$user['id']);
            return ['message'=>'登录成功','status_code'=>200];
        }
        return ['message'=>'账号或密码错误','status_code'=>400];
    }
    /**
     * 获取登录用户信息
     * @return array|false|\PDOStatement|string|\think\Model
     */
    protected function _user(){
        return UserModel::fieldLimit()->find(session('user_id'));
    }
}
