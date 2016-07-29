<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
        <title>数据管理平台</title>
        <link rel="stylesheet" href="__PUBLIC__/modules/back/css/index.css" type="text/css" />
        <script type="text/javascript" src="__PUBLIC__/js/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="__PUBLIC__/modules/back/js/index.js"></script>
    </head>
    <body>
        <div class="header">
            <div class="logo FL"><a href="__APP__/Back/Index/index"><img src="__PUBLIC__/modules/back/images/logo.png" alt="" /></a></div>
            <div class="mainNavi">
                <div class="naviTop clearFloat">
                    <p class="FL">欢迎您：<font color="#99FF00"><?php echo ($session['username']); ?></font>&nbsp;&nbsp;所属组：
                        [
                        <?php if(is_array($session['admin_group'])): $i = 0; $__LIST__ = $session['admin_group'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$values): $mod = ($i % 2 );++$i; if($i > 1): ?>,<?php endif; ?>
                            <?php echo ($values['title']); endforeach; endif; else: echo "" ;endif; ?>
                        ]
                        &nbsp;&nbsp;<a href="__APP__/Back/Entrance/logout" class="logout">[退出]</a></p>
                    <p class="FR" style="padding-right:10px;">
                        <a href="###" src="<?php echo U('Back/Public/update_cache');?>" id="u_Cache">更新缓存</a>
                    </p>
                </div>
                <p class="naviBottom">
                    <?php if(is_array($ruleData)): $i = 0; $__LIST__ = $ruleData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ruleValue): $mod = ($i % 2 );++$i;?><a href="###" <?php echo ($i==1 ? 'class="current"' : ''); ?>>
                            <?php echo ($ruleValue['title']); ?>
                        </a><?php endforeach; endif; else: echo "" ;endif; ?>
                </p>
            </div>
        </div>
        <div class="main clearFloat">
            <div class="mainLeft FL">
                <?php if(is_array($ruleData)): $i = 0; $__LIST__ = $ruleData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ruleValue): $mod = ($i % 2 );++$i;?><dl <?php echo ($i==1 ? '' : 'style="display:none;"'); ?>>
                        <?php if(is_array($ruleValue['child'])): $i = 0; $__LIST__ = $ruleValue['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$childValues): $mod = ($i % 2 );++$i;?><dt><?php echo ($childValues['title']); ?></dt>
                            <?php if(is_array($childValues['child'])): $i = 0; $__LIST__ = $childValues['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$childChildValues): $mod = ($i % 2 );++$i; if(($childChildValues['node_type'] == 1)): ?><dd src="__APP__<?php echo ($childChildValues['name']); ?>"><?php echo ($childChildValues['title']); ?></a></dd><?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                    </dl><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="mainRight">
                <a href="###" title="全屏" class="fullScreen" onclick="fullScreen()">&nbsp;&nbsp;</a>
                <a href="###" title="取消全屏" class="cancelFullScreen" onclick="cancelFullScreen();" style="display: none">&nbsp;&nbsp;</a>
                <div class="iframeContent">
                    <iframe src="__APP__/Back/Index/show" class="mainIframe" name="mainIframe" frameborder="0" width="100%"></iframe>
                </div>
            </div>
        </div>
        <div class="footer">
            <?php echo (date('Y-m-d g:i a',time())); ?>
        </div>
    </body>
</html>