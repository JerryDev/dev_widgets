<?php

// 若是HTML文档则用此报头
header('Content-type: text/html;charset=utf-8');

// 若是文本则用此报头
header('Content-type: text/plain;charset=utf-8');

// 若是只展示JSON用此报头
header('Content-type: application/json;charset=utf-8');


// 关闭所有错误显示 适合生产环境 production
error_reporting(0);

// 显示所有错误 适合开发环境 development （建议使用）
error_reporting(-1);

// 显示所有错误 适合开发环境 （官方不建议使用）
error_reporting(E_ALL);

// 不显示函数废弃提示，显示其他所有错误
error_reporting(E_ALL ^ E_DEPRECATED);


// 设置页面执行无时间限制 不超时
set_time_limit(0);


// 设置时区
date_default_timezone_set('Asia/Shanghai');


// 定义页面开始执行时间，方便计算页面执行时间
define('START_TIME', microtime(true));

define(_WEBROOT_, str_replace('\\', '/', dirname(__FILE__)));


// 配置数据库
$cfg = array(
    'driver'    => 'mysql',
    'host'      => '127.0.0.1:3306',
    'username'  => 'root',
    'password'  => '',
    'database'  => 'database',
    'charset'   => 'utf8',
);

