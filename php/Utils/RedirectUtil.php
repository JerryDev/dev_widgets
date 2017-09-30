<?php
/**
 * Http 转发 第二版
 * User: gzy <guozhenyi@yoyohr.com>
 * Date: 2017/4/24
 * Time: 15:19
 * Version: v2.0
 */

namespace App\Utils;

use Illuminate\Http\Request;

class RedirectUtil
{

    const AUTHORIZATION = 'authorization';

    const CONTENT_TYPE = 'content-type';

    /**
     * @var static
     */
    protected static $instance;


    private function __construct()
    {
    }

    public function __clone()
    {
        trigger_error('本类应用单例模式', E_USER_ERROR);
    }


    /**
     * 实例化
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }


    /**
     * 严格Http转发、原生数据包转发
     *
     * 不再在代码里处理错误和异常情况，全部改成抛出异常，由Laravel框架统一处理
     * 好处：简化代码，更直观、简洁，专注于业务逻辑
     *
     * @param Request $request
     * @param string $host
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function redirect(Request $request, $host)
    {
        // 装载头部数据
        $headers = [];
        if ($request->headers->has(static::AUTHORIZATION)) {
            $headers[static::AUTHORIZATION] = $request->headers->get(static::AUTHORIZATION);
        }
        if ($request->headers->has(static::CONTENT_TYPE)) {
            $headers[static::CONTENT_TYPE] = $request->headers->get(static::CONTENT_TYPE);
        }

        $query = $request->path();
        if (! empty($request->getQueryString())) {
            $query .= '?'. $request->getQueryString();
        }

        $fullUrl = rtrim($host, '/') . '/' . ltrim($query, '/');

        $method = $request->getRealMethod();

        $content = $request->getContent();

        $result = $this->req($method, $fullUrl, $content, $headers);

        return response()->make($result['content'], 200, $result['headers']);
    }


    /**
     * 至今封装最好的HTTP请求方法
     *
     * @param string $method
     * @param string $fullUrl
     * @param string $content
     * @param array $headers
     * @param bool $ca
     * @param int $timeout
     * @return array
     * @throws \Exception
     */
    protected function req($method, $fullUrl, $content = '', array $headers = [], $ca = false, $timeout = 30)
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
        curl_setopt($ch, CURLOPT_HEADER, false);        // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取的信息以字符串的形式返回
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);    // 设置超时限制防止死循环

        curl_setopt($ch, CURLOPT_USERAGENT, 'youpin-master'); // 模拟用户使用的浏览器

        // 解决POST的参数内容长度超过1024时无法获得response的数据的问题
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);       // 发送一个常规的Post请求
        } elseif ($method != 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($method != 'GET' && strlen($content) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }

        foreach ($headers as $k => $val) {
            unset($headers[$k]);
            $headers[strtolower($k)] = $val;
        }

        if (! empty($headers)) {
            $headArr = [];
            foreach ($headers as $key => $value) {
                $headArr[] = $key .': '. $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headArr); // 设置HTTP头字段的数组
        }

        $string = curl_exec($ch);

        /*
         * 捕获cURL错误
         */
        if ($errNo = curl_errno($ch)) {
            throw new \UnexpectedValueException(curl_strerror($errNo), 500);
//            throw new UnexpectedValueException(curl_error($ch), 500); // 这个会暴露接口地址
        }

        /*
         * 捕获HTTP异常
         */
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != '200') {
            throw new \UnexpectedValueException('ServiceException:'. $httpCode, 500);
        }

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (empty($contentType)) {
            $contentType = 'text/plain';
        }

        curl_close($ch); // 结束cURL会话

        return [
            'headers'=> [
                static::CONTENT_TYPE => $contentType
            ],
            'content'=> $string
        ];

        // curl_setopt($ch, CURLOPT_AUTOREFERER, true); // 自动设置Referer
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪301自动跳转

        // curl_setopt($ch, CURLOPT_REFERER, 'http://example.com'); // 构造来路

        // curl_setopt($ch, CURLOPT_HTTPHEADER, array( // 设置HTTP头字段的数组
        //     'Content-type:text/html; charset=utf-8',
        //     'X-FORWARDED-FOR: 127.0.0.1',
        //     'CLIENT-IP: 127.0.0.1',
        // ));

    }

}
