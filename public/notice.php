<?php

// [ 支付通知入口文件 ]

// 手动定义路由
$_GET['s'] = isset($_GET['s'] ) ? $_GET['s']  : '/task/notify/order';

// 定义运行目录
define('WEB_PATH', __DIR__ . '/');

// 定义应用目录
define('APP_PATH', WEB_PATH . '../application/');

// 加载框架引导文件
require WEB_PATH . '../thinkphp/start.php';
