<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        
<link rel="stylesheet" href="__PUBLIC__/css/global.css" type="text/css" />
<script type="text/javascript">var WEB_URL = '<?php echo C('WEB_URL');?>',_PUBLIC_ = '__PUBLIC__',_ROOT_ = '__ROOT__',_APP_ = '__APP__',_VAR_GROUP_='<?php echo C('VAR_GROUP') ;?>',_VAR_MODULE_='<?php echo C('VAR_MODULE') ;?>',_VAR_ACTION_='<?php echo C('VAR_ACTION') ;?>',JSONP_CALLBACK='<?php echo C('VAR_JSONP_HANDLER')?>',FACE_NUM = 70,_GROUP_NAME_='<?php echo GROUP_NAME;?>',_MODULE_NAME_='<?php echo MODULE_NAME;?>',_ACTION_NAME_='<?php echo ACTION_NAME;?>',_GROUP_ = _APP_+'?'+_VAR_GROUP_+'='+_GROUP_NAME_,_URL_ = _GROUP_+'&'+_VAR_MODULE_+'='+_MODULE_NAME_,_ACTION_ = _URL_+'&'+_VAR_ACTION_+'='+_ACTION_NAME_;//document.domain='<?php echo substr(C('WEB_URL'), 7);?>';</script>
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.10.2.min.js"></script>
<?php if (C('DEFAULT_THEME') == 'Desktop') {?>
<script type="text/javascript" src="__PUBLIC__/js/jquery-migrate-1.2.1.min.js"></script>
<?php } ?>
<script type="text/javascript" src="__PUBLIC__/js/global.js"></script>

        
<!-- 后台公共加载文件 -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<title>熊猫口袋数据管理平台</title>
<!-- 后台   公共JS或CSS加载 -->
<link rel="stylesheet" href="__PUBLIC__/js/validform/css/style.css" type="text/css" />
<link rel="stylesheet" href="__PUBLIC__/modules/css/basic.css" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/js/validform/js/Validform_v5.3.2_min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/My97Date/WdatePicker.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/artDialog/artDialog.js?skin=default"></script>
<script type="text/javascript" src="__PUBLIC__/js/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="__PUBLIC__/modules/js/global.js"></script>
<!-- 区别前后台操作公用Attachment -->
  <?php if(SESSION_TYPE== 1): ?><script type="text/javascript">//setInterval(function(){$.get($.G.U('Back/Public/check_admin_login'),{id:Math.random()},function(data){if(data.status == 'error') {_alert('您有帐号已在别处登录！！！',function(){window.location.href = $.G.U('Back/Entrance/logout');});} },'json');},15000);</script><?php endif; ?>
    </head>
    <body>
        
<div class="navi clearFloat">
    <div class="naviDesc"><?php echo ($parentRuleName); ?></div>
    <div class="naviAction">
        <?php if(is_array($ruleLink)): foreach($ruleLink as $key=>$values): $rule = explode('/',$values['name']); ?>
            <a class="<?php echo ACTION_NAME==$rule[3] ? 'current' : '';?>" href="<?php echo U(trim($values['name'],'/'));?>">
                <u><?php echo ($values['title']); ?></u>
            </a><?php endforeach; endif; ?>
    </div>
</div>
        <div class="operateTitle">添加管理员</div>
        <form action="__ACTION__" method="post" id="formData">
            <table class="formTable">
                <tr>
                    <td class="left"><span class="setRed">*</span>&nbsp;<span class="Validform_label">用户名：</span></td>
                    <td class="right">
                        <input type="text" class="text normal" size="35" name="username" datatype="/^[a-z][\w]{4,11}$/i" ajaxurl="<?php echo U('Back/Public/check_back_username');?>" errormsg="用户名格式不正确！" />
                        <span class="setDesc Validform_checktip">字母开头，5-12位，只能包含[a-z\w],添加成功后不可更改</span>
                    </td>
                </tr>
                <tr>
                    <td class="left"><span class="setRed">*</span>&nbsp;<span class="Validform_label">密码：</span></td>
                    <td class="right">
                        <input type="password" class="text normal" size="35" name="password" datatype="*6-16" errormsg="密码格式不正确！" />
                        <span class="setDesc Validform_checktip">6-16个字符之间</span>
                    </td>
                </tr>
                <tr>
                    <td class="left"><span class="setRed">*</span>&nbsp;<span class="Validform_label">确认密码：</span></td>
                    <td class="right">
                        <input type="password" class="text normal" size="35" name="password2" datatype="*" recheck="password" errormsg="您两次输入的密码不一致！"  />
                        <span class="setDesc Validform_checktip">6-16个字符之间</span>
                    </td>
                </tr>
                <tr>
                    <td class="left"><span class="setRed">*</span>&nbsp;<span class="Validform_label">手机号：</span></td>
                    <td class="right">
                        <input type="text" class="text normal" size="35" name="phone" datatype="m"  errormsg="手机号格式不正确！" />
                        <span class="setDesc Validform_checktip">请输入正确的手机号</span>
                    </td>
                </tr>
                <tr>
                    <td class="left"><span class="setRed">*</span>&nbsp;<span class="Validform_label">邮箱：</span></td>
                    <td class="right">
                        <input type="text" class="text normal" size="35" name="email" datatype="e" errormsg="邮箱格式不正确！" />
                        <span class="setDesc Validform_checktip">请输入正确的邮箱</span>
                    </td>
                </tr>
                <tr>
                    <td class="left">
                        <span class="setRed">*</span>&nbsp;<span class="Validform_label">所属权限组：</span>
                    </td>
                    <td class="right">
                        <?php if(is_array($groupData)): foreach($groupData as $key=>$values): ?><label><input type="checkbox" name="group[]" datatype="need_1" value="<?php echo ($values['id']); ?>" errormsg="请选择所属权限！" />&nbsp;<?php echo ($values['title']); ?></label><?php endforeach; endif; ?>
                        <span class="setDesc Validform_checktip"></span>
                    </td>
                </tr>
                <tr>
                    <td class="left">状态：</td>
                    <td class="right">
                        <label>启用&nbsp;<input type="radio" value="1" name="admin_status" checked="checked" /></label>
                        <label>禁用&nbsp;<input type="radio" value="2" name="admin_status" /></label>
                    </td>
                </tr>
                <tr>
                    <td class="left">&nbsp;&nbsp;</td>
                    <td class="right">
                        <input type="submit" name="send" class="sub" value="提交" />
                        <input type="reset" class="sub" />
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>