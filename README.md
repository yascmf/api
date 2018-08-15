# api

本项目处于WIP开发进行中，代码特性未稳定，请勿下载使用。基于 `Laravel/Lumen` 构建的API服务。


### 安装说明

>   本项目基础数据库表结构兼容原 [yascmf/base](https://github.com/yascmf/base) 项目，提供迁移文件供新项目开发与迁移使用。在原有的 `base` 数据库表基础上，增加了以下表：

- tags 标签表
- article_tag_relation 文章与标签关系表


```bash
git clone https://github.com/yascmf/api.git
cd api
cp .env.example .env
vim .env
// 修改相关配置，如APP_KEY,数据库账号密码等
// APP_KEY 可以去 http://tool.c7sky.com/password/ 随机生成32位长度的字符串 配置上去
composer install -vvv
php artisan migrate
php -S 127.0.0.1:9999 -t public
```


### 二次开发

需要开发者熟悉 `PHP` 与 `Laravel/Lumen` 框架。

参考资源：

- [Laravel](https://laravel.com)
- [Lumen中文文档](https://laravel-china.org/docs/lumen/5.5)