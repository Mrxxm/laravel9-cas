## CAS认证流程

![](https://img3.doubanio.com/view/photo/l/public/p2878009640.jpg)

## 1.安装PHP8.1.0 - for Mac

```
1.生成configure文件

$ ./buildconf --force

$ ./configure --prefix=/usr/local/php/81 \
--with-config-file-path=/usr/local/php/81/etc \
--with-config-file-scan-dir=/usr/local/php/81/etc/conf.d \
--enable-fpm \
--with-fpm-user=hptown \
--with-fpm-group=admin \
--with-mysqli \
--with-pdo-mysql \
--with-iconv-dir=/usr/local \
--enable-short-tags \
--with-zlib \
--with-jpeg \
--with-libxml \
--enable-xml \
--disable-rpath \
--enable-bcmath \
--enable-shmop \
--enable-sysvsem \
--enable-inline-optimization \
--with-curl \
--enable-mbregex \
--enable-mbstring \
--enable-ftp \
--with-mhash \
--enable-pcntl \
--enable-sockets \
--with-xmlrpc \
--enable-soap \
--without-pear \
--disable-fileinfo \
--enable-maintainer-zts \
--enable-mysqlnd \
--with-iconv=/usr/local/Cellar/libiconv/1.16 \
--without-pear \
--disable-phar \
--with-freetype=/usr/local/Cellar/freetype/2.11.1

2.报错一

configure: error: bison 3.0.0 or later is required to generate PHP parsers (excluded versions: none).

$ brew install bison
$ echo 'export PATH="/usr/local/opt/bison/bin:$PATH"' >> ~/.bash_profile
$ source .bash_profile
$ source .zshrc
$ 重启终端

3.报错二

configure: error: re2c 0.13.4 is required to generate PHP lexers.

$ brew install re2c

4.Warning

configure: WARNING: unrecognized options: --with-iconv-dir, --enable-inline-optimization, --with-xmlrpc, --enable-maintainer-zts

5.编译安装

$ sudo make && sudo make install

6.环境变量添加

.bash_profile 文件中添加

#------------------------------------ PHP -------------------------------------------#

export PATH=/usr/local/php/81/bin:$PATH
export PATH=/usr/local/php/81/etc:$PATH
export PATH=/usr/local/php/81/sbin:$PATH


function php81_start() {
    echo -e "PHP-fpm启动"
    sudo /usr/local/php/81/sbin/php-fpm
}

7.目录拷贝

➜  php-8.1.0 sudo cp php.ini-development /usr/local/php/81/etc/php.ini

➜  etc sudo cp php-fpm.conf.default php-fpm.conf
➜  php-fpm.d sudo cp www.conf.default www.conf

修改：sudo vim /usr/local/php/81/etc/php-fpm.d/www.conf
     user = hptown
     group = admin

     listen = 127.0.0.1:9810

8.安装相关扩展

2905  cd ext
 2906  ll
 2907  cd fileinfo
 2908  ll
 2909* which phpize
 2910  /usr/local/php/81/bin/phpize (执行当前命令生成configure文件)
 2911  ll
 2912* which php-config
 2913  ./configure --with-php-config=/usr/local/php/81/bin/php-config (对应扩展目录下编译安装)
 2914  make
 2915  sudo make install

9.php.ini文件中添加扩展

extension=phar
extension=openssl
extension=fileinfo
extension=redis
```

## 2.安装Laravel9 - for Mac

```
1.安装laravel9

$ composer create-project laravel/laravel=9.0 laravel9

2.报错一

zsh: no matches found: laravel/laravel=9.*

➜ setopt no_nomatch
➜ source ~/.zshrc

3.报错二(前面已安装phar将不会再报错)

PHP's phar extension is missing. Composer requires it to run. Enable the extension or recompile php without --disable-phar then try again.

➜  ext cd phar
➜  phar which
➜  phar which phpize
/usr/local/php/81/bin/phpize
➜  phar /usr/local/php/81/bin/phpize
➜  phar which php-config
/usr/local/php/81/bin/php-config
➜  phar ./configure --with-php-config=/usr/local/php/81/bin/php-config
```

## 3.项目准备

```
1.前置准备

Apache-tomcat9.0.24

2.客户端MTV子系统、客户端Music子系统

3.后台认证系统

4.域名准备(本地hosts文件添加)

127.0.0.1 www.mtv.com
127.0.0.1 www.music.com
127.0.0.1 www.laravel-cas.com

5.给laravel-cas.com签发本地证书，供https访问

➜  sso ll
➜  sso openssl genrsa -des3 -out server.key 4096
Generating RSA private key, 4096 bit long modulus
......++
...................++
e is 65537 (0x10001)
Enter pass phrase for server.key:
140736201282504:error:28069065:lib(40):UI_set_result:result too small:/BuildRoot/Library/Caches/com.apple.xbs/Sources/libressl/libressl-22.50.2/libressl/crypto/ui/ui_lib.c:834:You must type in 4 to 1023 characters
Enter pass phrase for server.key:
Verifying - Enter pass phrase for server.key:
➜  sso ll
total 8
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:30 server.key
➜  sso openssl rsa -in server.key -out server.key
Enter pass phrase for server.key:
writing RSA key
➜  sso ll
total 8
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:31 server.key
➜  sso openssl req -new -key server.key -out server.csr
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) []:CN
State or Province Name (full name) []:zhejiang
Locality Name (eg, city) []:hangzhou
Organization Name (eg, company) []:doublex-man
Organizational Unit Name (eg, section) []:doublex-man
Common Name (eg, fully qualified host name) []:www.laravel-cas.com
Email Address []:1377789****@163.com


Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:123456
➜  sso ll
total 16
-rw-r--r--  1 xuxiaomeng  staff   1.8K Feb  5 12:33 server.csr
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:31 server.key
➜  sso openssl genrsa -des3 -out ca.key 4096
Generating RSA private key, 4096 bit long modulus
............................................................................................................................................................................................................................................................................................................................................................++
.............................................................................++
e is 65537 (0x10001)
Enter pass phrase for ca.key:
Verifying - Enter pass phrase for ca.key:
➜  sso ll
total 24
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:34 ca.key
-rw-r--r--  1 xuxiaomeng  staff   1.8K Feb  5 12:33 server.csr
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:31 server.key
➜  sso openssl req -new -x509 -key ca.key -out ca.crt -days 3652
Enter pass phrase for ca.key:
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) []:CN
State or Province Name (full name) []:zhejiang
Locality Name (eg, city) []:hangzhou
Organization Name (eg, company) []:doublex-man
Organizational Unit Name (eg, section) []:doublex-man
Common Name (eg, fully qualified host name) []:www.laravel-cas.com
Email Address []:1377789****@163.com
➜  sso ll
total 32
-rw-r--r--  1 xuxiaomeng  staff   2.0K Feb  5 12:36 ca.crt
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:34 ca.key
-rw-r--r--  1 xuxiaomeng  staff   1.8K Feb  5 12:33 server.csr
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:31 server.key
➜  sso openssl x509 -req -days 365 -in server.csr -CA ca.crt -CAkey ca.key -CAcreateserial -out server.crt
Signature ok
subject=/C=CN/ST=zhejiang/L=hangzhou/O=doublex-man/OU=doublex-man/CN=www.laravel-cas.com/emailAddress=1377789****@163.com
Getting CA Private Key
Enter pass phrase for ca.key:
➜  sso ll
total 48
-rw-r--r--  1 xuxiaomeng  staff   2.0K Feb  5 12:36 ca.crt
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:34 ca.key
-rw-r--r--  1 xuxiaomeng  staff    17B Feb  5 12:36 ca.srl
-rw-r--r--  1 xuxiaomeng  staff   2.0K Feb  5 12:36 server.crt
-rw-r--r--  1 xuxiaomeng  staff   1.8K Feb  5 12:33 server.csr
-rw-r--r--  1 xuxiaomeng  staff   3.2K Feb  5 12:31 server.key
➜  sso pwd
/Users/xuxiaomeng/Documents/ssl/sso

5.nginx配置

server {

    listen 443 ssl;

    ssl_certificate /Users/hptown/Documents/ssl/laravel-cas/server.crt;
    ssl_certificate_key /Users/hptown/Documents/ssl/laravel-cas/server.key;
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;

    server_name  www.laravel-cas.com;
    root         /var/www/laravel9/public;
    charset      utf-8;

    error_log /var/log/nginx/laravel9.error.log;
    access_log /var/log/nginx/laravel9.access.log;

    location / {
        index index.php;
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /index.php/$1 last;
    }

    location ~ ^/(index)\.php(/|$) {
        fastcgi_pass   127.0.0.1:9810;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
        fastcgi_param  HTTPS              off;
        fastcgi_param HTTP_X-Sendfile-Type X-Accel-Redirect;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 8 128k;
    }
}
```

## Chrome访问https链接失败问题

```
1.https访问失败问题

* 浏览器页面

盲输：thisisunsafe
```

## Laravel相关开发问题

```
1.csrf问题

* VerifyCsrfToken中间件中添加对应不需要认证的路由；
* 表单中添加@csrf

2.跨域问题

* cors.php添加对应路由
* cors.php参数'supports_credentials' => true

3.cookie设置问题，并设置samesite属性

* 控制器Action - doLogin

* withCookie方法使用

public function doLogin(Request $request)
{
    $cookie = $this->setCookie(‘xxm', ‘cas’);

    return redirect($request->get('returnUrl') . '?tmpTicket=' . $tmp_ticket)->withCookie($cookie);
}

protected function setCookie(string $key, string $value, Response $response)
{
    return \cookie($key, $value, 60, '/', 'laravel-cas.com', true, false, false, 'none');
}

```
