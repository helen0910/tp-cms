
<?php 
$path = TEMPLATE_PATH.C('DEFAULT_SKIN').'/Content/show/';
$tpl = str_replace($path, '', glob($path.'*.php'));
?>
<input type="hidden" id="tpl_value" name="info[<?php echo $this->form['field_name']?>]" value="<?php echo $value;?>"  />
<select id="select_tpl" onchange="$('#tpl_value').val(this.value)" <?php echo $this->form['input_attr']?>>
	<option value="">继承导航模板</option>
	<?php foreach ($tpl as $tpl_value) {?>
	<option value="<?php echo $tpl_value?>" <?php echo $tpl_value==$value ? 'selected="selected"' : '';?>><?php echo $tpl_value?></option>
	<?php } ?>
</select>

