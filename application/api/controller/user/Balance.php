<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Wxapp as WxappModel;
use app\common\library\wechat\WxPay;
use app\common\service\Balance as BalanceService;

/**
 * 全额中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Balance extends Controller
{

    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 用户全额充值
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function balance()
    {
        $balanceService = new BalanceService();
        $request      = request();
        $balance      = $request->post('balance');
        try{// 发起微信支付
            return $this->renderSuccess([
                'payment'  => $balanceService->balanceOperate($this->user['user_id'],$this->user['open_id'], $balance),
            ]);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }

    /**
     * 我的余额
     */
    public function me(){
        try{// 发起微信支付
            $balance      = (new BalanceService)->myBalance($this->user['user_id']);
            return $this->renderSuccess($balance);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }
    /**
     * 我的余额
     */
    public function bill(){
        try{// 发起微信支付
            $bill      = (new BalanceService)->myBill($this->user['user_id']);
            return $this->renderSuccess($bill);
        }catch (\Exception $exception){
            return $this->renderError($exception->getCode() == 1 ? $exception->getMessage() :'操作异常');
        }
    }

}
