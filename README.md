# ulphp
> 框架初衷，因为其他框架太过于笨重，附加的内容比较多。
> 为了适应秒杀系统专门参考tp文档开发的简易框架。
> 仅作为学习使用，请勿用于商业项目中。

> 作者：zhd(410919571@qq.com)

### 版本V1.0.6 

> `注：自定义全局函数命名请勿和框架自带全局函数命名冲突`

> 除session函数外，为了框架性能所以只对session函数进行命名重复检测

> 在开发中我们经常会对系统进行高并发测试，为了避免产生大量session文件造成服务器iops过高影响测试结果，
> 所以开发者可以在 common\common.php文件中对session函数进行重构，以适应测试环境。

##PHP版本要求
> php5.6及以上

## nginx部署

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

## 文档中心
> http://git.oschina.net/kanpo/ulphp/wikis/home

> 文档可能和源码有部分冲突，请参照源码注释使用。

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─common                自定义函数文件     (必须，包括common.php)
├─config                配置文件目录       (可选)
├─controller            控制器层目录       (必须)
├─logic                 逻辑层目录         (可选)
├─model                 模块目录           (可选)
├─static                静态文件           (可选)
├─ulphp                 框架核心文件        (必须)
├─view                  视图层             (可选)
├─~runtime              记录日志及缓存文件   (必须，Linux权限7777)
├─README.md             README 文件        (可选)
├─index.phg             入口文件            (必须)
~~~

## 命名规范

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名
*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用小写字母和下划线（小写字母开头）的方式，例如 `id`、`user_name`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；

### 常量和配置
*   常量以大写字母和下划线命名，例如 `APP_PATH`；
*   配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；

### 数据表和字段
*   数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `user` 表和 `user_name`字段，不建议使用驼峰和中文作为数据表字段命名。

## 文件缓存原理

### 缓存格式

> 过期时间\r\n缓存数据

> 为了防止额外的负担，文件缓存未采用自动清理过期缓存的机制，但有清理过期缓存的函数。开发者
可使用linux自带定时命令对缓存文件进行定时清理。

<pre>
<code>
定时命令
crontab -l  显示所有任务
crontab -e 编辑任务
service crond start 开启服务
service crond restart 重启服务
service crond stop 停止服务
</code>
</pre>
