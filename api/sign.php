<?php 

/*
 * 一个登录、注册接口的样例
 *
 * @author guozhenyi
 *
 * @datetime 2016-08-29 16:34
 */


require_once __DIR__.'/../include/global.inc.php';

// date_default_timezone_set('Asia/Shanghai');
// session_set_cookie_params(60*60*6, '/');
// session_start();

// 定义全局返回格式
$total = array('code'=> -1, 'msg'=> '');


$mysqli = @new \mysqli(DBHost, DBUser, DBPassword);
if ($mysqli -> connect_errno) { // 数据库连接错误
    // echo 'DB: connect error (', $mysqli->connect_errno, ')', PHP_EOL;
    $total['code'] = 511;
    $total['msg'] = 'Db connect error';
    echo json_encode($total);
    exit;
}
if (! $mysqli -> set_charset('utf8mb4')) { // 无效的字符集
    // echo 'DB: invalid charset', PHP_EOL;
    $total['code'] = 511;
    $total['msg'] = 'Db invalid charset';
    echo json_encode($total);
    exit;
}
if (! $mysqli -> select_db(DBName)) { // 无效的数据库
    // echo 'DB: invalid database', PHP_EOL;
    $total['code'] = 511;
    $total['msg'] = 'Db invalid database';
    echo json_encode($total);
    exit;
}


$redirect_uri = isset($_GET['redirect_uri']) ? urldecode(trim($_GET['redirect_uri'])) : null;

$do = isset($_GET['do']) ? trim($_GET['do']) : null;

switch ($do) {
    case 'up':
    case 'reg':

        $username = isset($_POST['user_name']) ? trim($_POST['user_name']) : null;
        $password = isset($_POST['pass_word']) ? trim($_POST['pass_word']) : null;

        // if (preg_match('/[;\'\"\n\r\\\\]+/', $username)) {
        //     $total['code'] = 411;
        //     $total['msg'] = '请填写正确的用户名';
        //     echo json_encode($total, JSON_UNESCAPED_UNICODE);
        //     exit;
        // }
        if (! preg_match('/^(13[0-9]|15[0-9]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/', $username)) {
            $total['code'] = 411;
            $total['msg'] = '手机号格式不正确';
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (strlen($password) < 6) {
            $total['code'] = 411;
            $total['msg'] = '请填写6位以上密码';
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $username = $mysqli->real_escape_string($username);

        // 检查用户名是否存在
        $sql = "SELECT `id` FROM `users` WHERE `username` = '{$username}'";
        if ($result = $mysqli->query($sql)) {
            if ($result->num_rows > 0) {
                $total['code'] = 412;
                $total['msg'] = '用户名已存在！';
                echo json_encode($total, JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $total['code'] = 500;
            $total['msg'] = '系统错误:001';
            // $total['err'] = $mysqli->error;
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $nickname = $username;
        $avatar = 'assets/avatar/'. mt_rand(1,20) .'.jpg';
        // $avatar = 'assets/avatar/default.jpg';

        //存储用户注册信息,并且写入登录信息
        $password = md5($password);  // ***** md5已经不安全，建议用其他加密方式加密密码后存储 *****

        $sql = "INSERT INTO `users` (`username`,`password`,`nickname`,`avatar`,`created_at`) VALUES('{$username}','{$password}','{$nickname}','{$avatar}','" . date('Y-m-d H:i:s') . "')";
        if ($mysqli->query($sql)) {
            if ($mysqli->affected_rows > 0) {

                $_SESSION['chat_mob']['uid'] = $mysqli->insert_id;
                $_SESSION['chat_mob']['expires'] = time() + 60*60*6;

                $mysqli->query('INSERT INTO `room_user_map`(`room_id`,`user_id`,`state_map`) values("1","'. $mysqli->insert_id .'","0")');

                $total['code'] = 200;
                $total['msg'] = '注册成功！';
                $total['uri'] = $redirect_uri;
                echo json_encode($total, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $total['code'] = 413;
                $total['msg'] = '注册失败！';
                echo json_encode($total, JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $total['code'] = 500;
            $total['msg'] = '系统出错:002';
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }

        break;
    
    case 'in':
    case 'login':

        $username = isset($_POST['user_name']) ? trim($_POST['user_name']) : null;
        $password = isset($_POST['pass_word']) ? trim($_POST['pass_word']) : null;

        if (empty($username) || strlen($username) < 2) {
            $total['code'] = 411;
            $total['msg'] = '请填写用户名';
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($password) || strlen($password) < 6) {
            $total['code'] = 411;
            $total['msg'] = '请填写密码';
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $username = $mysqli->real_escape_string($username);

        $sql = "SELECT `id`,`username`,`password` FROM `users` WHERE `username` = '{$username}' LIMIT 1";
        if ($result = $mysqli->query($sql)) {
            if ($line = $result->fetch_assoc()) {
                // ***** md5已经不安全，建议用其他加密方式加密密码后存储 *****
                if ($line['password'] == md5($password)) { 

                    $_SESSION['chat_mob']['uid'] = $line['id'];
                    $_SESSION['chat_mob']['expires'] = time() + 60*60*6;

                    $total['code'] = 200;
                    $total['msg'] = '登录成功';
                    $total['uri'] = $redirect_uri;
                    echo json_encode($total, JSON_UNESCAPED_UNICODE);
                    exit;
                } else {
                    $total['code'] = 413;
                    $total['msg'] = '用户名或密码错误';
                    echo json_encode($total, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            } else {
                $total['code'] = 412;
                $total['msg'] = '用户名或密码错误';
                echo json_encode($total, JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $total['code'] = 500;
            $total['msg'] = '系统出错:003';
            // $total['err'] = $mysqli->error;
            echo json_encode($total, JSON_UNESCAPED_UNICODE);
            exit;
        }
        break;

    case 'out':
        $_SESSION['chat_mob'] = null;
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-3600,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $total['code'] = 200;
        $total['msg'] = 'sign out success';
        break;

    default:
        $total['code'] = 404;
        $total['msg'] = 'Not found';
        break;
}



echo json_encode($total, JSON_UNESCAPED_UNICODE);
exit;
