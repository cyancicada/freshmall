<?php


namespace app\common\service;


use app\api\model\Wxapp as WxappModel;
use app\common\library\wechat\WxPay;
use app\common\model\BalanceDetail;
use think\Db;
use think\Log;
use app\common\model\Balance as BalanceModel;

class Balance
{

    /**
     * @param $user_id
     * @param $balance
     * @return array
     * @throws \Exception
     */
    public function balanceOperate($user_id, $open_id, $balance)
    {
        if (empty($balance)) throw new \Exception('充值金额错误');

        $balance      = floatval($balance);
        $balanceModel = new BalanceModel;
        $type         = $balance > 0 ? BalanceModel::TYPE_ADD : BalanceModel::TYPE_CONSUMER;
        try {
            Db::startTrans();
            $id = $balanceModel->where('user_id', $user_id)->value('id');
            if (!empty($id)) {
                if ($type == BalanceModel::TYPE_ADD) $this->inc('balance', $balance);
                if ($type == BalanceModel::TYPE_CONSUMER) $this->dec('balance', $balance);
            } else {
                $balanceModel->save([
                    'user_id'  => $user_id,
                    'wxapp_id' => $balanceModel::$wxapp_id,
                    'balance'  => $balance,
                ]);
            }
            (new BalanceDetail)->save([
                'user_id'  => $user_id,
                'wxapp_id' => $balanceModel::$wxapp_id,
                'balance'  => $balance,
                'trade_no' => '',
                'mark'     => $type,
            ]);
            if ($type == BalanceModel::TYPE_ADD) {
                $wxConfig = WxappModel::getWxappCache();
                $WxPay    = new WxPay($wxConfig);
                $tradeNo  = self::buildTradeNo($user_id);
                $data     = $WxPay->unifiedorder($tradeNo, $open_id, $balance);
            }
            Db::commit();
            return $data;
        } catch (\Exception $exception) {
            Db::rollback();
            Log::info($exception->getMessage());
            throw new \Exception('充值失败');
        }
    }

    public static function buildTradeNo($user_id = '')
    {
        return 'B' . $user_id . date('YmdHis');
    }
}