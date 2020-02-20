<?php

namespace app\api\model;

use think\Cache;
use app\common\model\ShopModel;

/**
 * 积分管理
 * Class Score
 * @package app\api\model
 */
class Score
{
    /* @var string $error 错误信息 */
    public $error = '';

    /* @var int $user_id 用户id */
    private $user_id;

    /* @var bool $clear 是否清空积分 */
    private $clear = false;

    /**
     * 获取积分商城中的商品
     * @return
     */
    public function getScoreShop() {
        $shopModel = new ShopModel();
        return $shopModel->getScoreShopGoods();
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

}
