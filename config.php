<?php
    if (!IN_STATION) die();
    $_CONFIG = array();

    $_CONFIG['db']['host']     = '127.0.0.1';     // 数据库主机
    $_CONFIG['db']['port']     = 3306;            // 数据库端口
    $_CONFIG['db']['username'] = 'HIMlaoS_Misa';  // 数据库用户名
    $_CONFIG['db']['password'] = 'TestSQL114514'; // 数据库密码
    $_CONFIG['db']['name']     = 'vote';          // 数据库名称
    $_CONFIG['db']['prefix']   = '';              // 数据库表名前缀
    $_CONFIG['db']['charset']  = 'utf8';          // 数据库编码

    $_CONFIG['site']['name'] = '投票系统';         // 站点名称

    $_CONFIG['vote']['votesPerPerson'] = 1;       // 指定每人可投几票
    $_CONFIG['vote']['totalPerson']    = 0;       // 指定一共有多少人投票，设置为 0 则不使用该参数
?>