<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        
        
        <style type="text/css">
            div.topTip {border:1px solid #DFEDF7;margin-bottom:0px;padding-left:15px;}
            .info {border:1px solid #DFEDF7;border-top:0px;text-indent:15px;padding:7px 0px;}
            .info .sysinfo {width:345px;}
            .info li {height:26px;line-height:26px;}
            .phpinfo {overflow-x:hidden;overflow-y:auto;}
            .phpinfo * {font-size:13px;}
            .formTable {border:0px;}
            .formTable tr td {border:0px;border-bottom:1px solid 1px solid #CAE0F4;font-size:13px;}
            .formTable tr td.left {width:auto;text-align:left;}
            .fk {padding:5px 0px;}
            .fk li {padding:7px 0px;}
        </style>
    </head>
    <body>
        <div class="topTip"><b>系统信息</b></div>
        <div class="info clearFloat">
            <div class="sysinfo FL">
               系统介绍
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                var _width = $('.info').width();
                var _sysinfoWidth = $('.sysinfo').width();
                var _sysinfoHeight = $('.sysinfo').height();
                $('.phpinfo').width(_width - _sysinfoWidth).height(_sysinfoHeight);
            })
        </script>
    </body>
</html>