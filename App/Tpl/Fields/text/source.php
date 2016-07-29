
<input type="text" size="30" name="info[<?php echo $this->form['field_name']?>]" id="source" value="<?php echo $value;?>" <?php echo $this->form['input_attr']?> />
<div style="position:relative;display:inline-block;">
<input type="button" onclick="showSource()" class="smallSub" value="选择" style="margin-left:8px;height: 23px;line-height:23px;" />
<div id="source_div" show_status="show" style="display:none;position: absolute;bottom:25px;background:#F0F8FD;left:8px;width:200px;z-index:200;padding:10px;border:1px solid #95AADB">
	<?php 
		$Soucrce = M('Source');
		$source = $Soucrce->getField('source',true);
		unset($Soucrce);
		foreach ($source as $value) {
	?>
	<a href="###" style="padding:3px 7px; " onclick="$('#source').val($(this).text());$('#source_div').attr('show_status','show');$('#source_div').hide();"><?php echo $value?></a>
	<?php }?>
</div>
</div>
<script type="text/javascript">
function showSource() {
	if($('#source_div').attr('show_status') == 'show') {
		$('#source_div').attr('show_status','hide');
		$('#source_div').show();
	} else {
		$('#source_div').attr('show_status','show');
		$('#source_div').hide();
	}
}
</script>