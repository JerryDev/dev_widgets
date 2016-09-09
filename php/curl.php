<?php 

/*
 * curl函数的一些封装方法
 * 
 *
 * @author guozhenyi(JerryDev@163.com)
 * @datetime 2016-09-09 14:11
 * 
 */


/**
 * 最简单的 HTTP GET 模拟
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function curl_x_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response =  curl_exec($ch);
    curl_close($ch);
    return $response;
}

/**
 * 最简单的 HTTP POST 模拟
 * @param  [type] $url  [description]
 * @param  array  $data [description]
 * @return [type]       [description]
 */
function curl_x_post($url, $data = array()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

/**
 * 采集图片的方法 
 * (主要在微信登录时，用来抓取微信头像保存在本地,防止微信头像失效)
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function curl_get_img($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    $res =  curl_exec($ch);
    $tArr = array(
        'jpg'=>'image/jpeg',
        'jpg'=>'image/jpg',
        'png'=>'image/png',
        'gif'=>'image/gif',
    );
    $info = curl_getinfo($ch);
    $ext = 'jpg';
    if (isset($info['content_type']) && in_array($info['content_type'], $tArr)) {
        $ext = array_search($info['content_type'], $tArr);
    }
    curl_close($ch);
    $response = array();
    $response['ext'] = $ext;
    $response['data'] = $res;
    return $response;
}










