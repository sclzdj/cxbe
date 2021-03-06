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

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{
    public function index()
    {
        // 默认跳转模块
        if (config('home_default_module') != 'index') {
            $this->redirect(config('home_default_module'). '/index/index');
        }
        return '欢迎使用thinkphp5';
    }
    
    public function test()
    {
        echo '<form action="http://dt5.dj/upload" method="post" enctype="multipart/form-data"><input type="text" name="type" value="image"><input name="file" type="file" /><input type="submit" value="上传" /></form>';//上传图片接口测试
        die;
    }
}
