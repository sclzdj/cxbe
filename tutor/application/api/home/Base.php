<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/27
 * Time: 14:23
 */

namespace app\api\home;

use think\Controller;
use think\Request;

class Base extends Controller
{
    use Api;
    
    public function __construct(Request $request = null)
    {
        if (!config('web_site_status')) {
            $this->_error(['message'=>'站点已经关闭，请稍后访问~','status_code'=>400]);
        }

        parent::__construct($request);
        
        $this->_apiRun();
    }
}