<?php

namespace app\project\validate;

use think\Validate;

class User extends Validate
{
    protected $rule;

    protected $scene = [
        'add' => ['username', 'password' => 'require|min:5|max:15', 'identity', 'head_img', 'sex', 'birthday', 'mobile', 'qq', 'wechat', 'email', 'personality_sign', 'city', 'address', 'occupation', 'hobby', 'speciality', 'status', '__token__'],
        'edit' => ['username', 'password' => 'min:5|max:15', 'identity', 'head_img', 'sex', 'birthday', 'mobile', 'qq', 'wechat', 'email', 'personality_sign', 'city', 'address', 'occupation', 'hobby', 'speciality', 'status', '__token__'],
        'login' =>['user','password'=>'require|login','__token__'],
        'loginMobile' =>['user','code','captcha','__token__'],
        'register' =>['username','mobile','code','password'=>'require|min:5|max:15|confirm','__token__'],
    ];

    protected $message=[
        'password.confirm'=>'两次登录密码不一致',
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        $this->rule = [
            'user|账号' => 'require',
            'username|用户名' => 'require|min:5|max:16|unique:users|checkName',
            'identity|身份' => 'require|in:0,1',
            'head_img|图片' => 'number',
            'sex|性别' => 'in:0,1',
            'birthday|生日' => 'dateFormat:Y-m-d|before:' . date('Y-m-d'),
            'mobile|手机号' => 'require|regex:/^1[34578]\d{9}$/|unique:users|checkMobile',
            'code|短信验证码' => 'require|code',
            'qq|QQ号' => 'number|min:5|max:11',
            'wechat|微信号' => 'min:5|max:20',
            'email|邮箱' => 'email|min:5|max:50',
            'personality_sign|个性签名' => 'min:1|max:20',
            'city|所在地' => 'min:1|max:20',
            'address|详细地址' => 'min:1|max:50',
            'occupation|职业' => 'min:1|max:20',
            'hobby|兴趣爱好' => 'min:1|max:20',
            'speciality|特长' => 'min:1|max:50',
            'password|登录密码' => 'require|min:5|max:15|confirm|login',
            'status|状态' => 'in:0,1',
            'captcha|验证码'=>'require|checkCaptcha',
            '__token__' => 'token',
        ];
        parent::__construct($rules, $message, $field);
    }
    // 自定义验证规则
    protected function code($value, $rule, $data)
    {
        return true;
    }
    // 自定义验证规则
    protected function checkCaptcha($value, $rule, $data)
    {
        if(!captcha_check($value)){
            return '验证码错误';
        };
        return true;
    }
    // 自定义验证规则
    protected function login($value, $rule, $data)
    {
        if(!\app\project\model\User::fieldLimit()->where('username|mobile',$data['user'])->where('password',md5(md5($value).config('custom.password_hash')))->find()){
            return '账号或密码错误';
        }
        return true;
    }
    // 自定义验证规则
    protected function checkName($value, $rule, $data)
    {
        if (isset($data['id'])) {
            $where = ['mobile' => $value, 'id' => ['neq', $data['id']]];
        } else {
            $where = ['mobile' => $value];
        }
        if (\app\project\model\User::where($where)->find()) {
            return '该用户名已被其它用户注册成手机号了';
        }
        return true;
    }
    // 自定义验证规则
    protected function checkMobile($value, $rule, $data)
    {
        if (isset($data['id'])) {
            $where = ['username' => $value, 'id' => ['neq', $data['id']]];
        } else {
            $where = ['username' => $value];
        }
        if (\app\project\model\User::where($where)->find()) {
            return '该手机号已被其它用户注册成用户名了';
        }
        return true;
    }
}