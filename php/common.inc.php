<?php 

// 引入文件的方法，不要带括号
// require_once __DIR__.'/functions.php';


// 设置默认时区
date_default_timezone_set('Asia/Shanghai');

// 这句话是为了防止使用mb_*函数时出现乱码，mb扩展内部编码竟然不是utf-8，shit!
mb_internal_encoding('UTF-8'); 


// 全局设置session有效期8个小时
session_set_cookie_params(60*60*8, '/');
session_start();



// 判断session_start是否被调用，千万不要用isset($_SESSION)
// if (empty(session_id())) { 
//     session_set_cookie_params(60*60*8, '/');
//     session_start();
// }
// session_regenerate_id(true); // 生成新的session_id 并删除旧的session文件
// $_SESSION = array(); // 清空$_SESSION


