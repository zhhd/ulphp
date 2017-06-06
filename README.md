#ulphp

##nginx部署

<pre>
<code>
location /project/ {
    index  index.html index.htm index.php l.php;
    autoindex  off;

    if (!-e $request_filename){
        rewrite  ^/project/(.*)$  /project/index.php?s=/$1  last;
    }
}
</code>
</pre>

#文档帮助

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─config                配置文件目录
├─controller            控制器层目录
├─log                   日志目录
├─logic                 逻辑层目录
├─model                 模块目录
├─static                静态文件
├─ulphp                 框架核心文件
├─view                  视图层
├─README.md             README 文件
├─index.phg             入口文件
~~~

