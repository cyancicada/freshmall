<?php

namespace app\task\controller;

use app\api\model\Order as ApiOrderModel;
use app\common\library\wechat\WxPay;
use app\task\model\Order as OrderModel;
use think\Log;

/**
 * 支付成功异步通知接口
 * Class Notify
 * @package app\api\controller
 */
class Notify
{
    /**
     * 支付成功异步通知
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function order()
    {
        $WxPay = new WxPay([]);
        $WxPay->notify(new OrderModel);
    }

    /**
     * MQ 完成订单
     * @author kyang
     */
    public function receipt()
    {
        $request = $this->parseRequest();
        if (isset($request->order_id) && !empty($request->order_id) &&
            isset($request->user_id) && !empty($request->user_id)) {
            try {
                $model = ApiOrderModel::getUserOrderDetail($request->order_id, $request->user_id);
                if (empty($model)) throw new \Exception('订单不存在', 1);

                if (intval($model['pay_status']['value']) != 20) {
                    throw new \Exception('完成订单错误，ERROR[当前支付状态：' . $model['pay_status']['value'] . ']', 1);
                }
                $model->receipt();

            } catch (\Exception $exception) {
                Log::info('receipt order ERROR :' . $exception->getMessage());
            }
        }
        die('SUCCESS');
    }

    /**
     * MQ 取消订单
     * @author kyang
     */
    public function cancel()
    {
        $request = $this->parseRequest();
        if (isset($request->order_id) && !empty($request->order_id) &&
            isset($request->user_id) && !empty($request->user_id)) {
            try {
                $model = ApiOrderModel::getUserOrderDetail($request->order_id, $request->user_id);
                if (empty($model)) throw new \Exception('订单不存在', 1);

                if (intval($model['pay_status']['value']) != 10) {
                    throw new \Exception('取消订单错误，ERROR[当前支付状态：' . $model['pay_status']['value'] . ']', 1);
                }
                if (intval(['order_status']['value']) == 20) {
                    throw new \Exception('订单已经取消，无需再次取消，ERROR[当前订单状态：' . $model['order_status']['value'] . ']', 1);
                }
                $model->cancel();

            } catch (\Exception $exception) {
                Log::info('cancel order ERROR :' . $exception->getMessage());
            }
        }
        die('SUCCESS');
    }

    /**
     * 后期删除
     * @deprecated
     * @author kyang
     */
    public function mq() { die('SUCCESS'); }

    private function parseRequest($array = false)
    {
        $body = file_get_contents('php://input');
        Log::info('php://input=>' . $body);
        return json_decode($body, $array);
    }

}
