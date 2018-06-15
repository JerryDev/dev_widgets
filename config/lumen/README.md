# Lumen 配置

## 配置 Model

在app下新建 Models目录

并移动 app/User.php 到 app/Models/User.php (注意同时修改对User.php的引用)

## 配置日志按天记录
```php
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
// 在$app返回之前，一般放到register之后即可

/*
 * 配置日志文件为每日
 */
$app->configureMonologUsing(function(Monolog\Logger $monoLog) use ($app){
    return $monoLog->pushHandler(
        new \Monolog\Handler\RotatingFileHandler($app->storagePath().'/logs/lumen.log')
    );
});

```



## Lumen 配置 jwt-auth

下载jwt-auth库

```bash
composer require tymon/jwt-auth 1.0.0-rc.1
```

在 bootstrap/app.php 的 Register Service Providers 部分添加注册

```php
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
```

在app目录新建文件 helpers.php，并写入以下代码

```php
if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}
```

在composer.json 文件 autoload 部分 添加
```json
"autoload": {
    "files": [
        "app/helpers.php"
    ]
}
```

复制vendor/tymon/jwt-auth/config下的配置文件到 app/config目录，并重命名为jwt.php

复制vendor/laravel/lumen-framework/config下的auth.php到app/config目录

并修改guards、providers

```php
    'guards' => [
//        'api' => ['driver' => 'api'],
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
//        'api' => [
//            'driver' => 'token',
//            'provider' => 'users',
//        ],
    ],
```
```php
    'providers' => [
//        'users' => [
//            'driver' => 'database',
//            'table' => 'user',
//        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
```

修改app/Models/User.php，使其实现 \Tymon\JWTAuth\Contracts\JWTSubject 类，并实现其方法

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
```

开启 Eloquent、并载入配置文件

```php 
$app->withEloquent();

// 载入需要的配置文件
$app->configure('auth');
$app->configure('cors');
$app->configure('jwt');

```










