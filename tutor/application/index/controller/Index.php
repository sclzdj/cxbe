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

use think\captcha\Captcha;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Base
{
    public function index()
    {
        return '主页';
    }

    /**
     * 验证码
     * @return mixed
     */
    public function captcha()
    {
        $config = [
            // 验证码字体大小
            'fontSize' => 30,
            // 验证码位数
            'length' => 4,
            // 关闭验证码杂点
            'useNoise' => true,
            //集合
            'codeSet' => '1234567890',
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}
