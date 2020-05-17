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
            $tradeNo = self::buildTradeNo($user_id);
            (new BalanceDetail)->save([
                'user_id'  => $user_id,
                'wxapp_id' => $balanceModel::$wxapp_id,
                'balance'  => $balance,
                'trade_no' => $tradeNo,
                'mark'     => $type,
            ]);
            if ($type == BalanceModel::TYPE_ADD) {
                $wxConfig = WxappModel::getWxappCache();
                $WxPay    = new WxPay($wxConfig);
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

    /**
     * @param $data
     * @throws \Exception
     */
    public function writeBalance($data)
    {
        try {
            $filter = ['trade_no' => $data['out_trade_no']];
            $row    = BalanceDetail::get($filter);
            Log::info(var_export($row, true));
            Log::info(var_export($data, true));
            if (empty($row)) throw new \Exception('充值记录不存在');
            Db::startTrans();
            BalanceDetail::update(['trade_status' => 'FINISHED'], $filter);
            $balanceModel = new BalanceModel;
            $id           = BalanceModel::get(['user_id' => $row['user_id']])->value('id');
            if (!empty($id)) {
                if (floatval($row['balance']) < 0) {
                    $balanceModel->dec('balance', $row['balance']);
                }
                if (floatval($row['balance']) > 0) {
                    $balanceModel->inc('balance', $row['balance']);
                }
                return;
            }
            $balanceModel->save([
                'user_id'  => $row['user_id'],
                'wxapp_id' => $row['wxapp_id'],
                'balance'  => $row['balance'],
            ]);
            Db::commit();
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