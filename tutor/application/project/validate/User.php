<?php

namespace app\project\validate;

use think\Validate;

class User extends Validate
{
    protected $rule
        = [
            'username|用户名' => 'require|min:5|max:10|unique:users',
            'password|登录密码' => 'require|min:5|max:15',
            'identity|身份' => 'require|in:0,1',
            'head_img|图片' => 'number',
            'sex|性别' => 'in:0,1',
            'date|生日' => 'dateFormat:y-m-d',
            'mobile|手机号' => 'require|regex:/^1[34578]\d{9}$/|unique:users',
            'qq|QQ号' => 'number|min:5|max:11',
            'wechat|微信号' => 'min:5|max:20',
            'email|邮箱' => 'email|min:5|max:50',
            'personality_sign|个性签名' => 'min:1|max:20',
            'city|所在地' => 'min:1|max:20',
            'address|详细地址' => 'min:1|max:50',
            'occupation|职业' => 'min:1|max:20',
            'hobby|兴趣爱好' => 'min:1|max:20',
            'speciality|特长' => 'min:1|max:50',
            'status|状态' => 'in:0,1',
            '__token__' => 'token',
        ];
    protected $scene = [
        'edit'  =>  ['username','password'=>'min:5|max:15','identity','head_img','sex','date','mobile','qq','wechat','email','personality_sign','city','address','occupation','hobby','speciality','status','__token__'],
    ];
}