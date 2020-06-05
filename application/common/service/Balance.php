<?php


namespace app\common\service;


use app\api\model\Wxapp as WxappModel;
use app\common\library\wechat\WxPay;
use app\common\model\Balance as BalanceModel;
use app\common\model\BalanceDetail;
use app\task\model\Setting as SettingModel;
use think\Db;
use think\Log;

class Balance
{

    /**
     * @param $user_id
     * @param $balance
     * @return array
     * @throws \Exception
     */
    public function balanceOperate($user_id, $open_id, $balance, $type = BalanceModel::TYPE_RECHARGE, $mark = '')
    {
        if (empty($balance)) throw new \Exception('充值金额错误');

        $balance      = floatval($balance);
        $balanceModel = new BalanceModel;
        try {
            Db::startTrans();
            $tradeNo = BalanceModel::buildTradeNo();
            (new BalanceDetail)->save([
                'user_id'  => $user_id,
                'wxapp_id' => $balanceModel::$wxapp_id,
                'balance'  => $balance,
                'trade_no' => $tradeNo,
                'type'     => $type,
                'mark'     => $mark,
            ]);
            if ($type == BalanceModel::TYPE_RECHARGE) {

                $data = (new WxPay(WxappModel::getWxappCache()))->unifiedorder($tradeNo, $open_id, $balance);
            }
            Db::commit();
            return $data;
        } catch (\Exception $exception) {
            Db::rollback();
            Log::info($exception->getMessage());
            throw new \Exception('充值失败');
        }
    }


    /** 用来获取充值设置里对于充值多少送多少
     * @param $chargeAmount
     * @return float|int
     */
    public static function extraAmount($chargeAmount)
    {
        $extraAmount = 0;
        $today       = date('Y-m-d H:i:s');
        $values      = SettingModel::getItem('charge');

        if (empty($values)) return $extraAmount;

        if (!isset($values['time_range'])) return $extraAmount;

        list($start, $end) = explode(' ~ ', $values['time_range']);
        unset($values['time_range']);

        if ($today >= $start && $today <= $end) {

            foreach ($values as $item) {

                $amount  = floatval($item['amount']);
                $freeGet = floatval($item['free_get']);
                if ($amount <= 0 || $freeGet <= 0) continue;

                if ($chargeAmount >= $amount) $extraAmount = $freeGet;
            }
        }
        return $extraAmount;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function chargeBalance($data)
    {
        try {
            Db::startTrans();
            $actualAmount = round(floatval($data['total_fee']) / 100, 2);
            if ($actualAmount <= 0) throw new \Exception('实际充值金额为0：' . $data['out_trade_no']);

            $filter = ['trade_no' => $data['out_trade_no'], 'trade_status' => 'UNFINISHED'];
            $row    = BalanceDetail::get($filter);
            if (empty($row)) throw new \Exception('充值记录不存在：' . $data['out_trade_no']);

            $chargeAmount = $actualAmount; // 此时充值金额 等于微信返回的真实金额
            $extraAmount  = self::extraAmount($chargeAmount); // 计算充值返多少
            $chargeAmount += $extraAmount; // 最后应该充值多少

            $balanceRow = BalanceModel::get(['user_id' => $row['user_id']]);
            if (!empty($balanceRow)) {
                $balanceRow->setInc('balance', $chargeAmount);
            } else {
                (new BalanceModel)->save([
                    'user_id'  => $row['user_id'],
                    'wxapp_id' => $row['wxapp_id'],
                    'balance'  => $chargeAmount,
                ]);
            }
            BalanceDetail::update([
                'trade_status'   => 'FINISHED',
                'actual_amount'  => $actualAmount, //微信返回的真实金额
                'balance'        => $chargeAmount,
                'extra'          => $extraAmount,
                'transaction_id' => $data['transaction_id'],
                'latest_balance' => $this->myBalance($row['user_id'], true)
            ], $filter);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            Log::info($exception->getMessage());
            throw new \Exception('充值失败');
        }


    }


    /** 我的余额
     * @param $user_id
     * @throws \Exception
     */
    public function myBalance($user_id, $onlyBalance = false)
    {
        return BalanceModel::myBalance($user_id, $onlyBalance);
    }

    /** 我的余额
     * @param $user_id
     * @throws \Exception
     */
    public function myBill($user_id)
    {
        try {
            $filter = ['user_id' => $user_id, 'trade_status' => 'FINISHED'];
            $data   = (new BalanceDetail)->where($filter)->order(['create_time' => 'desc'])->select();

            foreach ($data as &$item) {
                $item['type_name']       = isset(BalanceModel::$typeMap[$item['type']]) ? BalanceModel::$typeMap[$item['type']] : '其它';
                $item['balance_display'] = floatval($item['balance']) > 0 ? '+' . $item['balance'] : $item['balance'];
            }
            return $data;
        } catch (\Exception $exception) {
            throw new \Exception('获取余额账单失败');
        }

    }

}
