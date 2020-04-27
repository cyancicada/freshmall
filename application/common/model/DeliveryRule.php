<?php

namespace app\common\model;

/**
 * 配送模板区域及运费模型
 * Class DeliveryRule
 * @package app\store\model
 */
class DeliveryRule extends BaseModel
{
    protected $name = 'delivery_rule';
    protected $updateTime = false;

    const DELIVERY_DEFAULT = [
        'express_company' => '严明宝',
        'express_no'      => '15050567076'
    ];

}
