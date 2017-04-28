# 如何配置php并作为Apache的module运行

## 方法一

利用我已经写好的一个php脚本 run4conf.php

在此脚本里面设置必要的php根目录

然后用php.exe在命令行运行，就可以直接生成需要的php.ini

## 方法二

### 1. 修改upload保存目录

  upload_tmp_dir = "D:/server/tmp"

### 2. 修改php扩展目录

  如果要配合Apache的mod_php模块使用，就要把php扩展目录设置成绝对目录       <br>
  不然会导致php扩展加载失败，用自己系统上的路径替换其中相应的内容

  extension_dir = "D:/server/php/ext"

### 3. 加载必要的扩展

<pre>

  extension=php_bz2.dll
  extension=php_curl.dll
  extension=php_gd2.dll
  extension=php_gmp.dll
  extension=php_mbstring.dll
  extension=php_mysqli.dll
  extension=php_openssl.dll
  extension=php_pdo_mysql.dll
  extension=php_pdo_pgsql.dll
  extension=php_sockets.dll
  extension=php_xmlrpc.dll
  extension=php_xsl.dll

</pre>

### 4. 修改默认时区

  date.timezone = Asia/Shanghai

### 5. 修改session保存目录

  session.save_path = "D:/server/tmp"



