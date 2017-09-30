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

    const TZ_SHANGHAI = 'Asia/Shanghai';

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

        $microTime = $this->getBeiJingMicroTime();

        $date = date('Ymd', $microTime['sec']);

        $time = date('His', $microTime['sec']);

        $usec = substr($microTime['usec'], 2, 6);

        $string .= $date . '_' . $time . '_' . $usec . '_';

        $pool = 'abcdefghjkmnpqrstwxyz123456789';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $chars = str_shuffle(str_repeat($pool, $size));

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

        $microTime = $this->getBeiJingMicroTime();

        $date = date('Ymd', $microTime['sec']);

        $time = date('His', $microTime['sec']);

        $usec = substr($microTime['usec'], 2, 6);

        $string .= $date . $time . $usec;

        $pool = 'abcdefghjkmnpqrstwxyz123456789';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $chars = str_shuffle(str_repeat($pool, $size));

            $string .= substr($chars, 0, $size);
        }

        return trim($string, '_');
    }





    public function getBeiJingMicroTime()
    {
        return $this->getMicroTime(static::TZ_SHANGHAI);
    }

    public function getUTCMicroTime()
    {
        return $this->getMicroTime(static::TZ_UTC);
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
