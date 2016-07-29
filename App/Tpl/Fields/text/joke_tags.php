
<input class="text tags" type="text" id="tags" value="<?php echo $value;?>" <?php echo $this->form['input_attr']?> name="info[<?php echo $this->form['field_name']?>]" size="40">
<?php 
$Tags = M('Tags');
$tagsData = $Tags->order('tag_num DESC')->limit(7)->getField('tag_name',true);
if ($tagsData) {
?>
<style type="text/css">
.hotTags {margin-top:10px;clear:both;}
.tag_detail {background:#F0A401;color: #FFFFFF;cursor: pointer;display: inline-block;height: 22px;line-height: 22px;margin: 0 6px 0 0;padding: 0 12px;text-align: center;}
</style>
<div class="hotTags">
	<?php 
			foreach ($tagsData as $tagValue) {
	?>
	<span class="tag_detail" onclick="select_tag(this)" value="<?php echo $tagValue?>"><?php echo $tagValue?></span>
	<?php }?>
</div>
<script type="text/javascript">
<!--
function select_tag(obj) {
	var tagsValue = $('#tags').val();
	var _tag_detail = $(obj).attr('value');
	if(tagsValue.indexOf(_tag_detail) <= -1) {
		if(tagsValue) {
			$('#tags').val(tagsValue+' '+_tag_detail);
		}else {
			$('#tags').val(_tag_detail);
		}
	}
} 
//-->
</script>
<?php }?>
