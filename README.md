# ulphp

### 版本V1.0.6 

> `注：自定义函数文件命名请勿和框架自带命名冲突`

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