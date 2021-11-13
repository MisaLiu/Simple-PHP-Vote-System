# Simple-PHP-Vote-System
一个使用 PHP 开发的简易集体投票系统

## 特色
* 使用 [MDUI](https://mdui.org) 进行开发，具有漂亮的自适应 Material Design 页面样式
* 简单部署与使用，只需支持 PHP 与 MySQL 的 Web 环境就可以使用
* 轻量级，仅两张数据表和 2MB 的主程序，加载更迅速
* 完全的 Ajax 请求方式，动态响应，更可支持开发配套 App。

## 安装方式
1. Clone 本仓库源码，并上传至你的服务器
2. 导入 `vote.sql` 至你的数据库，并在表 `items` 中设置投票项目
3. 打开 `config.php`，按照注释填写设置项目
4. 打开你的站点试试吧！

## 鸣谢
* [zdhxiong/mdui](https://github.com/zdhxiong/mdui)
* [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class)
