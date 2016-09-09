<?php


/**
 * 简单检查是否微信登录
 * (注意：这种方式不准确，很容易被代理绕过检查，最好的方法还是集成微信授权)
 * @param  boolean $redirect 是否跳转
 * @return boolean           布尔值
 */
function check_weixin($redirect = false) {
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
        if ($redirect) {
            header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxcdff2f131e2d9a86&redirect_uri=&response_type=code&scope=snsapi_userinfo&state=NORMAL&connect_redirect=1#wechat_redirect');
            exit;
        } else {
            return false;
        }
    } else {
        return true;
    }
}


/**
 * 计算某年某月有多少天（在某个签到项目用到）
 * @param  [type] $year  [description]
 * @param  [type] $month [description]
 * @return [type]        [description]
 */
function days_in_year_month($year, $month) {
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}


/**
 * 计算某年某月某日为星期几 （在某个签到项目用到）
 * @param  [type] $year  [description]
 * @param  [type] $month [description]
 * @param  [type] $day   [description]
 * @return int 0-6       0:星期天 1-6： 星期一-星期六
 */
function week_in_year_month_day($year, $month, $day) {
    $time = strtotime($year.'-'.$month.'-'.$day);
    return date('w', $time);
}


/**
 * 为聊天消息生成 time+micro+rand 的随机值做为唯一id
 * @param  integer $length 字符串长度
 * @return string          [description]
 */
function gen_msg_id($length = 19)
{
    $string = '';

    $pool = '0123456789';
    $mt = explode(' ', microtime());

    $string = $mt[1] . substr($mt[0], 2, 6);

    while (($len = strlen($string)) < $length) {
        $size = $length - $len;
        $string .= substr(str_shuffle($pool), 0, $size);
    }

    return $string;
}


/**
 * 为微信登录用户生成随机用户名
 * @param  integer $length [description]
 * @return [type]          [description]
 */
function gen_wx_str($length = 12) {
    $username = 'wx';

    // 第一种实现 数组
    // $randArr = str_split('01234567890123456789');
    // for ($i = 0; $i < $length; $i++) {
    //     shuffle($randArr);
    //     $username .= $randArr[mt_rand(0, count($randArr)-1)];
    // }

    $scope = '0123456789';
    while (($len = strlen($username)) < $length) {
        // $size = $length - $len;
        $username .= substr(str_shuffle(str_repeat($scope, 3)), 0, $length - $len);
    }

    return $username;
}

