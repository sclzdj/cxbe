<?php
/**
 * Created by PhpStorm.
 * User: DuJun
 * Date: 2018/10/27
 * Time: 14:23
 */

namespace app\api\home;

use app\project\model\Category As CategoryModel;
use app\project\model\User;

class Category extends AuthUsers
{
    public function getIndex(){
        $categorys=CategoryModel::fieldLimit()->order('sort')->select();
        return $this->_success($categorys);
    }
    public function getAuthId(){
        return $this->_success($this->_auth_id);
    }
    public function getInfo(){
        $users=User::fieldLimit()->order('create_time')->select();
        return $this->_success($users);
    }
}