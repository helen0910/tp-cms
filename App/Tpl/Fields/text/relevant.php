
<style type="text/css">
#relevantTitle {margin-bottom:5px;}
#relevantTitle p {line-height:180%;clear:both;overflow:hidden;_zoom:1}
</style>
<input type="hidden" name="info[<?php echo $this->form['field_name']?>]" value="<?php echo $value['value']?>" id="source" <?php echo $this->form['input_attr']?> />
<div id="relevantTitle">
<?php if ($value['relevant_title']) { foreach ( $value['relevant_title'] as $reValue) {?>
	<p><?php echo String::msubstr($reValue['title'], 0,13)?><a href="###" class="setRed FR" style="margin-right:5px;" id="<?php echo $reValue['id']?>">╳</a></p>
<?php } }?>
</div>
<div style="text-align:center;">
	<input type="button" value="添加相关" class="smallSub" onclick="openNewWindow('<?php echo U('Content/Contents/relevant',array('cid'=>$_GET['cid'],'file_name'=>rawurlencode("info[{$this->form['field_name']}]")))?>','相关文章',870,485)" />
	<input type="button" value="显示已有" class="smallSub" style="margin-left:10px;" />
</div>
<script type="text/javascript">
$(function(){
	$('#relevantTitle').on('click','p a',function(){
		var _id = $(this).attr('id');
		var _val = $('[name="info[<?php echo $this->form['field_name']?>]"]').val();
		if(_val) {
			_val = _val.replace(_id,'');
			_val = _val.replace(',,',',');
			$('[name="info[<?php echo $this->form['field_name']?>]"]').val(_val);
		}
		$(this).parent().remove();
	})
})
</script>