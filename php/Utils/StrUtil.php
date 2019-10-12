<?php
/**
 * 随机字符串辅助类
 * User: gzy <guozhenyi@yoyohr.com>
 * Date: 2017/3/16
 * Time: 18:20
 * Version: v1.0
 */

namespace App\Utils;

class StrUtil
{

    const TZ_UTC = 'UTC';
    const TZ_PRC = 'PRC';
    const TZ_SHANGHAI = 'Asia/Shanghai';

    const POOL = 'abcdefhkmnrstwxyz123456789';

    /**
     * @var static
     */
    protected static $instance;


    private function __construct(){}

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
     * 生成数据集表的表名
     *
     * @example ds_20170317_141107_968092_ae5cf7
     *
     * @return string
     */
    public function randomDatasetName()
    {
        $string = 'ds_';
        $length = 32;

        $microTime = $this->getMicroTime(static::TZ_SHANGHAI);

        $date = date('Ymd', $microTime['sec']);

        $time = date('His', $microTime['sec']);

        $usec = substr($microTime['usec'], 2, 6);

        $string .= $date . '_' . $time . '_' . $usec . '_';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $chars = str_shuffle(str_repeat(static::POOL, $size));

            $string .= substr($chars, 0, $size);
        }

        return trim($string, '_');
    }

    /**
     * 生成数据集表的表名
     *
     * @example ds_20170317_141107_968092_ae5cf7
     * @param int $length
     * @return string
     */
    public function randomTableName($length = 32)
    {
        $string = 'ds';

        $microTime = $this->getMicroTime(static::TZ_SHANGHAI);

        $date = date('Ymd', $microTime['sec']);

        $time = date('His', $microTime['sec']);

        $usec = substr($microTime['usec'], 2, 6);

        $string .= $date . $time . $usec;

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $string .= $this->random($size);
        }

        return trim($string, '_');
    }


    /**
     * 获得指定位数的随机数
     *
     * @param int $size
     * @return string
     */
    public function random($size = 6)
    {
        $chars = str_shuffle(str_repeat(static::POOL, $size));

        return substr($chars, 0, $size);
    }


    /**
     * 有时序的随机数经sha1散列后返回的字符串
     *
     * @return string
     */
    public function sha1TimeOrderRandomString()
    {
        $seed = 6; // 种子数

        list($uSec, $sec) = explode(' ', microtime());

        $chars = str_shuffle(str_repeat(static::POOL, $seed));

        return sha1($sec . $uSec . substr($chars, 0, $seed));
    }



    /**
     * 获得指定时区的时间戳和微秒
     *
     * @param string $timeZone
     * @return array
     */
    protected function getMicroTime($timeZone = 'UTC')
    {
        $defaultTimeZone = date_default_timezone_get();

        date_default_timezone_set($timeZone);

        list($usec, $sec) = explode(' ', microtime());

        date_default_timezone_set($defaultTimeZone);

        return ['usec'=> $usec, 'sec' => $sec];
    }




}
