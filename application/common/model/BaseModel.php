<?php

namespace app\common\model;

use think\Model;
use think\Request;
use think\Session;

/**
 * 模型基类
 * Class BaseModel
 * @package app\common\model
 */
class BaseModel extends Model
{
    public static $wxapp_id;
    public static $base_url;

    ///7-8。  8-9。  9-10。  10-11。  11-12。  14-15。 15-16。  16-17。 17-18。 18- 19
    public static $timeRange = [
        [
            '今天',
            '明天',
        ],
        [
            '07:30~08:30',
            '08:30~09:30',
            '09:30~10:30',
            '10:30~11:30',
            //            '11:00~12:00',
            '15:00~16:00',
            '16:00~17:00',
            '17:00~18:00',
            '18:00~19:00',
            //            '17:00~18:00',
            //            '18:00~19:00',
        ]
    ];

    public static function calTimeRange()
    {
        $key = -1;
        $hs   = date('H:i');

        foreach (self::$timeRange[1] as $index => $value) {

            list($start,$end) = explode('~', $value);
            if ($hs <= $start) {
                $key = $index;
                break;
            }
        }
        return [
            $key == -1 ? 1 : 0,
            $key == -1 ? 0 : $key,
        ];
    }

    /**
     * 模型基类初始化
     */
    public static function init()
    {
        parent::init();
        // 获取当前域名
        self::$base_url = base_url();
        // 后期静态绑定wxapp_id
        self::bindWxappId(get_called_class());
    }

    /**
     * 后期静态绑定类名称
     * 用于定义全局查询范围的wxapp_id条件
     * 子类调用方式:
     *   非静态方法:  self::$wxapp_id
     *   静态方法中:  $self = new static();   $self::$wxapp_id
     * @param $calledClass
     */
    private static function bindWxappId($calledClass)
    {
        $class = [];
        if (preg_match('/app\\\(\w+)/', $calledClass, $class)) {
            $callfunc = 'set' . ucfirst($class[1]) . 'WxappId';
            method_exists(new self, $callfunc) && self::$callfunc();
        }
    }

    /**
     * 设置wxapp_id (store模块)
     */
    protected static function setStoreWxappId()
    {
        $session        = Session::get('yoshop_store');
        self::$wxapp_id = $session['wxapp']['wxapp_id'];
    }

    /**
     * 设置wxapp_id (api模块)
     */
    protected static function setApiWxappId()
    {
        $request        = Request::instance();
        self::$wxapp_id = $request->param('wxapp_id');
    }

    /**
     * 获取当前域名
     * @return string
     */
    protected static function baseUrl()
    {
        $request = Request::instance();
        $host    = $request->scheme() . '://' . $request->host();
        $dirname = dirname($request->baseUrl());
        return empty($dirname) ? $host : $host . $dirname . '/';
    }

    /**
     * 定义全局的查询范围
     * @param \think\db\Query $query
     */
    protected function base($query)
    {
        if (self::$wxapp_id > 0) {
            $query->where($query->getTable() . '.wxapp_id', self::$wxapp_id);
        }
    }

}
