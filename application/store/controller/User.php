<?php

namespace app\store\controller;

use app\common\model\Balance;
use app\common\service\Balance as BalanceService;
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
    /**
     * 用户余额列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function balance()
    {
        $request = Request::instance();
        $filter = [];
        if ($username = $request->get('username')){
            $filter['nickName']=['like','%'.$username.'%'];
        }
        $model = new Balance;

        $list = $model->getList($filter);
        return $this->fetch('balance', compact('list'));
    }
    /**
     * 用户余额明细
     * @return mixed
     * @throws \Exception
     */
    public function balanceItems()
    {
        $request = Request::instance();
        $userName = $request->get('nickName');
        $list      = (new BalanceService)->myBill($request->get('user_id'));


        return $this->renderSuccess($list);
    }

}
