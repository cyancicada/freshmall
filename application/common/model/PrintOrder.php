<?php

namespace app\common\model;

/**
 * 商品图片模型
 * Class GoodsImage
 * @package app\common\model
 */
class PrintOrder extends BaseModel
{
    protected $name       = 'print_order';
    protected $updateTime = false;
    protected $createTime = false;

    /** 当前用户是否可有打单服务
     * @param $openId
     * @return bool
     */
    public static function isPrintByOpenId($openId)
    {

        if (empty($openId)) return false;

        $res = self::useGlobalScope(false)->where('open_id', '=', $openId)->value('open_id');

        return !empty($res) && $res == $openId;
    }

}
