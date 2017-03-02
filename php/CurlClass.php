<?php

/**
 * HTTP Request Class
 * HTTP 请求类
 * @author     Jerry
 * @version    0.1
 * @datetime   2015-05-25 11:33:48 Asia/Shanghai
 */
class CurlClass
{

    /**
     * 构造函数
     */
    public function __construct() {}


    /**
     * HTTP GET 获取数据
     *
     * $params = array(
     *     'host'   => '',  // 网址,构造来路所用
     *     'agent'  => '',  // 用户代理
     *     'header' => array(), // HTTP头信息
     * );
     *
     * return string
     */
    public static function get($url, $params = array())
    {

        /*
        | 想用正则提取url中的网址，这个正则写得还不太完美
        | $url = 'http://www.baidu.com/aaa/bbb.html'; // ok
        | $url = 'ftp://www.baidu.com/';              // ok
        | $url = 'ftp://www.baidu.com';               // no
        |
        */
        // preg_match('/.*?:\/\/.*?(?=\/)/i', $url, $host);

        // if (! isset($params['agent'])) {
        //     $params['agent'] = 'Mozilla/5.0 Firefox/37.0';
        // }

        // if (! isset($params['header'])) {
        //     $params['header'] = array(
        //         'Content-type:text/html; charset=utf-8',
        //         'X-FORWARDED-FOR: 127.0.0.1',
        //         'CLIENT-IP: 127.0.0.1',
        //     );
        // }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);             // 设置访问的url地址
        // bool
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // 跟踪301自动跳转
        curl_setopt($ch, CURLOPT_HEADER, false);         // 是否显示头部信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // 返回结果
        // int
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);           // 设置超时
        // string
        if (isset($params['host'])) {
            curl_setopt($ch, CURLOPT_REFERER, $host);    // 构造来路
        }
        if (isset($params['agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $params['agent']);  // 模拟用户使用的浏览器
        }
        // array
        if (isset($params['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $prams['header']); // 设置HTTP头字段的数组
        }

        $str = curl_exec($ch);

        // error capture
        if (curl_errno($ch)) {
            echo 'Curl error: ', curl_error($ch), PHP_EOL;
        }

        curl_close($ch);
        return $str;
    }


    /**
     * HTTP POST 获取数据
     *
     * $params = array(
     *     'host'   => '',      // 网址,构造来路所用
     *     'agent'  => '',      // 用户代理
     *     'header' => array(), // HTTP头信息
     *     'form'   => array(), // Post数据包
     * );
     *
     * return string
     */
    public static function post($url, $params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);            // 要访问的地址

        //bool
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);    // 自动设置Referer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪301自动跳转
        curl_setopt($ch, CURLOPT_HEADER, false);        // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_POST, true);           // 发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取的信息以字符串的形式返回
        // int
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);          // 设置超时限制防止死循环
        // string
        if (isset($params['form'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        http_build_query($params['form'])); // Post提交的数据包
        }
        if (isset($params['host'])) {
            curl_setopt($ch, CURLOPT_REFERER, $host);    // 构造来路
        }
        if (isset($params['agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $params['agent']);  // 模拟用户使用的浏览器
        }
        // array
        if (isset($params['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $prams['header']); // 设置HTTP头字段的数组
        }

        $str = curl_exec($ch); // 执行操作

        // error capture
        if (curl_errno($ch)) {
            echo 'Curl error: ', curl_error($ch), PHP_EOL;
        }

        curl_close($ch);
        return $str;
    }

}
