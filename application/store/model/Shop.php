<?php
/**
 * Created by PhpStorm.
 * User: huaidao
 * Date: 2020/2/19
 * Time: 0:36
 */

namespace app\store\model;


use app\common\model\ShopModel;
use think\Db;
use think\Request;

class Shop extends ShopModel
{

    /**
     * 获取商品列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList() {
        $list = Db::table("yoshop_score_shop")
            ->join("yoshop_upload_file file", "file.file_id = img")
            ->order("score_num asc")
            ->paginate(15, false, [
            'query' => Request::instance()->request()
        ]);
        return $list;
    }

    /**
     * 添加商品
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {
        if (!isset($data['img']) || empty($data['img'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        // 添加商品
        $this->allowField(true)->save($data);
        return true;
    }
}