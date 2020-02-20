<?php
/**
 * Created by PhpStorm.
 * User: huaidao
 * Date: 2020/2/19
 * Time: 0:37
 */

namespace app\common\model;


use think\Request;

class ShopModel extends BaseModel
{
    protected $name = 'score_shop';

    /**
     * 获取积分商城中的商品
     * @return object
     */
    public function getScoreShopGoods() {
        return $this->alias("score")
            ->join("yoshop_upload_file file", "file.file_id = score.img")
            ->order("score.score_num desc")
            ->paginate(15, false, [
                'query' => Request::instance()->request()
            ]);
    }
}