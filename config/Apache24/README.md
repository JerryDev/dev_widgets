# 如何配置Apache2.4

### 1. 下载

  一个不错的apache httpd 第三方编译版本                         <br>
  http://www.apachelounge.com/download/VC11/

  由于php 64位 版本 还不稳定，为了配合php，下载32位的Apache     <br>
  若配合php，可以下载php 5.5.*系列 32位 最新版

  由于是VC11，需要安装VC++2012 update 4 运行库                  <br>
  [VC++ 2012 update 4](http://www.microsoft.com/zh-CN/download/details.aspx?id=30679)


### 1. 创建 webserver 目录

  在D盘创建server目录                          <br>
  在webserver目录创建htdocs目录和tmp目录

### 2. 修改 ServerRoot

  ServerRoot "D:/server/Apache24"

### 3. 加载必要的模块

<pre>

  LoadModule headers_module modules/mod_headers.so
  LoadModule info_module modules/mod_info.so
  #LoadModule proxy_module modules/mod_proxy.so
  #LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
  LoadModule rewrite_module modules/mod_rewrite.so
  LoadModule ssl_module modules/mod_ssl.so
  LoadModule status_module modules/mod_status.so
  #LoadModule version_module modules/mod_version.so
  LoadModule vhost_alias_module modules/mod_vhost_alias.so

</pre>

### 4. 修改 ServerName

  ServerName localhost:80


### 5. 修改DocumentRoot 和 相应的Directory

<pre>
  修改为： DocumentRoot "D:/webserver/htdocs"
           &lt;Directory "D:/server/htdocs"&gt;

  生产环境下，要禁止Apache列出目录下所有文件：
  去掉Indexes选项： Options FollowSymLinks

  如果配置为FastCGI方式解析php，还需要加一个选项：
  加上ExecCGI选项： Options FollowSymLinks ExecCGI

  为了支持rewrite模块，把directory部分的AllowOverride值修改为All
  修改为： AllowOverride None
<pre>

### 6. dir_module 部分增加支持index.php为默认入口文件

<pre>
  &lt;IfModule dir_module&lt;
      DirectoryIndex index.php index.html index.htm
  &lt;/IfModule&lt;
</pre>

### 7. 把所有出现C:的盘符修改成Apache目录对应的盘符 D:/webserver/Apache24

### 8. 如果有配置虚拟主机，需要打开include部分

  去掉前面的#号： #Include conf/extra/httpd-vhosts.conf

  并修改好extra目录下的httpd-vhosts.conf 文件 (或者注释掉默认的vhost)

### 9. 以Apache + mod_php + php ts 的方式支持对php脚本的解析

  在LoadModule 部分引入php5_module

<pre>
以下为需要添加进配置文件的内容：

LoadModule php5_module "D:/webserver/php5/php5apache2_4.dll"

&lt;IfModule php5_module&gt;

    # 使Apache支持解析.php后缀的脚本 example.php.txt
    #AddHandler application/x-httpd-php .php

    # 只处理以.php结尾的文件 example.txt.php
    &lt;FilesMatch \.php$&gt;
    SetHandler application/x-httpd-php
    &lt;/FilesMatch&gt;

    # 加载dll
    LoadFile "D:/webserver/php5/libssh2.dll"
    #LoadFile "D:/webserver/php5/ssleay32.dll"
    #LoadFile "D:/webserver/php5/libeay32.dll"
    #LoadFile "D:/webserver/php5/libpq.dll"

    # 配置 php.ini 的路径
    PHPIniDir "D:/webserver/php5/"

&lt;/IfModule&gt;
</pre>

### 10. 以Apache + FastCGI + mod_fcgid + php nts 的方式支持对php脚本的解析

下载Apache的mod_fcgid模块，放到modules目录下

<pre>
以下为需要添加进配置文件的内容：

LoadModule fcgid_module modules/mod_fcgid.so

&lt;IfModule fcgid_module&gt;

    AddHandler fcgid-script .php
    FcgidWrapper "D:/server/php/php-cgi.exe" .php
    # 配置php.ini文件路径
    FcgidInitialEnv PHPRC "D:/server/php/"

&lt;/IfModule&gt;
</pre>


