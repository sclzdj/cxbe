<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/27
 * Time: 14:23
 */

namespace app\api\home;

use think\Db;
use think\Request;
use think\Validate;

class AuthUsers extends Base
{
    protected $_guard="users";
    protected $_token_type="bearer";
    
    protected $_username="username";
    protected $_password="password";
    
    protected $_auth_id;
    
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        
        $except=['login'];
        if (!in_array(Request::instance()->action(),$except)){
            $check=$this->_check(true);
            if(!is_numeric($check)){
                $this->_error($check);
            }
        }
    }
    /**
     * 登录接口
     * @return \think\response\Json
     */
    public function login(){
        $username=Request::instance()->param($this->_username);
        $password=Request::instance()->param($this->_password);
        $data = compact('username','password');
        $validate = new Validate([
            $this->_username  => 'require',
            $this->_password => 'require'
        ],[
            $this->_username.'.require'=>'账号不能为空',
            $this->_password.'.require'=>'密码不能为空',
        ]);
        if (!$validate->check($data)) {
            $this->_error([
                'message'=>$validate->getError(),
                'status_code'=>400,
            ]);
        }
        $attempt=$this->_attempt($username,$password,true);
        if($attempt['status_code']!=200){
            $this->_error($attempt);
        }
        return $this->_success($attempt);
    }
    /**
     * 退出接口
     * @return \think\response\Json
     */
    public function logout(){
        $check=$this->_check(true);
        if(is_numeric($check)){
            Db::name('guard_tokens')->where('id',$check)->update([
                'is_logout'=>1,
                'update_time'=>time(),
            ]);
            return $this->_success([
                'message'=>'退出成功',
                'status_code'=>200
            ]);
        }else{
            $this->_error($check);
        }
    }
    /**
     * 是否登录
     * @return array|bool
     */
    protected function _check($status=false){
        $authorization = Request::instance()->header('authorization');
        $authorization=explode(' ',$authorization);
        if(count($authorization)==2){
            if(strtolower($authorization[0])==strtolower($this->_token_type)){
                $guard_token=Db::name('guard_tokens')->where([
                    'access_token'=>$authorization[1],
                    'is_logout'=>0,
                    'token_type'=>$this->_token_type,
                    'guard'=>$this->_guard
                ])->find();
                if($guard_token){
                    if($guard_token['is_forever']==1 || $guard_token['create_time']+$guard_token['expires_in']>time()){
                        $user=Db::name($guard_token['guard'])->find($guard_token['relate_id']);
                        if($user){
                            if($status && $user['status']==1){
                                $this->_auth_id=$user['id'];
                                return $guard_token['id'];
                            }else{
                                return [
                                    'message'=>'该用户已禁用',
                                    'status_code'=>400,
                                ];
                            }
                        }else{
                            return [
                                'message'=>'该用户已丢失',
                                'status_code'=>400,
                            ];
                        }
                    }
                }
            }
        }
        return [
            'message'=>'token认证出错',
            'status_code'=>401,
        ];
    }
    /**
     * @return mixed
     */
    protected function _auth_id(){
        return $this->_auth_id;
    }
    /**
     * 尝试登录
     * @param      $username
     * @param      $password
     * @param bool $stauts  是否验证用户禁用状态
     *
     * @return array
     */
    protected function _attempt($username,$password,$stauts=false){
        $user=Db::name($this->_guard)->where($this->_username,$username)->find();
        if($user){
            if($user[$this->_password]===md5(md5($password).config('custom.password_hash'))){
                if($stauts){
                    if($user['status']==0){
                        return [
                            'message'=>'账号已被禁用',
                            'status_code'=>400,
                        ];
                    }
                }
                $access_token=random(191);
                $expires_in=max((int)config('custom.api_expires_in'),0);
                $timestamp=time();
                Db::name('guard_tokens')->insert([
                    'access_token'=>$access_token,
                    'guard'=>$this->_guard,
                    'token_type'=>$this->_token_type,
                    'expires_in'=>$expires_in,
                    'is_forever'=>$expires_in==0?1:0,
                    'relate_id'=>$user['id'],
                    'create_time'=>$timestamp,
                    'update_time'=>$timestamp,
                ]);
                return [
                    'message'=>'登录成功',
                    'status_code'=>200,
                    'access_token'=>$access_token,
                    'token_type'=>$this->_token_type,
                    'expires_in'=>$expires_in,
                ];
            }
        }
        return [
            'message'=>'账号或密码错误',
            'status_code'=>400,
        ];
    }
}