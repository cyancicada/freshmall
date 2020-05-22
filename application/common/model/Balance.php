<?php

namespace app\common\model;

use app\common\library\utils\SnowFlake;

/**
 * 商品分类模型
 * Class Category
 * @package app\common\model
 */
class Balance extends BaseModel
{
    protected $name = 'balance';

    const TYPE_RECHARGE = 'RECHARGE'; // 充值
    const TYPE_CONSUMER = 'CONSUMER'; // 消费
    const TYPE_REFUND   = 'REFUND'; //退款


    public static $typeMap = [
        self::TYPE_RECHARGE => '充值',
        self::TYPE_CONSUMER => '消费',
        self::TYPE_REFUND   => '退款',
    ];


    public static function buildTradeNo()
    {
        return 'B' . date('Ymd') . SnowFlake::nextId();
    }

    /** 我的余额
     * @param $user_id
     * @throws \Exception
     */
    public static function myBalance($user_id, $onlyBalance = false)
    {
        try {
            $response = ['balance' => 0.00];
            $balance  = self::get(['user_id' => $user_id]);

            if (!empty($balance)) $response['balance'] = round($balance['balance'], 2);

            return $onlyBalance ? round($response['balance'], 2) : $response;
        } catch (\Exception $exception) {
            throw new \Exception('获取余额失败');
        }

    }
}
