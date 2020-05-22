<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\common\service\Balance as BalanceService;
use app\task\service\NotifyService;

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
        $balanceService = new BalanceService();
        $request      = request();
        $balance      = $request->post('balance');
        try{// 发起微信支付
            return $this->renderSuccess([
                'payment'  => $balanceService->balanceOperate($this->user['user_id'],$this->user['open_id'], $balance),
            ]);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }

    /**
     * 我的余额
     */
    public function me(){
        try{// 发起微信支付
            $balance      = (new BalanceService)->myBalance($this->user['user_id']);
            return $this->renderSuccess($balance);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }
    /**
     * 我的余额
     */
    public function bill(){
        try{// 发起微信支付
            $bill      = (new BalanceService)->myBill($this->user['user_id']);
            return $this->renderSuccess($bill);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }


    /**
     * 立即支付
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function pay()
    {
        $request       = request();
        $order_id      = $request->post('order_id');
        $delivery_time = $request->post('delivery_time');
        // 订单详情
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 判断商品状态、库存
        if (!$order->checkGoodsStatusFromOrder($order['goods'])) return $this->renderError($order->getError());

        OrderModel::updateClaimDeliveryTime($order_id, $delivery_time);
        // 支付
        try {
            NotifyService::updateOrder($order['order_no'], BalanceModel::buildTradeNo($this->user['user_id']), true);
            return $this->renderSuccess();
        } catch (\Exception $exception) {
            $m = $exception->getCode() == 1 ? $exception->getMessage():'支付失败';
            return $this->renderError($m);
        }

    }

}
