<?php


namespace app\common\library\utils;

class IDGenerate
{
    /**
     * 生成全局唯ID 长度为27字符（$prefix除外）
     * @param string $prefix
     * @return string
     * @author kyang
     */
    public static function globalUniqueID($prefix = '')
    {
        return $prefix . str_pad(date('YmdHis') . substr(microtime(), 2, 6) . sprintf('%03d', rand(0, 999)), 27, '0', STR_PAD_LEFT);
    }
}
