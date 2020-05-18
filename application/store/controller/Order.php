<?php

namespace app\store\controller;

use app\api\model\Goods as GoodsModel;
use app\common\model\BaseModel;
use app\store\model\Order as OrderModel;
use think\cache\driver\Redis;
use app\store\model\User;

/**
 * 订单管理
 * Class Order
 * @package app\store\controller
 */
class Order extends Controller
{
    /**
     * 待发货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function delivery_list()
    {
        return $this->getList('待发货订单列表', [
            'pay_status'      => 20,
            'delivery_status' => 10
        ]);
    }

    /**
     * 待收货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function receipt_list()
    {
        return $this->getList('待收货订单列表', [
            'pay_status'      => 20,
            'delivery_status' => 20,
            'receipt_status'  => 10
        ]);
    }

    /**
     * 待付款订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function pay_list()
    {
        return $this->getList('待付款订单列表', ['pay_status' => 10, 'order_status' => 10]);
    }

    /**
     * 已完成订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function complete_list()
    {
        return $this->getList('已完成订单列表', ['order_status' => 30]);
    }

    /**
     * 已取消订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function cancel_list()
    {
        return $this->getList('已取消订单列表', ['order_status' => 20]);
    }

    /**
     * 全部订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function all_list()
    {
        return $this->getList('全部订单列表');
    }


    /** 构建订单查询条件
     * @param array $filter
     * @throws \think\exception\DbException
     * @return array
     */
    public function buildFilter($filter = [])
    {
        $request   = request();
        $orderSn   = trim($request->get('order_no'));
        $username  = trim($request->get('username'));
        $claimTime = trim($request->get('claim_time'));
        $range     = trim($request->get('claim_range'));
        if (!empty($orderSn)) $filter['order_no'] = ['like', '%' . $orderSn . '%'];

        if (!empty($claimTime)) {
            list($start, $end) = explode(' ~ ', $claimTime);
            $filter['claim_delivery_time'] = ['between',[trim($start),trim($end)]];
        }
        if (!empty($range)) $filter['claim_time_range'] = $range;

        if (!empty($username)) {
            $uw['nickName'] = ['like', '%' . $username . '%'];
            $user           = (new User())->where([
                'nickName' => ['like', '%' . $username . '%'],
            ])->field('user_id')->select()->toArray();
            $uidList        = array_column($user, 'user_id');
            if (empty($uidList)) $uidList[] = '0';

            $filter['user_id'] = ['in', array_column($user, 'user_id')];
        }
        return $filter;
    }
    /**
     * 订单列表
     * @param $title
     * @param $filter
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function getList($title, $filter = [])
    {
        $filter = $this->buildFilter($filter);
        $model  = new OrderModel;
        $list   = $model->getList($filter);

        $range = BaseModel::$timeRange;
        return $this->fetch('index', compact('title', 'list','range'));
    }

    /**
     * 订单详情
     * @param $order_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function detail($order_id)
    {
        $detail = OrderModel::detail($order_id);
        return $this->fetch('detail', compact('detail'));
    }

    /**
     * 确认发货
     * @param $order_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery($this->postData('order'))) {
            return $this->renderSuccess('发货成功');
        }
        $error = $model->getError() ?: '发货失败';
        return $this->renderError($error);
    }

    /** 订单打印
     * @param null $order_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function print()
    {
        if ($this->request->isPost() && !empty($orderSn)) {
            $model = new OrderModel;
            foreach (explode(',', str_replace('，', ',', $orderSn)) as $sn) {
                if (empty($sn)) continue;
                $model->findPrintOrderNoOrCreate($sn);
            }
            return $this->renderSuccess();
        }
        return $this->fetch('print');
    }
}
