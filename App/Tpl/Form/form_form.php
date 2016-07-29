<form action="<?php echo U('form_post',array('mid'=>$_GET['mid'])) ?>" method="post" class="postForm" onsubmit="return checkData();">
	<input type="hidden" value="<?php echo $referer?>" name="referer"  />
	<input type="hidden" value="<?php echo $posttime;?>" name="posttime" />
	<input type="hidden" value="<?php echo $_GET['aid']?>" name="aid" />
	<input type="hidden" value="<?php echo $_GET['cid']?>" name="cid" />
	<input type="hidden" value="<?php echo $token?>" name="token" />
	<div class="content_form clearFloat">
		<?php $newFieldArray = array_merge($fieldArray['is_basic'],(array)$fieldArray['is_append']);?>
		<table class="">
		<?php foreach ($newFieldArray as $values) {?>
			<tr <?php if ($values['field_setting']['is_hide'] == 1) {echo 'style="display:none;"';}?>>
				<td class="left" style="width:85px;">
				<?php if ($values['field_setting']['is_required']==1) {echo '<font color="red">*</font>';}?>
				<span class="Validform_label"><?php echo $values['nick_name']?></span>
				</td>
				<td class="right" style="width:auto;">
					<?php echo $values['html'];?>
					<?php if ($values['form_type'] != 'editor') {?>
						<span class="setDesc  Validform_checktip"><?php echo $values['tips'];?></span>
					<?php }?>
				</td>
			</tr>
		<?php }?>
		<?php if ($start_code == 1) {?>
			<tr>
				<td class="left" style="width: 85px;">验证码</td>
				<td class="right"><input type="text" name="code" class="code" /><img src="__URL__/get_code?w=120&h=38&l=5&fs=16&mid=<?php echo $_GET['mid']?>" onclick="this.src=this.src+'&_='+Math.random()" alt="" /></td>
			</tr>
		<?php }?>
			<tr>
				<td class="left">&nbsp;&nbsp;</td>
				<td class="right">
					<input type="submit" name="send" class="sub" value="提交"  />
				</td>
			</tr>
		</table>
	</div>
</form>
