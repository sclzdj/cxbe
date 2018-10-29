<?php

return [
    'table_engine'   => 'myisam',#数据表引擎
    'pageSize'       => 10,#每页条数
    'api_log_on'     => true,#是否开启api日志
    'api_limit_num'     => 0,#每分钟只能请求多少次，设置为0就不限制
    'api_sign_check' => false,#是否开启api签名校验
    'api_sign_key'   => 'uun_dp-tp5',#api签名密钥
    'api_expires_in'=> 0,#token过期时间，设置为0就用不过期
    'password_hash'  => 'uun_dp-tp5',#密码加密key
    'is_allow_upload'=> true,//是否允许文件上传
];