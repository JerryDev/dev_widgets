<?php
header('Content-type: text/plain; charset=utf-8');
error_reporting(E_ALL ^ E_DEPRECATED);
// error_reporting(0);
// set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
define('START_TIME', microtime(true));

$cfg = array(
    'server'   => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'duckphp2015',
    'charset'  => 'utf8',
);



include_once(dirname(__FILE__).'/MysqlClass.php');

$db = new MysqlClass($cfg['server'], $cfg['username'], $cfg['password']);


$bool = $db -> isSelectDB($cfg['database']);

if (! $bool) {
    $db -> query(<<<EOL
    CREATE DATABASE IF NOT EXISTS `{$cfg['database']}` DEFAULT CHARACTER SET utf8
EOL
    );
    $db -> selectDB($cfg['database']);
}


$bool = $db -> show('show tables like "user"');

if (! $bool) {
    $db -> query(<<<EOL
    CREATE TABLE `test_user`(
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL DEFAULT '',
      PRIMARY KEY(`id`)
    )
EOL
    );
}



echo 'ok';
















// $result = $db -> insert('insert into `test`(`name`) values("boy")');

// print_r($result);







exit;


