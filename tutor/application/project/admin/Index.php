<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/25
 * Time: 14:46
 */

namespace app\project\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;

/**
 * cms 后台模块
 */
class Index extends Admin
{
    public function index(){
        // 使用ZBuilder构建数据表格，并将页面标题设置为“列表”
        return ZBuilder::make('table')->setPageTitle('列表')->fetch();
    }
}