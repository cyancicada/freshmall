<?php

namespace app\common\model;

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


    public static function buildTradeNo($user_id = '')
    {
        return 'B' . $user_id . date('YmdHis');
    }
}
