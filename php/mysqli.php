<?php 


$config = array( // from Laravel
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'username'  => 'forge',
    'password'  => '',
    'database'  => 'forge',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => false,
);

// @ 符号，抑制错误，防止泄露数据库配置信息
$mysqli = @new \mysqli($config['host'], $config['username'], $config['password']);

if ($mysqli -> connect_errno) { // 数据库连接错误
    echo 'DB: connect error (', $mysqli->connect_errno, ')', PHP_EOL;
    exit;
}
if (! $mysqli -> set_charset($config['charset'])) { // 无效的字符集
    echo 'DB: invalid charset', PHP_EOL;
    exit;
}
if (! $mysqli -> select_db($config['database'])) { // 无效的数据库名
    echo 'DB: invalid database', PHP_EOL;
    exit;
}


/*
 * Example
 */

$sqlStr = 'select * from `foobar` limit 10';
$foobars = array();
if ($result = $mysqli->query($sqlStr)) {
    while ($line = $result->fetch_assoc()) {
        $foobars[] = $line;
    }
} else {
    die('DB exception');
}





