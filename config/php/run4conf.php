<?php

/*
* 这是一个在PHP CLI 命令行下运行的php文件
* 把php.ini里面需要配置的地方，一次性通过本脚本修改完成
*
* 使用方法：
*     1.把本文件放在php的根目录
*     2.在命令行运行 php cli_php_conf.php
*
*    会在本目录下生成一个php.ini文件
*
*    到底是用用于开发环境的配置文件php.ini-development
*    还是用生产环境的配置文件php.ini-production
*    可以通过在本脚本中通过配置变量来实现
*
* 注意：1.本脚本暂时只测试了修改php5.5.26的配置文件
*           其他版本的配置文件由于没有对比差异，不保证能够成功
*       2.此命令只在windows下做了测试，Linux涉及权限分配需要另说
*/

error_reporting(-1);
// error_reporting(E_ALL ^ E_DEPRECATED);
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
define('_PATH_', rtrim(str_replace('\\', '/', dirname(__FILE__)),'/'));




$cfg = array(
    // 需要读取的配置文件名
    'filename' => 'php.ini-development',
    // 'filename' => 'php.ini-production',
    // 另存为的文件名
    'savefile' => 'php.ini',
    // 配置php根目录
    'phpdir' => 'D:/webserver/php5',
);

$data = array(
    array('; extension_dir = "ext"',      'extension_dir = "D:/webserver/php5/ext"'),
    array(';extension=php_bz2.dll',       'extension=php_bz2.dll'),
    array(';extension=php_curl.dll',      'extension=php_curl.dll'),
    array(';extension=php_gd2.dll',       'extension=php_gd2.dll'),
    array(';extension=php_gmp.dll',       'extension=php_gmp.dll'),
    array(';extension=php_mbstring.dll',  'extension=php_mbstring.dll'),
    // array(';extension=php_mysql.dll',     'extension=php_mysql.dll'),
    array(';extension=php_mysqli.dll',    'extension=php_mysqli.dll'),
    array(';extension=php_openssl.dll',   'extension=php_openssl.dll'),
    array(';extension=php_pdo_mysql.dll', 'extension=php_pdo_mysql.dll'),
    // array(';extension=php_pdo_pgsql.dll', 'extension=php_pdo_pgsql.dll'),
    // array(';extension=php_pdo_sqlite.dll','extension=php_pdo_sqlite.dll'),
    // array(';extension=php_pgsql.dll',     'extension=php_pgsql.dll'),
    // array(';extension=php_soap.dll',      'extension=php_soap.dll'),
    array(';extension=php_sockets.dll',   'extension=php_sockets.dll'),
    // array(';extension=php_sqlite3.dll',   'extension=php_sqlite3.dll'),
    // array(';extension=php_xmlrpc.dll',    'extension=php_xmlrpc.dll'),
    // array(';extension=php_xsl.dll',       'extension=php_xsl.dll'),
    array(';date.timezone =',             'date.timezone = Asia/Shanghai'),
    array(';session.save_path = "/tmp"',  'session.save_path = "D:/webserver/tmp"'),
    array(';upload_tmp_dir =',            'upload_tmp_dir = "D:/webserver/tmp"'),
);


$filePath = $cfg['phpdir'].'/'.$cfg['filename'];

if (! file_exists($filePath)) {
    exit('file is not exist: ' . $filePath);
}
if (! is_file($filePath)) {
    exit('not a file:' . $filePath);
}
if (! is_readable($filePath)) {
    exit('file is not readable: ' . $filePath);
}


$content = file_get_contents($filePath);


foreach ($data as $key => $value) {
    $content = str_replace($value[0], $value[1], $content);
}


$res = file_put_contents($cfg['phpdir'].'/'.$cfg['savefile'], $content);

if (! $res) {
    echo 'save php.ini fail';
}

echo 'ok!', PHP_EOL;
echo 'create php.ini success!', PHP_EOL;
echo 'dir: ', $cfg['phpdir'],'/',$cfg['savefile'];

exit;













