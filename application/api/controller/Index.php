<?php

namespace app\api\controller;

use app\api\model\Goods as GoodsModel;
use app\api\model\WxappPage;

/**
 * 首页控制器
 * Class Index
 * @package app\api\controller
 */
class Index extends Controller
{
    /**
     * 首页diy数据
     * @return array
     * @throws \think\exception\DbException
     */
    public function page()
    {
        // 页面元素
        $wxappPage = WxappPage::detail();
        $items = $wxappPage['page_data']['array']['items'];
        // 新品推荐
        $model = new GoodsModel;
        $newest = $model->getNewList();
        // 猜您喜欢
        try{
            $user        = $this->getUser();
            $goodsIdList = $model->historyGoodsUserView($user['user_id']);
            if (empty($goodsIdList)) {
                $best = $newest;
            } else {
                $best = $model->getBestList(['goods_id' => ['in', $goodsIdList]], $goodsIdList);
            }
        }catch (\Exception $exception){
            $best = $newest;//$model->getBestList();
        }
        return $this->renderSuccess(compact('items', 'newest', 'best'));
    }

}
