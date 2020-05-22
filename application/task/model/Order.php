<?php

namespace app\task\model;

use app\common\model\Balance;
use app\common\model\DeliveryRule;
use app\common\model\Order as OrderModel;
use think\Db;

/**
 * 订单模型
 * Class Order
 * @package app\common\model
 */
class Order extends OrderModel
{
    /**
     * 待支付订单详情
     * @param $order_no
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function payDetail($order_no)
    {
        return self::get(['order_no' => $order_no, 'pay_status' => 10], ['goods']);
    }

    /**
     * 更新付款状态
     * @param $transaction_id
     * @return false|int
     * @throws \Exception
     */
    public function updatePayStatus($transaction_id, $userBalance = false)
    {
        Db::startTrans();
        // 更新商品库存、销量
        $GoodsModel = new Goods;
        $GoodsModel->updateStockSales($this['goods']);
        // 更新订单状态
        $data = [
            'pay_status'     => 20,
            'pay_time'       => time(),
            'transaction_id' => $transaction_id,
        ];
        // 更新订单为待收货状态
        if (count(DeliveryRule::DELIVERY_DEFAULT) == 2) {
            $data = array_merge($data,[
                'express_company' => DeliveryRule::DELIVERY_DEFAULT['express_company'],
                'express_no'      => DeliveryRule::DELIVERY_DEFAULT['express_no'],
                'delivery_status' => 20,
                'delivery_time'   => time(),
            ]);
        }
        if ($userBalance) {
            $data['use_balance'] = $this['pay_price'];

            $myBalance = Balance::myBalance($this['user_id'], true);
            if ($myBalance < $this['pay_price']) throw new \Exception('余额不足');
            $this->consumerBalance($this['user_id'], $this['pay_price'], $this['order_no']);
        }

        $this->save($data);
        Db::commit();
        return true;
    }

}
