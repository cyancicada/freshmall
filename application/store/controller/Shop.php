<?php

namespace app\store\controller;

use app\store\model\Shop as shopModel;

/**
 * 商品管理控制器
 * Class Goods
 * @package app\store\controller
 */
class Shop extends Controller
{
    /**
     * 商品列表(换购中)
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new ShopModel;
        $list = $model->getList();
        return $this->fetch('index', compact('list'));
    }


    /**
     * 删除商品
     * @param $score_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($score_id)
    {
        $model = ShopModel::get($score_id);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 添加商品
     * @return array
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $model = new ShopModel;
        if (!$this->request->isAjax()) {
            return $this->fetch('add');
        }
        if ($model->add($this->request->post())) {
            return $this->renderSuccess('添加成功', url('shop/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError(json_encode($this->request->post(), true));
    }
}
