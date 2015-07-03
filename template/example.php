<?php

// header('Content-type: text/html;charset=utf-8');
// header('Content-type: application/json;charset=utf-8');
// header('Content-type: text/plain;charset=utf-8');
// error_reporting(0);//hide the error report
error_reporting(-1); // development for all errors
// error_reporting(E_ALL ^ E_DEPRECATED);
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
/* _WEBPATH_ , _WEBROOT_ , _WEBFIX_ 三个常量很重要，不可修改*/
define('_WEBPATH_', rtrim(str_replace('\\', '/', dirname(__FILE__)),'/') );
define('_WEBROOT_', rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') );
/* 从_WEBPATH_和_WEBROOT_可以判断出项目入口文件（index.php）是否不是放在网站根目录 */
define('_WEBFIX_', str_replace(_WEBROOT_, '', _WEBPATH_));
/* 路径修复，在引入css js等文件时的路径前缀 */
define('_PUBLIC_PATH_', _WEBFIX_);
define('START_TIME', microtime(true));

// 检测php版本，要大于等于5.3
if(version_compare(PHP_VERSION, '5.3.0', '<')){
    echo 'php must be 5.3.0 or later >_<', '<br>';
    echo 'version:',phpversion();
    exit;
}

// 自5.3 起，该函数已被弃用，因此关闭该函数
if(version_compare(PHP_VERSION, '5.4.0', '<')){
    @ini_set('magic_quotes_sybase', 0);
    @set_magic_quotes_runtime(0);
    @ini_set('magic_quotes_runtime', 0);
}

//是否加载mysqli扩展
if(!extension_loaded('mysqli')){
    exit('mysqli is not loaded!');
}

if(! function_exists('curl_init')){
    exit('php_curl is not loaded!');
}



// Variables
$dopost = isset($_POST['do']) ? $_POST['do'] : '';

// $cfg = include_once dirname(__FILE__).'/config.php';

// $mysqli = new mysqli($cfg['host'], $cfg['username'], $cfg['password']);
// $mysqli -> set_charset($cfg['charset']);
// $mysqli -> select_db($cfg['database']);


Class DataUtil{

    public static $version = '1.0';

    public function md5($data){
        return md5($data);
    }


}

// echo DataUtil::$version;

// $du = new DataUtil();
// $md5 = $du -> md5('lalala');
// echo $md5;



if ($dopost == 'ajax') {

    $arr = array('a' => 111, 'b' => '中文','c' => "boy\nboy\tboy/boy\\");

    // 编码中文
    $arr['b'] = urlencode($arr['b']);

    // JSON特殊字符转义, 如果JSON字符串存储在数据库没有转义的话
    $arr['c'] = str_replace(
                  array('\\','"','/',chr(8),chr(12),chr(13),chr(10),chr(9)),
                  array('\\\\','\"','\/','\b','\f','\r','\n','\t'),
                  $arr['c']
                );

    $str = json_encode($arr);
    echo urldecode($str);
    exit;
}





?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Examples</title>
<meta name="description" content="">
<meta name="keywords" content="">

<link rel="stylesheet" type="text/css" href="">
<style type="text/css">
body{
    margin: 0;
    padding: 0;
}
</style>
</head>
<body>

<div class="wrapper">

<div>
点击跳转: <a href="//cn.bing.com">必应</a>
</div>


<div>
点击不跳转：<a id="ctrl_a_alert" href="//cn.bing.com">必应</a>
</div>



</div>




<!-- baidu cdn -->
<script type="text/javascript" src="//libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>

<!-- other cdn -->
<!-- <script type="text/javascript" src="//cdn.bootcss.com/jquery/1.9.1/jquery.min.js"></script> -->

<!-- jquery cdn -->
<!-- <script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script> -->

<script type="text/javascript">

// 普通函数调用
function test(){
    console.log('function test');
}
test(); //调用

/*
* 匿名函数  不需要调用直接执行
*/
// 原型
// ()();

// 应用1
(function(){
    console.log('execute directly');
})();

// 应用2
(function(name){
    console.log('hello '+ name);
})('Jerry');

// 应用3
(function showTime(format){

    var dtime = format;
    var dt = new Date();
    var reg = {
        'y+': dt.getFullYear(),
        'm+': dt.getMonth()+1,
        'd+': dt.getDay(),
        'h+': dt.getHours(),
        'i+': dt.getMinutes(),
        's+': dt.getSeconds(),
    }

    for(x in reg){
        if(new RegExp('('+x+')').test(dtime)){
            reg[x] = reg[x] < 10 ? '0'+reg[x] : reg[x];
            dtime = dtime.replace(RegExp.$1, reg[x]);
        }
    }

    console.log('dtime '+dtime);

    // return dtime;

})('yyyy-mm-dd hh:ii:ss');

// 应用4  包装jQuery,防止和其他用$的库冲突
(function($){
    console.log('body width: ' + $('body').width());
})(jQuery);


/*
* jQuery 相关
*/

// 第一种表示方法
jQuery(document).ready(function($){
    // ...
});

// 第二种表示方法
jQuery(function($){
    // 绑定事件最好用 事件委派 delegate
    jQuery('body').delegate('#ctrl_a_alert', 'click', function(e){
        alert('hello');
        if(e.cancelable){ // 判断默认动作是否可取消
            e.preventDefault(); //通知浏览器不要执行与事件关联的默认动作 IE无效
        }
    });
});



/*
* 定义对象
*/
var DataUtil = {
    duVersion: '1.0',
    duParse: function(data){

        var dt = data;

        if(window.JSON){
            dt = JSON.parse(data);
        }else{
            // dt = eval('('+ data + ')'); // 不安全
            dt = jQuery.parseJSON(data); // jQuery实现的方法
            // 或者引入json2.js这个js库实现的JSON.parse方法
        }
        return dt;
    },
    duAlert: function(){
        alert('test');
    }
};

console.log(DataUtil.duVersion);

console.log(DataUtil.duParse('{"a":"1","b":"222"}'));


// php给js赋值
var dopost = '<?php echo $dopost; ?>';

console.log('dopost: ' + dopost);


jQuery(function($){

    $.post('', {'do':'ajax'}, function(data){
        console.log(typeof data + ' : ' + data);

        console.log(DataUtil.duParse(data));

    });

});




</script>
</body>
</html>