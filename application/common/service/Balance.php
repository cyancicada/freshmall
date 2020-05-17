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
    public function balanceOperate($user_id, $open_id, $balance, $type = BalanceModel::TYPE_RECHARGE, $mark = '')
    {
        if (empty($balance)) throw new \Exception('充值金额错误');

        $balance      = floatval($balance);
        $balanceModel = new BalanceModel;
        try {
            Db::startTrans();
            $tradeNo = self::buildTradeNo($user_id);
            (new BalanceDetail)->save([
                'user_id'  => $user_id,
                'wxapp_id' => $balanceModel::$wxapp_id,
                'balance'  => $balance,
                'trade_no' => $tradeNo,
                'type'     => $type,
                'mark'     => $mark,
            ]);
            if ($type == BalanceModel::TYPE_RECHARGE) {
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
            $filter = ['trade_no' => $data['out_trade_no'], 'trade_status' => 'UNFINISHED'];
            $row    = BalanceDetail::get($filter);

            if (empty($row)) throw new \Exception('记录不存在');
            Db::startTrans();
            $balanceModel = new BalanceModel;
            $userFilter   = ['user_id' => $row['user_id']];
            $balanceRow   = BalanceModel::get($userFilter);
            if (!empty($balanceRow)) {
                if (floatval($row['balance']) < 0) {
                    $balanceRow->setDec('balance', $row['balance']);
                }
                if (floatval($row['balance']) > 0) {
                    $balanceRow->setInc('balance', $row['balance']);
                }
            } else {
                $balanceModel->save([
                    'user_id'  => $row['user_id'],
                    'wxapp_id' => $row['wxapp_id'],
                    'balance'  => $row['balance'],
                ]);
            }
            $balance = $this->myBalance($row['user_id'], true);
            BalanceDetail::update(['trade_status' => 'FINISHED', 'latest_balance' => $balance], $filter);
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
        try {
            $response = ['balance' => '0.00'];
            $balance  = BalanceModel::get(['user_id' => $user_id]);
            if (!empty($balance)) $response['balance'] = $balance['balance'];

            return $onlyBalance ? $response['balance'] : $response;
        } catch (\Exception $exception) {
            throw new \Exception('获取余额失败');
        }

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
                $item['type_name'] = isset(BalanceModel::$typeMap[$item['type']]) ? BalanceModel::$typeMap[$item['type']] : '其它';
            }
            return $data;
        } catch (\Exception $exception) {
            throw new \Exception('获取余额账单失败');
        }

    }

    public static function buildTradeNo($user_id = '')
    {
        return 'B' . $user_id . date('YmdHis');
    }
}