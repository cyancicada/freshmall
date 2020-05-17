<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\common\model\PrintOrder;
use app\common\service\Balance;

/**
 * 个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前用户信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \Exception
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        // 订单总数
        $model                = new OrderModel;
        $orderCount           = [
            'payment'  => $model->getCount($userInfo['user_id'], 'payment'),
            'received' => $model->getCount($userInfo['user_id'], 'received'),
        ];
        $balance              = (new Balance)->myBalance($userInfo['user_id']);
        $userInfo['is_print'] = PrintOrder::isPrintByOpenId($userInfo['open_id']);
        return $this->renderSuccess(compact('userInfo', 'orderCount', 'isPrint', 'balance'));
    }

}
