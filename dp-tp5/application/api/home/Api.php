<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/27
 * Time: 14:11
 */

namespace app\api\home;

use think\Db;
use think\Request;

trait Api
{
    protected $_request;#请求params
    protected $_request_log_id;#请求日志记录id
    
    protected function _apiRun(){
        $this->_request=Request::instance()->param();
        //接口请求次数限制
        if(config('custom.api_limit_num')>0){
            $this->_limitRequestRate();
        }
        //开启接口日志记录
        if(config('custom.api_log_on')){
            $this->_requestLog();
        }
        //开启签名验证
        if(config('custom.api_sign_check')){
            $this->_checkSign();
        }
    }
    /**
     * 验证签名
     * @return mixed
     */
    protected function _checkSign(){
        $request=$this->_request;
        if(!isset($request['sign']) || $request['sign']===''){
            throw new \think\exception\HttpException(400, '签名错误');
        }
        $sign=$request['sign'];
        unset($request['sign']);
        $makeSign=$this->_makeSign($request);
        if($sign!==$makeSign){
            throw new \think\exception\HttpException(400, '签名错误');
        }
    }
    /**
     * 请求日志
     */
    protected function _requestLog(){
        $request=$this->_request;
        $log=$this->_getAgentInfo();
        $log['type']=Request::instance()->method();
        $log['authorization']=isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:'';
        $log['sign']=isset($request['sign'])?$request['sign']:'';
        unset($request['sign']);
        if(isset($request['action'])){
            unset($request['action']);
        }
        $log['data']=json_encode($request,JSON_UNESCAPED_UNICODE);
        $log['restrict']=Request::instance()->ip().'_'.Request::instance()->module().'_'.Request::instance()->controller().'_'.Request::instance()->action();
        $timestamp=time();
        $log['create_time']=$timestamp;
        $log['update_time']=$timestamp;
        $this->_request_log_id = Db::name('request_logs')->insertGetId($log);
    }
    /**
     * 获取签名
     * @param $data 请求数据
     * @return string
     */
    protected function _makeSign($data)
    {
        $api_sign_key=config('custom.api_sign_key');
        ksort($data, SORT_STRING);
        $sign=json_encode($data).$api_sign_key;
        $sign=md5($sign);
        return $sign;
    }
    /**
     * 获取当前请求的一些代理信息
     * @return array
     */
    protected function _getAgentInfo(){
        $agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        $brower = ['MSIE','Firefox','QQBrowser', 'QQ/', 'UCBrowser', 'MicroMessenger', 'Edge', 'Chrome', 'Opera', 'OPR', 'Safari', 'Trident/'];
        $terminal = ['Windows Phone', 'Windows', 'Android', 'iPhone', 'iPad'];
        $browser_info = '';//未知
        $terminal_info = '';//未知
        foreach($brower as $k => $bro){
            if(stripos($agent, $bro) !== false){
                $browser_info = $bro;
                break;
            }
        }
        foreach($terminal as $k => $sys){
            if(stripos($agent, $sys) !== false){
                $terminal_info = $sys;
                break;
            }
        }
        $reIP=isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:'';
        $url=$this->_curPageURL();
        if($terminal_info==='' && stripos($agent, 'MicroMessenger')!==false){
            $terminal_info='WeChat applet';
        }
        return ['terminal' => $terminal_info, 'brower' => $browser_info,'ip'=>$reIP,'url'=>$url,'agent'=>$agent];
    }
    /**
     * 获取当前请求完整的url
     * @return string
     */
    public function _curPageURL(){
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80"){
            $pageURL .= (isset($_SERVER["SERVER_NAME"])?$_SERVER["SERVER_NAME"]:'') . ":" . $_SERVER["SERVER_PORT"] . (isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:'');
        }else{
            $pageURL .= (isset($_SERVER["SERVER_NAME"])?$_SERVER["SERVER_NAME"]:'') . (isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:'');
        }
        return $pageURL;
    }
    /**
     * 接口请求次数限制
     */
    public function _limitRequestRate()
    {
        $api_limit_num=config('custom.api_limit_num');
        $ip=Request::instance()->ip();
        $module=Request::instance()->module();
        $controller=Request::instance()->controller();
        $action=Request::instance()->action();
        $method=Request::instance()->method();
        $timestamp=time();
        $num=Db::name('request_limits')->where(compact('ip','module','controller','action','method'))->where('create_time <= '.$timestamp.' && create_time > '.($timestamp-60))->count('id');
        if($num>=$api_limit_num){
            throw new \think\exception\HttpException(400, '同一接口每分钟只能请求'.$api_limit_num.'次');
        }
        Db::name('request_limits')->insert([
            'ip'=>$ip,
            'module'=>$module,
            'controller'=>$controller,
            'action'=>$action,
            'method'=>$method,
            'create_time'=>$timestamp,
            'update_time'=>$timestamp,
        ]);
    }
}