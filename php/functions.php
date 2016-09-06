<?php


/**
 * 检查是否微信登录
 * @param  boolean $redirect [description]
 * @return [type]            [description]
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
 * 计算某年某月有多少天
 */
function days_in_year_month($year, $month) {
    return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}


/**
 * 计算某年某月某日为星期几
 * @return int 0-6  0:星期天 1-6： 星期一-星期六
 */
function week_in_year_month_day($year, $month, $day) {
    $time = strtotime($year.'-'.$month.'-'.$day);
    return date('w', $time);
}
