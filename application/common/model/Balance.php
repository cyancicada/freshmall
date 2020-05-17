<?php

namespace app\common\model;

use think\Cache;
use think\Db;

/**
 * 商品分类模型
 * Class Category
 * @package app\common\model
 */
class Balance extends BaseModel
{
    protected $name = 'balance';

    const TYPE_ADD      = 'ADD';
    const TYPE_CONSUMER = 'CONSUMER';

    public function balanceOperate($user_id, $balance)
    {
        if (empty($balance)) {
            $this->error = '充值金额错误';
            return false;
        }
        $type = $balance > 0 ? self::TYPE_ADD : self::TYPE_CONSUMER;
        try {
            Db::startTrans();
            $id = $this->where('user_id', $user_id)->value('id');
            if (!empty($id)) {
                if ($type == self::TYPE_ADD) $this->inc('balance', $balance);
                if ($type == self::TYPE_CONSUMER) $this->inc('balance', $balance);
            } else {
                $this->save([
                    'user_id'  => $user_id,
                    'wxapp_id' => self::$wxapp_id,
                    'balance'  => $balance,
                ]);
            }
            (new BalanceDetail)->save([
                'user_id'  => $user_id,
                'wxapp_id' => self::$wxapp_id,
                'balance'  => $balance,
                'mark'     => $type,
            ]);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            $this->error = '充值失败';
        }
        return false;
    }
}
