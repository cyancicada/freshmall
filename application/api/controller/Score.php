<?php
/**
 * Created by PhpStorm.
 * User: huaidao
 * Date: 2020/2/20
 * Time: 17:16
 */

namespace app\api\controller;

use app\api\model\Score as ScoreModel;

class Score extends Controller
{
    /**
     * 积分商城列表
     * @return array
     */
    public function lists() {
        $scoreModel = new ScoreModel();
        $outCome = $scoreModel->getScoreShop();
        return $this->renderSuccess($outCome);
    }
}