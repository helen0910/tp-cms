<?php
header('P3P: CP=CAO PSA OUR');//由于P3P限制  ie专用
error_reporting(E_ALL);
define('__PATH__', getcwd().'/');
define('APP_NAME', 'App');
define('APP_PATH', './App/');
//路径
define('TEMPLATE_DIR', 'Template');//模板目录
define('REQUIRE_DIR', 'Require');
define('HTML_DIR', 'html');
define('TEMPLATE_PATH', realpath(__PATH__.APP_PATH.TEMPLATE_DIR).'/');
define('REQUIRE_PATH', __PATH__.REQUIRE_DIR.'/');
define('RUNTIME_PATH', __PATH__.'_Runtime/');
define('HTML_PATH', __PATH__.HTML_DIR.'/');
//调试模式
define('APP_DEBUG', true);
require './ThinkPHP/ThinkPHP.php';
?>
