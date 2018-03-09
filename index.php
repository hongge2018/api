<?php

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die ('require PHP > 5.4.0 !');
}
// 网站路径
define('SITE_PATH', str_replace('\\', '/', __DIR__ . '/'));
// 定义应用目录
define('APP_NAMESPACE', 'zzcms');
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

// 加载框架引导文件
require __DIR__ . '/vendor/thinkphp/start.php';


//think\Build::run ( include APP_PATH . 'build . php' );