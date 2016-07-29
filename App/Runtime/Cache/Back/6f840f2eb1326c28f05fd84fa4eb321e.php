<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>数据管理平台</title>
<link rel="stylesheet" href="__PUBLIC__/css/global.css" type="text/css" />
<link rel="stylesheet" href="__PUBLIC__/modules/back/css/login.css" type="text/css" />
</head>
<body>
<div class="main clearFloat" id="main">
	<form method="post" action="__ACTION__">
		<dl>
			<dt><span>&nbsp;</span>数据管理平台</dt>
			<dd><input type="text" class="username text" value="" name="username" /></dd>
			<dd><input type="password" class="password text" value="" name="password"  /></dd>
			<dd><input type="text" class="code" name="code" /><img src="<?php echo U('Back/Entrance/get_code','w=115&h=34&l=5');?>" style="margin-left:10px;cursor:pointer;" onclick="javascript:this.src=this.src+'&id='+Math.random();" /></dd>
			<dd><input type="submit" value="登录" class="loginSub" /></dd>
		</dl>
	</form>
</div>
<script type="text/javascript">window.onload = function(){var _main = document.getElementById('main');_main.style.marginTop = (document.documentElement.clientHeight - _main.offsetHeight)/2+'px';}</script>
</body>
</html>