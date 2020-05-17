<?php

namespace app\store\controller;

use app\store\model\User as UserModel;
use think\Request;

/**
 * 用户管理
 * Class User
 * @package app\store\controller
 */
class User extends Controller
{
    /**
     * 用户列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = Request::instance();
        $filter = [];
        if ($username = $request->get('username')){
            $filter['nickName']=['like','%'.$username.'%'];
        }
        $model = new UserModel;

        $list = $model->getList($filter);
        return $this->fetch('index', compact('list'));
    }

}
