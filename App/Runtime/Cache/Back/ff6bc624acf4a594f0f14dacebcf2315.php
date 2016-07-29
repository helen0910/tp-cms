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
        <form action="__ACTION__" method="get" name="search_form">
            <table class="showTable">
                <tr><td colspan="8">
                        <div class="marginTop10">
                            用户名：
                            <input type="text" name="username" size="25" value="<?php echo ($_GET['search_content']); ?>" placeholder="模糊查询" />&nbsp;&nbsp;
                            状态：<select name="admin_status">
                                <option value="">全部</option>
                                <option value="1" <?php echo ($_GET['m_status']==1 ? 'selected="selected"' : ''); ?>>启用</option>
                                <option value="2" <?php echo ($_GET['m_status']==2 ? 'selected="selected"' : ''); ?>>禁用</option>
                            </select>&nbsp;&nbsp;
                            <input type="button" class="smallSub" value="搜索" name="search" onclick="$.CR.G.searchs();" />
                        </div>
                    </td></tr>
                <tr>
                    <th width="5%">ID</th>
                    <th width="8%">用户名</th>
                    <th width="10%">邮箱</th>
                    <th width="8%">手机号</th>
                    <th width="14%">注册时间</th>
                    <th width="5%">状态</th>
                    <th width="10%">用户类别</th>
                    <th width="15%">操作</th>
                </tr>
                <?php if(is_array($adminArray[0])): foreach($adminArray[0] as $key=>$values): ?><tr>
                        <td><?php echo ($values['id']); ?></td>
                        <td><?php echo ($values['username']); ?></td>
                        <td><?php echo ($values['email']); ?></td>
                        <td><?php echo ($values['phone']); ?></td>
                        <td><?php echo (date("Y-m-d H:i:s",$values['reg_time'])); ?></td>
                        <td><?php echo ($values['admin_status']==1 ? '启用' : '禁用'); ?></td>
                        <td>
                            <?php if(is_array($values['group'])): foreach($values['group'] as $key=>$groupVal): echo ($groupVal['title']); ?>&nbsp;<?php endforeach; endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo U('edit',array('id'=>$values['id']));?>" class="operate">编辑</a>&nbsp;&nbsp;
                            <a href="###" onclick="(_confirm('是否确定要初始化密码？', function () {
                                        location.href = '<?php echo U("resetpwd",array('id'=>$values['id']))?>'}))" class="operate">初始化密码</a>&nbsp;&nbsp;
                        </td>
                    </tr><?php endforeach; endif; ?>
                <tr>
                    <td colspan="8" class="right"><?php echo ($adminArray[1]); ?></td>
                </tr>
            </table>
        </form>
    </body>
</html>