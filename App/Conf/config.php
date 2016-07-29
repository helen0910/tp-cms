<?php

/**
 * 网站核心配置，可手动修改，但不建议
 */
return array(
    /*     * 分组模式* */
    'APP_GROUP_LIST' => 'Back', // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'APP_GROUP_MODE' => 1, // 分组模式 0 普通分组 1 独立分组
    'APP_GROUP_PATH' => 'Modules', //独立分组路径
    'LOAD_EXT_CONFIG' => 'core,site,mutual,attachment,email,ext', //自动加载配置
    'URL_CASE_INSENSITIVE' => true, //URL不区分大小写
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',
    /* 认证方式 */
    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'xmkd_admin_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'xmkd_admin_group_access', //用户组明细表
        'AUTH_RULE' => 'xmkd_admin_rule', //权限规则表
        'AUTH_USER' => 'xmkd_admin'//用户信息表
    ),
    'APP_AUTOLOAD_PATH' => 'ORG,ORG.Com,ORG.Util,ORG.Net,ORG.Crypt', //自动加载类库包
    //默认皮肤
    'DEFAULT_THEME' => 'Default',
    //管理员标记
    'ADMIN_SESSION' => 'admin_session', //后台用户标记
    'SUPER_ADMIN' => 'SUPER_ADMIN', //超级管理员标记
    'DEFAULT_GROUP' => 'Back', // 默认分组
    'DEFAULT_MODULE' => 'Entrance', // 默认模块名称
    'DEFAULT_ACTION' => 'login', // 默认操作名称
    'TMPL_PARSE_STRING' => array(
        '__APP_PUBLIC__' => __ROOT__ . '/' . APP_NAME . '/Tpl/Public', //app
        /*  Default  => 也请修改，它是site.php中的DEFAULT_SKIN配置 */
        //'__TEMPLATE_PUBLIC__'=>__ROOT__.'/'.APP_PATH.TEMPLATE_DIR.'/Default/Public',//这里 Default是网站默认skin
        '__TEMPLATE__' => __ROOT__ . '/' . APP_PATH . TEMPLATE_DIR, //前台模板目录
    ),
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PORT' => '3306', // 端口
);
?>