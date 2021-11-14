<?php
    /**
     *  版权声明：
     * 本套程序由 MisaLiu（@HIMlaoS_Misa）开发，仅允许非商业性质的传播。
     * 任何衍生程序都应该遵守本程序的开源协议。
     */
    define('IN_STATION', true);
    require_once('config.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8" />
        <meta name="viewport" content="width=device-width" />
        <meta name="author" content="MisaLiu @HIMlaoS_Misa" />
        <title><? echo $_CONFIG['site']['name'] ?></title>
        <link rel="stylesheet" href="./css/mdui.min.css" />
        <script src="./js/mdui.min.js"></script>
        <script>
            const totalPerson    = <? echo $_CONFIG['vote']['totalPerson'] ?>;
            const votesPerPerson = <? echo $_CONFIG['vote']['votesPerPerson'] ?>;
        </script>
        <style>
            html, body {
                scroll-behavior: smooth;
            }

            .mdui-card-media {
                height: 160px;
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
            }
            .mdui-card-primary {
                display: flex;
                padding-top: 16px;
            }

            .mdui-card-primary h2 {
                margin: 0 16px 0 0;
                padding: 5px 0;
            }

            .mdui-card-primary p {
                margin: 10px 0;
            }

            .mdui-card-primary button {
                margin-left: auto;
            }
        </style>
    </head>

    <body class="mdui-appbar-with-toolbar mdui-drawer-body-left mdui-loaded mdui-theme-primary-indigo mdui-theme-accent-pink mdui-theme-layout-light">
        <!--这里的主题（即 “mdui-theme-*”）是默认的，如果你有需求的话，可以到 mdui.org/docs/ 自行选择主题配色-->
        <header class="mdui-appbar mdui-appbar-fixed">
            <div class="mdui-toolbar mdui-color-theme">
                <button class="mdui-btn mdui-btn-icon mdui-ripple" mdui-drawer="{target:'#drawer',swipe:true}"><i class="mdui-icon material-icons">menu</i></button>
                <a href="#" onclick="window.scrollTo(0,0);return false;" class="mdui-typo-title"><? echo $_CONFIG['site']['name'] ?></a>
            </div>
        </header>

        <drawer class="mdui-drawer" id="drawer">
            <div class="mdui-list">
                <a href="#" onclick="window.scrollTo(0,0);return false;" class="mdui-list-item mdui-ripple">
                    <i class="mdui-list-item-icon mdui-icon material-icons">home</i>
                    <div class="mdui-list-item-content">首页</div>
                </a>
            </div>
        </drawer>

        <main class="mdui-container" id="container">
            <center class="mdui-m-t-2 mdui-m-b-2">
                <div class="mdui-spinner mdui-spinner-colorful"></div>
                <br>
                <p class="mdui-text-color-theme-secondary">稍安勿躁，马上就好</p>
            </center>
        </main> 

        <script src="./js/main.js"></script>
    </body>
</html>
