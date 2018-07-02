<?php 

/*
 *  一个微信内置webview里的授权机制 样例
 *  实现了微信一键登录（其实就是查询openid是否已绑定用户账号）
 *  实现了给当前用户ID绑定微信openid
 *  实现了创建新用户并绑定微信openid
 *
 *  使用挺麻烦的，需要微信服务号，并且已认证
 *  还需要有自己的已备案的域名绑定才行
 *
 *  平时用不到
 *
 *  @author guozhenyi (JerryDev@163.com)
 *  @datetime 2016-09-09 14:08
 * 
 */





require_once __DIR__.'/include/global.inc.php';


// error_reporting(E_ALL);
// ini_set('display_errors', 1);


function curl_x_get($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response =  curl_exec($ch);
    curl_close($ch);
    return $response;
}

function curl_x_post($url, $data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function wx_get_token($appid, $appkey, $code) {
    $wx_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appkey.'&code='.$code.'&grant_type=authorization_code';
    return curl_x_get($wx_token_url);
}

function wx_get_userinfo($token, $openid) {
    $wx_userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid;
    return curl_x_get($wx_userinfo_url);
}

function gen_weixin_username($length = 12) {
    $username = 'wx';

    // 第一种实现 数组
    // $randArr = str_split('01234567890123456789');
    // for ($i = 0; $i < $length; $i++) {
    //     shuffle($randArr);
    //     $username .= $randArr[mt_rand(0, count($randArr)-1)];
    // }

    $scope = '0123456789';
    while (($len = strlen($username)) < $length) {
        // $size = $length - $len;
        $username .= substr(str_shuffle(str_repeat($scope, 3)), 0, $length - $len);
    }

    return $username;
}

// header('Content-Type: text/html; charset=utf-8');

// 如果不为微信登录则跳转到首页，不授权
// if (! check_weixin()) {
//     header('Location: ./index.php');
//     exit;
// }

/*
 *********************************************************
 *  注意：墻裂不建议把数据库配置信息定义为常量！
 *  这里是领导埋的坑，我不背锅。 
 *  我喜欢用config.php return array()的形式定义配置信息
 *********************************************************
 */
try {
    $dsn = 'mysql:host='. DBHost .';dbname='. DBName .';charset=utf8mb4';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 抛出异常
        // PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8mb4', // 1002 => 'set names utf8mb4'
    );
    $pdo = new PDO($dsn, DBUser, DBPassword, $options);
    $pdo->exec('SET NAMES utf8mb4');
    // $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // false 用原生预处理
} catch (PDOException $e) {
    echo 'DB error', '<br>', PHP_EOL;
    echo $e->getMessage();
    exit;
}

// session uid
$uid = isset($_SESSION['chat_mob']['uid']) ? $_SESSION['chat_mob']['uid'] : 0;



$code = isset($_GET['code']) ? trim($_GET['code']) : null;
$state = isset($_GET['state']) ? trim($_GET['state']) : null;

if (!empty($state)) {
    if ($state != $_SESSION['chat_mob']['oauth2_wx']['state']) {
        echo 'CSRF stack';
        exit;
    }
    unset($_SESSION['chat_mob']['oauth2_wx']['state']);

    if (empty($code)) {
        echo '<h3>您取消了授权</h3><br>', PHP_EOL;
        echo '<div style="font-size:12px;color:#999;">3秒后关闭...<div><br>';
        echo '<script>setTimeout("window.close()",3000);</script>';
        exit;
    }

    $tokenStr = wx_get_token(WX_MP_APPID, WX_MP_APPKEY, $code);
    if (!json_decode($tokenStr)) {
        echo '<h3>微信服务器获取凭证异常，请稍后再试</h3>';
        exit;
    }
    $tokenArr = json_decode($tokenStr, true);
    if (isset($tokenArr['errcode'])) {
        echo 'error: '.$tokenArr['errcode'].' '.$tokenArr['errmsg'];
        exit;
    }

    $tokenArr['expires_at'] = time() + $tokenArr['expires_in'] - 60*5;
    $tokenArr['refresh_at'] = time() + 3600*24*29;


    /*  判断用户是否用微信登录过直播间 */
    /*  这里不做判断，应该要做绑定新的微信账号的功能 */

    // 判断是否有绑定openid的UID
    try {
        $sql = 'select `id` from `users` where `openid` = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $tokenArr['openid'], PDO::PARAM_STR);
        $stmt->execute();
        if ($line = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['chat_mob']['uid'] = $line['id'];
            $_SESSION['chat_mob']['expires'] = time() + 60*60*6;
            header('Location: ./index.php?t='.time());
            exit;
        }
        $stmt->closeCursor();
    } catch(PDOException $e) {
        echo 'DB error', __LINE__, '<br>', PHP_EOL;
        echo $e->getMessage();
        exit;
    }


    $userinfo = wx_get_userinfo($tokenArr['access_token'], $tokenArr['openid']);
    if (!json_decode($userinfo)) {
        echo '<h3>微信服务器获取用户信息异常，请稍后再试</h3>';
        exit;
    }
    $userArr = json_decode($userinfo, true);
    if (isset($userArr['errcode'])) {
        echo 'error: '.$userArr['errcode'].' '.$userArr['errmsg'];
        exit;
    }

    $nickname = preg_replace("/[^\x{0000}-\x{d7ff}\x{f900}-\x{ffff}]/u", '', $userArr['nickname']);
    // $nickname = $userArr['nickname'] ?: '';
    $sex = $userArr['sex'] == '1' ? '1' : ($userArr['sex'] == '2' ? '0' : '2');
    $avatar = empty($userArr['headimgurl']) ? null : $userArr['headimgurl'];


    if (empty($uid)) { // 生成新用户

        $username = gen_weixin_username();
        $sql = 'SELECT count(*) FROM `users` WHERE `username`=? LIMIT 1';
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $username, PDO::PARAM_STR);
            do {
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $username = gen_weixin_username();
                    $stmt->closeCursor();
                    continue;
                }
                $stmt->closeCursor();
            } while(false);
        } catch (PDOException $e) {
            echo 'DB error ', __LINE__, '<br>', PHP_EOL;
            echo $e->getMessage;
            exit;
        }

        $password = md5($username);
        $role_id = '1';
        $avatar = empty($avatar) ? ('assets/avatar/'. mt_rand(1,20) .'.jpg') : $avatar;

        $data = array(
            $username,
            $password,
            $role_id,
            $nickname,
            $sex,
            $avatar,
            $tokenArr['access_token'],
            $tokenArr['expires_in'],
            $tokenArr['expires_at'],
            $tokenArr['refresh_token'],
            $tokenArr['refresh_at'],
            $tokenArr['openid'],
            date('Y-m-d H:i:s')
        );
        $sql = 'INSERT INTO `users`(`username`,`password`,`role_id`,`nickname`,`sex`,`avatar`,`access_token`,`expires_in`,`expires_at`,`refresh_token`,`refresh_at`,`openid`,`created_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
        try {
            $stmt = $pdo->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue($key+1, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $user_id = $pdo->lastInsertId();
                $pdo->exec('INSERT INTO `room_user_map`(`room_id`,`user_id`,`state_map`) values("1",'. $user_id .',"0")');
                $_SESSION['chat_mob']['uid'] = $user_id;
                $_SESSION['chat_mob']['expires'] = time() + 60*60*6;
                header('Location: ./index.php?t='.time());
                exit;
            } else {
                header('Location: ./error.php?msg='.urlencode('微信自动注册失败'));
                exit;
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo 'DB error ', __LINE__, '<br>', PHP_EOL;
            echo $e->getMessage;
            exit;
        }

    } else { // uid 绑定 openid

        $data = array(
            '`nickname`='. $pdo->quote($nickname),
            '`sex`='. $pdo->quote($sex),
            '`access_token`='. $pdo->quote($tokenArr['access_token']),
            '`expires_in`='. $pdo->quote($tokenArr['expires_in']),
            '`expires_at`='. $pdo->quote($tokenArr['expires_at']),
            '`refresh_token`='. $pdo->quote($tokenArr['refresh_token']),
            '`refresh_at`='. $pdo->quote($tokenArr['refresh_at']),
            '`openid`='. $pdo->quote($tokenArr['openid']),
        );
        if (!empty($avatar)) {
            $data[] = '`avatar`='. $pdo->quote($avatar);
        }
        $sql = 'UPDATE `users` SET '. implode(',', $data) . ' where `id`='. $pdo->quote($uid);

        try {
            if ($pdo->exec($sql) !== false) {
                header('Location: ./index.php?'.time());
                exit;
            } else {
                header('Location: ./error.php?msg='.urlencode('系统好忙，请稍后再试'));
                exit;
            }
        } catch(PDOException $e) {
            echo 'DB error ', __LINE__, '<br>', PHP_EOL;
            echo $e->getMessage();
            exit;
        }

    }

    exit;

} else {

    // 生成唯一随机串防CSRF攻击
    $_SESSION['chat_mob']['oauth2_wx']['state'] = $state = sha1(uniqid(mt_rand(), true));

    $wx_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WX_MP_APPID.'&redirect_uri='.urlencode(WX_MP_CALLBACK).'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
    header('Location: '.$wx_code_url);
    exit;

}




echo '咦，你在做什么呢！';
echo '<h1 style="font-size:3em;">:(</h1>';

exit;


