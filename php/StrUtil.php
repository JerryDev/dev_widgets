<?php

/**
 * Created by PhpStorm.
 * User: gzy <guozhenyi@yoyohr.com>
 * Date: 2017/3/16
 * Time: 18:20
 */


class StrUtil
{

    const TIMEZONE_UTC = 'UTC';
    const TIMEZONE_BEIJING = 'Asia/Shanghai';

    /**
     * @var self
     */
    private static $instance;


    private function __construct()
    {
        //
    }

    private function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }


    /**
     * 实例化
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return self::$instance = new self();
        }
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







    public function getBeiJingMicroTime()
    {
        return $this->getMicroTime(self::TIMEZONE_BEIJING);
    }

    public function getUTCMicroTime()
    {
        return $this->getMicroTime(self::TIMEZONE_UTC);
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
