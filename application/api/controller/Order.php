<?php

namespace app\api\controller;

use app\api\model\Order as OrderModel;
use app\api\model\Wxapp as WxappModel;
use app\api\model\Cart as CartModel;
use app\common\library\mq\RabbitMQ;
use app\common\library\wechat\WxPay;
use app\task\model\Setting as SettingModel;
use think\Log;

/**
 * 订单控制器
 * Class Order
 * @package app\api\controller
 */
class Order extends Controller
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
     * 订单确认-立即购买
     * @param $goods_id
     * @param $goods_num
     * @param $goods_sku_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function buyNow($goods_id, $goods_num, $goods_sku_id, $delivery_time = null)
    {
        // 商品结算信息
        $model = new OrderModel;
        $order = $model->getBuyNow($this->user, $goods_id, $goods_num, $goods_sku_id);

        if (!$this->request->isPost()) {
            return $this->renderSuccess($order);
        }
        if ($model->hasError()) {
            return $this->renderError($model->getError());
        }
        $order['remark'] = $this->request->post('remark');
        if (!isset($order['delivery_time'])) $order['delivery_time'] = $delivery_time;
        // 创建订单
        if ($model->add($this->user['user_id'], $order)) {

            self::pushOrderMegToMQ($order);
            // 发起微信支付
            return $this->renderSuccess([
                'payment'  => $this->wxPay($model['order_no'], $this->user['open_id']
                    , $order['order_pay_price']),
                'order_id' => $model['order_id']
            ]);

        }
        $error = $model->getError() ?: '订单创建失败';
        return $this->renderError($error);
    }

    public static function pushOrderMegToMQ($data, $delay = 0, $retryTime = 0)
    {

        $values = SettingModel::getItem('trade');
        $day    = isset($values['order']['close_days']) ? intval($values['order']['close_days']) : 2;
        RabbitMQ::instance()->push([
            'data'      => $data,
            'delay'     => $delay === 0 ? $day * 86400000 : $delay,
            'retryTime' => $retryTime,
        ]);
    }
    /**
     * 订单确认-购物车结算
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function cart($delivery_time = null)
    {
        // 商品结算信息
        $model = new OrderModel;
        $order = $model->getCart($this->user);
        if (!$this->request->isPost()) {
            return $this->renderSuccess($order);
        }
        if (!isset($order['delivery_time'])) $order['delivery_time'] = $delivery_time;
        $order["remark"] = $this->request->post("remark");
        // 创建订单
        if ($model->add($this->user['user_id'], $order)) {
            // 清空购物车
            $Card = new CartModel($this->user['user_id']);
            $Card->clearAll();
            // 发起微信支付
            return $this->renderSuccess([
                'payment'  => $this->wxPay($model['order_no'], $this->user['open_id']
                    , $order['order_pay_price']),
                'order_id' => $model['order_id']
            ]);
        }
        $error = $model->getError() ?: '订单创建失败';
        return $this->renderError($error);
    }


    /** 打单服务
     * @return array
     */
    public function printOrder()
    {
        $orderList = [];
        try {
            $canWrite = $this->request->get('canWrite',false);
            $connected = $this->request->get('connected',false);
            Log::info('canWrite=>'.$canWrite);
            Log::info('connected=>'.$connected);
            if (!$canWrite || $canWrite == 'false' || !$connected || $connected == 'false') {
                return $this->renderSuccess($orderList);
            }

            $model      = new OrderModel;
            $orderNo = $model->findPrintOrderNoOrCreate();
            if ($orderNo){
                $order      = $model->where('order_no', '=', $orderNo)->limit(1)->field(['order_id'])->find();
                if (isset($order->order_id) && !empty($order->order_id)) {
                    $orderList[] = OrderModel::detail($order->order_id);
                }
            }
            return $this->renderSuccess($orderList);
        } catch (\Exception $exception) {

        }
        return $this->renderSuccess($orderList);
    }

    /**
     * 构建微信支付
     * @param $order_no
     * @param $open_id
     * @param $pay_price
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function wxPay($order_no, $open_id, $pay_price)
    {
        $wxConfig = WxappModel::getWxappCache();
        $WxPay    = new WxPay($wxConfig);
        return $WxPay->unifiedorder($order_no, $open_id, $pay_price);
    }


}
