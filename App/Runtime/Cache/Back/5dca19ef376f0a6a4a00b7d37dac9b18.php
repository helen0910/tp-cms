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
        <table class="showTable">
            <tr>
                <th width="5%"><input type="checkbox" name="check_id_all" id="check_id_all" /></th>
                <th width="5%">ID</th>
                <th width="10%">角色名称</th>
                <th width="55%">角色备注</th>
                <th width="5%">状态</th>
                <th width="20%">操作</th>
            </tr>
            <?php if(is_array($adminGroup)): foreach($adminGroup as $key=>$values): ?><tr>
                    <td><input type='checkbox' name='id_all[]' value="<?php echo ($values['id']); ?>" /></td>
                    <td><?php echo ($values['id']); ?></td>
                    <td><?php echo ($values['title']); ?></td>
                    <td style="text-align:left;"><?php echo ($values['remark']); ?></td>
                    <td><?php echo ($values['status']==1 ? '正常' : '禁用'); ?></td>
                    <td>
                        <?php if($values['is_super'] == 1): ?><span class="disabled">系统权限</span>&nbsp;&nbsp;
                            <span class="disabled">编辑</span>&nbsp;&nbsp;
                            <?php else: ?> 
                            <?php if(in_array('set_rule', $ruleOperate) ): ?><a href="###" class="operate" onclick="openNewWindow('<?php echo U('set_rule',array('id'=>$values['id']));?>', '系统权限')">系统权限</a>&nbsp;&nbsp;<?php endif; ?>
                            <?php if(in_array('edit', $ruleOperate) ): ?><a href="<?php echo U('edit',array('id'=>$values['id']));?>" class="operate">编辑</a>&nbsp;&nbsp;<?php endif; endif; ?>
                    </td>
                </tr><?php endforeach; endif; ?>
        </table>
    </body>
</html>