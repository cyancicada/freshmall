<?php

namespace app\task\controller;

use app\task\model\Order as OrderModel;
use app\common\library\wechat\WxPay;
use app\api\model\Order as ApiOrderModel;
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

    public function mq()
    {
        $body = file_get_contents('php://input');
        Log::info('php://input=>' . $body);
        $request = json_decode($body);

        Log::info('input=request:order_id'.$request->order_id);
        Log::info('input=request:user_id'.$request->user_id);
        if (isset($request->order_id) &&
            !empty($request->order_id) &&
            isset($request->user_id) &&
            !empty($request->user_id)) {
            try {
                $model = ApiOrderModel::getUserOrderDetail($request->order_id, $request->user_id);
                Log::info('input=pay_status:'.$model['pay_status']);
                if (empty($model)) die('SUCCESS');

                switch (intval($model['pay_status'])) {
                    case 10:
                        $model->cancel();
                        break;
                    case 20:
                        $model->receipt();
                        break;
                }
            } catch (\Exception $exception) {
                Log::info('input=Exception:'.$exception->getMessage());
            }
        }
        die('SUCCESS');
    }

}
