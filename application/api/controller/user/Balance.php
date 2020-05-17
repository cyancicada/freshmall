<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\common\model\Balance as BalanceModel;

/**
 * 全额中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Balance extends Controller
{

    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 用户全额充值
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function balance()
    {
        $balanceModel = new BalanceModel();
        $request      = request();
        $balance      = $request->post('balance');
        if ($balanceModel->balanceOperate($this->user['user_id'], $balance)) {
            return $this->renderSuccess();
        }
        return $this->renderError($balanceModel->getError());
    }

}
