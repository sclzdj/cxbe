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
        parent::__construct($request);
        
        $this->_apiRun();
    }
}