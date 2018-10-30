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

use app\common\controller\Common;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Base extends Common
{
    protected function _initialize()
    {
        // 系统开关
        if (!config('web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
        // 默认跳转模块
        if (config('home_default_module') != 'index') {
            $this->redirect(config('home_default_module'). '/index/index');
        }
    }
    /**
     * 错误返回
     * @param $data
     */
    protected function _error($data){
        if(!is_array($data)){
            $data=['message'=>$data];
        }
        if(isset($data['status_code'])){
            $status_code=$data['status_code'];
            unset($data['status_code']);
        }else{
            $status_code=400;
        }
        if(isset($data['message'])){
            $message=$data['message'];
        }else{
            $message='Bad Request';
        }
        throw new \think\exception\HttpException($status_code, $message);
    }
    /**
     * 成功返回
     * @param $data
     * @return \think\response\Json
     */
    protected function _success($data){
        if(isset($data['status_code'])){
            $status_code=$data['status_code'];
            unset($data['status_code']);
        }else{
            $status_code=200;
        }
        return json($data,$status_code);
    }
}
