<?php

/**
 * MySQL database handle package
 * MySQL数据库常用操作类
 * @author     Jerry
 * @version    0.1
 * @datetime   2015-05-12 14:31:51 Asia/Shanghai
 */
class MysqlClass
{

    /**
     * MySQL server host
     * If the default port is 3306, you can omit it
     * eg:
     *   localhost:3306
     *   localhost
     *   127.0.0.1
     */
    private $host;

    /**
     * 用户名
     * eg: root
     */
    private $username;

    /**
     * 密码
     */
    private $password;

    /**
     * 数据库名
     */
    private $database;

    /**
     * 设置字符集
     */
    private $charset;

    /**
     * 数据库连接
     * type: resource
     */
    private $link;

    /**
     * 时间戳
     * 用来记录每次操作的时间
     * 执行定时ping MySQL服务器
     */
    private $dtime;


    /**
     * 构造函数
     * 在创建对象时自动调用
     * 主要作用：做一些类的初始化工作
     */
    public function __construct($host, $username, $password,
                                $database = null, $charset = 'utf8')
    {
        // Assign
        $this -> host     = $host;
        $this -> username = $username;
        $this -> password = $password;

        // Link
        $this -> connect();

        // Select db
        if (isset($database)) {
            // $this -> database = $database;
            $this -> selectDB($database);
        }

        // Set charset
        if (isset($charset)) {
            // $this -> charset = $charset;
            $this -> setCharset($charset);
        }
    }

    /**
     * 连接数据库
     *
     */
    private function connect()
    {
        $link = mysql_connect($this -> host, $this -> username, $this -> password)
            or die('LINE: '.__LINE__.',msg: Could not connect to DB Server');
        $this -> link = $link;
        $this -> dtime = time();
    }

    /**
     * 选择数据库 如果出错就停止执行
     *
     */
    public function selectDB($database)
    {
        $this -> database = $database;
        $this -> ping();
        mysql_select_db($this -> database, $this -> link)
            or die('LINE: '.__LINE__.',msg: Could not select database');
        $this -> dtime = time();
    }

    /**
     * 选择数据库 并自行处理执行成功与否
     * return bool
     */
    public function isSelectDB($database)
    {
        $this -> database = $database;
        $this -> ping();
        if(! mysql_select_db($this -> database, $this -> link)) {
            return false;
        }
        $this -> dtime = time();
        return true;
    }

    /**
     * 设置连接数据库的字符集
     */
    public function setCharset($charset)
    {
        $this -> charset = $charset;
        $this -> ping();
        if (function_exists('mysql_set_charset')) {
            mysql_set_charset($this -> charset, $this -> link)
                or die('LINE: '.__LINE__.',msg: Could not set charset');
        } else {
            mysql_query("SET NAMES {$this -> charset}", $this -> link)
                or die('LINE: '.__LINE__.',msg: Could not query set names');
        }
        $this -> dtime = time();
    }

    /**
     * Ping一个MySQL服务器连接
     * 如果没有连接则重新连接
     */
    public function ping()
    {
        if (! mysql_ping($this -> link)) {
            mysql_close($this -> link);
            $this -> connect();
        }
        $this -> dtime = time();
    }

    /**
     * Ping一个MySQL服务器连接
     * Return bool
     */
    public function isPing()
    {
        if (! mysql_ping($this -> link)) {
            return false;
        }
        $this -> dtime = time();
        return true;
    }









    /**
     * 获取数据 Select
     * Return 二维数组
     */
    public function select($strSql)
    {
        $arrRes = array();
        $this -> ping();
        $result = mysql_query($strSql, $this -> link)
            or die('LINE: '.__LINE__.',msg: select failed');
        while ($line = mysql_fetch_assoc($result)) {
            $arrRes[] = $line;
        }
        $this -> dtime = time();
        return $arrRes;
    }

    /**
     * 插入数据 Insert
     * Return int
     */
    public function insert($strSql)
    {
        $this -> ping();
        mysql_query($strSql, $this -> link)
            or die('LINE: '.__LINE__.',msg: insert filed');
        $num = mysql_affected_rows($this -> link);
        $this -> dtime = time();
        return $num;
    }

    /**
     * 更新数据 Update
     * Return bool
     */
    public function update($strSql)
    {
        $this -> ping();
        $bool = mysql_query($strSql, $this -> link)
            or die('LINE:'.__LINE__.',msg: update failed');
        $this -> dtime = time();
        return $bool;
    }

    /**
     * 删除数据 Delete
     * Return int
     */
    public function delete($strSql)
    {
        $this -> ping();
        mysql_query($strSql, $this -> link)
            or die('LINE: '.__LINE__.',msg: delete failed');
        $num = mysql_affected_rows($this -> link);
        $this -> dtime = time();
        return $num;
    }

    /**
     *
     */
    public function statement($strSql)
    {
        $this -> ping();
        mysql_query($strSql, $this -> link)
            or die('statement failed');

    }


    public function query($strSql)
    {
        $this -> ping();
        $bool = mysql_query($strSql, $this -> link);
        if($bool){
            return true;
        }
        return false;
            // or die('query failed');
        // $result = mysql_info($this -> link);
        // echo 'query:', PHP_EOL;
        // if ($result) {
        //     print_r($result);
        // } else {
        //     echo 'false', PHP_EOL;
        // }
    }

    /**
     * SHOW TABLES LIKE 'user'
     * return bool
     */
    public function show($strSql)
    {
        $arrRes = array();
        $this -> ping();
        $result = mysql_query($strSql, $this -> link)
            or die('LINE: '.__LINE__.', msg: '.mysql_error());
        while ($line = mysql_fetch_assoc($result)) {
            $arrRes[] = $line;
        }
        if(empty($arrRes)){
            return false;
        }
        return true;
    }




}
