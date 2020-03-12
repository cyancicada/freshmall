<?php

namespace app\store\controller\setting;

use app\store\controller\Controller;
use app\store\model\DeliveryRule;
use app\store\model\Region;
use app\store\model\Delivery as DeliveryModel;
use think\Db;
use think\Request;

/**
 * 配送设置
 * Class Delivery
 * @package app\store\controller\setting
 */
class Delivery extends Controller
{
    /**
     * 配送模板列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new DeliveryModel;
        $list = $model->getList();
        return $this->fetch('index', compact('list'));
    }

    /**
     * 删除模板
     * @param $delivery_id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delete($delivery_id)
    {
        $model = DeliveryModel::detail($delivery_id);
        if (!$model->remove()) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 添加配送模板
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            // 获取所有地区
            $regionData = [];
            $provinceList = Region::getIdByPId();
            return $this->fetch('add', compact('regionData','provinceList'));
        }
        if ($this->request->post('pid')){
            $pid = $this->request->post('pid');
            return $this->renderSuccess('添加成功',null, Region::getIdByPId($pid));
        }
        // 新增记录
        $model = new DeliveryModel;
        if ($model->add($this->postData('delivery'))) {
            return $this->renderSuccess('添加成功', url('setting.delivery/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑配送模板
     * @param $delivery_id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit($delivery_id=0)
    {
        // 模板详情
        $model = DeliveryModel::detail($delivery_id);
        if (!$this->request->isAjax()) {
            // 获取所有地区
            $regionData = [];
            $provinceList = Region::getIdByPId();
            $info = DeliveryRule::getAllRulesByDeliveryId($delivery_id);
            foreach ($info as $item){
                $area=Region::useGlobalScope(false)->whereIn('id',$item->region)
                    ->order('level','asc')->field(['group_concat(name) AS name'])->find()->toArray();
                $item->area = $area['name'];
            }
            return $this->fetch('edit', compact('regionData','model','provinceList','info'));
        }
        if ($this->request->post('pid')){
            $pid = $this->request->post('pid');
            return $this->renderSuccess('添加成功',null, Region::getIdByPId($pid));
        }
        // 更新记录
        if ($model->edit($this->postData('delivery'))) {
            return $this->renderSuccess('更新成功', url('setting.delivery/index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }

}
