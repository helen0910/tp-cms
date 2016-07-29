<?php
class FormElements {
	
	static public function radio($options,$field_name,$value = '',$input_attr = '') {
		$html = '';
		foreach ($options as $key=>$values) {
			$checked = in_array($key, $value) ? 'checked="checked"' : ''; 
			$html .='<label><input type="radio" name="'.$field_name.'" value="'.$key.'" '.$checked.'  '.$input_attr.' />&nbsp;'.$values.'</label>&nbsp;&nbsp;'; 
		}
		return $html;
	}
	
	
	static public function checkbox($options,$field_name,$value,$input_attr = '') {
		$html = '';
		foreach ($options as $key=>$values) {
			$checked = in_array($key, $value) ? 'checked="checked"' : ''; 
			$html .= '<label><input type="checkbox" name="'.$field_name.'" value="'.$key.'" '.$checked.' '.$input_attr.' /> '.$values.'</label>&nbsp;&nbsp;';
		}
		return $html;
	}
	
	static public function select($options,$field_name,$value='',$input_attr='',$ismultiple = false) {
		if ($ismultiple) {
			$ismultiple = 'multiple="multiple"';
		}
		$html = "<select name=\"$field_name\" $ismultiple $input_attr $class>";
		foreach ($options as $key=>$values) {
			if (is_array($value)) {
				$selected = in_array($key, $value) ? 'selected="selected"' : '';
			}else {
				$selected = $key==$value ? 'selected="selected"' : '';
			}
			$html .= '<option value="'.$key.'" '.$selected.'>'.$values.'</option>';
		}
		return $html .= '</select>';
	}
	
	public static function input($fieldName,$value = '',$input_attr = '') {
		return '<input type="text" value="'.$value.'" '.$input_attr.' name="'.$fieldName.'" />';
	}
	public static function password($fieldName,$value = '',$input_attr = '') {
		return '<input type="password" value="'.$value.'" '.$input_attr.' name="'.$fieldName.'" />';
	}
	
	public static function textarea($fieldName,$value = '',$input_attr = '') {
		return '<textarea '.$input_attr.' name="'.$fieldName.'">'.$value.'</textarea>';
	}
	
	/**
	 * 模板选择
	 * @param $style  风格
	 * @param $module 模块
	 * @param $id 默认选中值
	 * @param $str 属性
	 * @param $pre 模板前缀
	 */
	public static function selectTemplate($dirpath = 'Content/show') {
		//先读取上传的模板
		$dirpath = trim($dirpath,'/');
// 	模板目录
		$defaultSkin = C('DEFAULT_SKIN') ?  C('DEFAULT_SKIN') : 'Default';
		$filepath = TEMPLATE_PATH . $defaultSkin . "/$dirpath/";
// 	取出原文件
		$tp_show =  str_replace($filepath, '', glob($filepath ."*.php"));
		$new_tpl = array();
		foreach ($tp_show as $k => $v) {
			$new_tpl[$v] = $v;
		}
		return $new_tpl;
	}
	
	public static function selectUrl($model,$typeFile,$isHtml,$selectName,$value = '') {
		$UrlRule = M('UrlRule');
		$urlArr = $UrlRule->where("model='$model' AND url_name='$typeFile' AND is_html=$isHtml AND u_status=1")->select();
		$newArr = array();
		foreach ($urlArr as $values) {
			$newArr[$values['id']] = $values['example'];
		}
		return self::select($newArr, $selectName,$value);
	}
}
?>