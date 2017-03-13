<?php
/**
 * Created by PhpStorm.
 * User: gzy <guozhenyi@yoyohr.com>
 * Date: 2017/3/3
 * Time: 14:51
 */


class Curl
{


    /**
     * HTTP GET
     *
     * @param string $url
     * @param array $header
     * @return string
     * @throws \Exception
     */
    public function get($url, array $header = [])
    {
        return $this->req('GET', $url, [], $header, false, 30);
    }


    /**
     * HTTP POST
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return string
     * @throws \Exception
     */
    public function post($url, array $data = [], array $header = [])
    {
        return $this->req('POST', $url, $data, $header, false, 30);
    }


    /**
     * 采集图片的方法 (主要在微信登录时，用来抓取微信头像保存在本地,防止微信头像失效)
     *
     * @param  string $url
     * @return array
     */
    public function getPicture($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);

        $content = curl_exec($ch);

        $extArr = array(
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
        );

        $contentType = trim(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

        $result = ['ext'=> 'jpg', 'data'=> $content];
        if (array_key_exists($contentType, $extArr)) {
            $result['ext'] = $extArr[$contentType];
        }

        curl_close($ch);

        return $result;
    }


    /**
     * HTTP请求方法
     *
     * @param string $method
     * @param string $fullUrl
     * @param array $data
     * @param array $header
     * @param bool $ca
     * @param int $timeout
     * @return string
     * @throws \Exception
     */
    public function req($method, $fullUrl, array $data = [], array $header = [], $ca = false, $timeout = 30)
    {
        $method = strtoupper(trim($method));

        $cacert = getcwd() .'/cacert.pem'; // CA根证书
        if (! file_exists($cacert) || ! is_readable($cacert)) {
            $ca = false;
        }

        $ssl = substr($fullUrl, 0, 8) == 'https://' ? true : false;

        $ch = curl_init();

        if ($ssl && $ca) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 只信任CA颁布的证书
            curl_setopt($ch, CURLOPT_CAINFO, $cacert);      // CA根证书（用来验证的网站证书是否是CA颁布）
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // 检查公用名是否存在，并且是否与提供的主机名匹配
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁止cURL验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // 不检查公用名
        }

        curl_setopt($ch, CURLOPT_URL, $fullUrl);        // 地址
        curl_setopt($ch, CURLOPT_HEADER, true);        // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取的信息以字符串的形式返回
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);    // 设置超时限制防止死循环

        // 解决POST的参数内容长度超过1024时无法获得response的数据的问题
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);       // 发送一个常规的Post请求
            $header['Content-type'] = 'application/x-www-form-urlencoded';
        } elseif ($method != 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($method != 'GET' && ! empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        }

        if (! empty($header)) {
            $headArr = [];
            foreach ($header as $key => $value) {
                $headArr[] = $key .': '. $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headArr); // 设置HTTP头字段的数组
        }

        $content = curl_exec($ch);

        // 捕获cURL错误
        if ($errno = curl_errno($ch)) {
            throw new \UnexpectedValueException(curl_strerror($errno), 500);
//            throw new UnexpectedValueException(curl_error($ch), 500); // 这个会暴露内部接口地址
        }

        // 捕获HTTP异常
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != '200') {
            throw new \UnexpectedValueException($content, 500);
        }

        curl_close($ch); // 关闭CURL会话

        return $content;

        // curl_setopt($ch, CURLOPT_AUTOREFERER, true); // 自动设置Referer
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪301自动跳转

        // curl_setopt($ch, CURLOPT_REFERER, 'http://example.com'); // 构造来路

        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 Firefox/42.0'); // 模拟用户使用的浏览器

        // curl_setopt($ch, CURLOPT_HTTPHEADER, array( // 设置HTTP头字段的数组
        //     'Content-type:text/html; charset=utf-8',
        //     'X-FORWARDED-FOR: 127.0.0.1',
        //     'CLIENT-IP: 127.0.0.1',
        // ));

    }



}