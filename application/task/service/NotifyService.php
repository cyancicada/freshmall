<?php


namespace app\task\service;


use app\common\library\mq\RabbitMQ;
use app\common\library\sms\Driver as SmsDriver;
use app\task\model\Order;
use app\task\model\Setting as SettingModel;

class NotifyService
{
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
        $order->updatePayStatus($transactionId, $userBalance);
        //记录已经支付的id，供打印机打印
        $order->findPrintOrderNoOrCreate($order['order_no']);
        // 发送订单确认 MQ
        self::pushOrderMegToMQ($order, '/task/notify/receipt', 'trade.order.receive_days');
        // 发送短信通知 MQ
        self::pushOrderMegToMQ([
            'type'           => 'order_pay',
            'wxapp_id'       => $order['wxapp_id'],
            'templateParams' => ['order_no' => $order['order_no']],
        ], '/task/notify/sms');
    }


    /**
     * 推送MQ消息
     * @param $data
     * @param int $delay
     * @param int $retryTime
     * @author kyang
     */
    public static function pushOrderMegToMQ($data, $path = '', $settingKey = '', $retryTime = 0)
    {
        $day = 0;
        if (!empty($settingKey)) {
            list($key, $typeKey, $dayKey) = explode('.', $settingKey);

            $values = SettingModel::getItem($key);

            if (!empty($values)) {
                $day = isset($values[$typeKey][$dayKey]) ? intval($values[$typeKey][$dayKey]) : 0;
            }
        }

        RabbitMQ::instance()->push([
            'data'      => $data,
            'delay'     => $day * 86400000,
            'retryTime' => $retryTime,
            'path'      => $path
        ]);
    }
}
