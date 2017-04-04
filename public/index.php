<?php
header('content-type:text/html;charset=utf8');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// ini_set('session.cookie_domain', ".domain.com");//跨域访问Session
// [ 应用入口文件 ]
ini_set('session.cookie_domain',".domain.com");//跨域访问Session 
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
// //	绑定当前访问到index模块 
// define('BIND_MODULE','test');