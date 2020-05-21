<?php


namespace app\task\service;


use app\common\library\mq\RabbitMQ;
use app\common\library\sms\Driver as SmsDriver;
use app\task\model\Order;
use app\task\model\Setting as SettingModel;

class NotifyService
{

    /**
     * 发送短信通知
     * @param $wxapp_id
     * @param $order_no
     * @return mixed
     * @throws \think\Exception
     */
    public static function sendSms($wxapp_id, $order_no)
    {
        // 短信配置信息
        $config = SettingModel::getItem('sms', $wxapp_id);
        return (new SmsDriver($config))->sendSms('order_pay', compact('order_no'));
    }

    /**
     * 支付完成更新订单
     * @param $outTradeNo
     * @param $transactionId
     * @throws \Exception
     * @author kyang
     */
    public static function updateOrder($outTradeNo, $transactionId, $userBalance = false)
    {
        // 订单信息
        $order = (new Order)->payDetail($outTradeNo);
        if (empty($order)) throw new \Exception('订单不存在');
        // 更新订单状态
        $order->updatePayStatus($transactionId,$userBalance);
        // 发送短信通知
        self::sendSms($order['wxapp_id'], $order['order_no']);
        //记录已经支付的id，供打印机打印
        $order->findPrintOrderNoOrCreate($order['order_no']);
        self::pushOrderMegToMQ($order);
    }


    /**
     * 推送MQ消息
     * @param $data
     * @param int $delay
     * @param int $retryTime
     * @author kyang
     */
    public static function pushOrderMegToMQ($data, $delay = 0, $retryTime = 0)
    {

        $values = SettingModel::getItem('trade');
        $day    = isset($values['order']['receive_days']) ? intval($values['order']['receive_days']) : 2;
        RabbitMQ::instance()->push([
            'data'      => $data,
            'delay'     => $delay === 0 ? $day * 86400000 : $delay,
            'retryTime' => $retryTime,
        ]);
    }
}
