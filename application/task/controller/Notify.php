<?php

namespace app\task\controller;

use app\task\model\Order as OrderModel;
use app\common\library\wechat\WxPay;
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
        Log::info($body);
        $request = json_encode($body);
    }

}
