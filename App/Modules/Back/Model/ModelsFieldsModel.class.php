<?php
/**
 * 模型字段处理模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class ModelsFieldsModel extends GlobalModel {
	
	/**
	 * 数据验证
	 * @param array|string $setting
	 * @return Ambigous <boolean, unknown, multitype:unknown string >
	 */	
	public function checkData($setting = array()) {
		$postData = Tool::filterData($_POST,false);
		$globalArr = array(
				'nick_name'=>array('s1-30','字段别名格式不正确！！！'),
				'tips'=>array('a|s1-255','字段提示格式不正确！！'),
				'field_len'=>array('a|r/^[1-9][\d]*$/','字符长度取值范围，最大值格式不正确！！！'),
				'input_attr'=>array('a|s1-255','表单附加属性格式不正确！！！'),
				'pattern'=>array('a|s1-255','数据校验正则格式不正确！！！'),
// 				'errortips'=>array('a|s1-255','验证未通过提示信息格式不正确！！！'),
				'sort'=>array('r/^[1-9][\d]{0,7}$/','字段排序格式不正确！！！'),
		);
		if ($setting == 'add') {
			$newArr = array(
					'form_type'=>array('s1,','请选择字段类型！！！'),
					'field_name'=>array('r/^[a-z][\w]{0,19}$/i','字段名格式不正确！！！'),
			);
			$globalArr = array_merge($newArr,$globalArr);
		}
		return ValiData::_vail()->_check($globalArr, $postData);
	}
	
	/**
	 * 保存字段
	 * @param string $fieldType 字段类型
	 * @param string $tableName 数据表名
	 * @param array $postData 其它使用数组数据
	 * @param string $type
	 * @return Ambigous <false, number, boolean>
	 */
	public function saveField($fieldType,$tableName,$postData,$type = 'ADD') {
		$tableName = $this->tablePrefix.$tableName;
		$setting = $postData['setting'];
		$defaultValue = isset($setting['default_value'])&& !empty($setting['default_value']) ? "DEFAULT '{$setting['default_value']}'" : '';//默认值
		$unsignedType = ($setting['is_unsigned'] == 1) ? 'UNSIGNED' : '';//数值类型是否是正整数
		switch ($fieldType) {
			case 'varchar':
			case 'char':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` ".strtoupper($fieldType)."( {$postData['field_len']} ) NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
			case 'text':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` TEXT NOT NULL COMMENT '{$postData['nick_name']}'";
				break;
			case 'title':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` CHAR( {$postData['field_len']} ) NOT NULL COMMENT '{$postData['nick_name']}',";
				$sql .= "$type `style` CHAR(12) NOT NULL COMMENT '{$setting['nick_name']}';";
				break;
			case 'tinyint':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` TINYINT( {$postData['field_len']} ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
			case 'smallint':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` SMALLINT( {$postData['field_len']} ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
			case 'mediumint':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` MEDIUMINT( {$postData['field_len']} ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
			case 'int':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` INT( {$postData['field_len']} ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
			case 'bigint':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` BIGINT( {$postData['field_len']} ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}'";
				break;
// 			case 'date':
// 				$sql = "ALTER TABLE `{$fieldArr['table_name']}` $type `{$fieldArr['field_name']}` DATE NOT NULL COMMENT '{$fieldArr['nick_name']}'";
// 				break;
// 			case 'datetime_a':
// 			case 'datetime':
// 				$sql = "ALTER TABLE `{$fieldArr['table_name']}` ADD `{$fieldArr['field_name']}` DATETIME NOT NULL COMMENT '{$fieldArr['nick_name']}'";
// 				break;
// 			case 'timestamp':
// 				$sql = "ALTER TABLE `{$fieldArr['table_name']}` ADD `{$fieldArr['field_name']}` TIMESTAMP NOT NULL COMMENT '{$fieldArr['nick_name']}'";
// 				break;
// 			case 'int':
// 				$sql = "ALTER TABLE `{$fieldArr['table_name']}` $type `{$fieldArr['field_name']}` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '{$fieldArr['nick_name']}'";
// 				break;
			case 'associate':
				// 				取得关联表的字段信息
				switch ($setting['insert_type']) {
					case 'id':
						$tableInfo = parent::getTableField(str_replace($this->tablePrefix, '', $tableName),$setting['set_id']);
						$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` ".strtoupper($tableInfo['Type'])." NOT NULL COMMENT '{$postData['nick_name']}'";
						break;
					case 'title':
						$tableInfo = parent::getTableField(str_replace($this->tablePrefix, '', $tableName),$setting['set_id']);
						$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` ".strtoupper($tableInfo['Type'])." NOT NULL COMMENT '{$postData['nick_name']}'";
						break;
					case 'title_id':
						// 						title+id未完成
						break;
				}
				break;
			case 'page':
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` VARCHAR( 10 ) $unsignedType NOT NULL $defaultValue COMMENT '{$postData['nick_name']}';";
				break;
			case 'set':
			case 'enum':
				$fieldType = strtoupper($fieldType);
				$setting['set_value'] = "'".implode("','", explode(',',$setting['set_value']))."'";
				$sql = "ALTER TABLE `$tableName` $type `{$postData['field_name']}` $fieldType( {$setting['set_value']} ) NOT NULL COMMENT '{$postData['nick_name']}'";
				break;
		}
		return $this->execute($sql);
	}
	

	/**
	 * 添加数据
	 * @param array $postData
	 * @param array|string $setting
	 * @return Ambigous <mixed, boolean, string, unknown, false, number>
	 */
	public function addData($postData,$setting = array()) {
		$postData['field_setting'] = empty($postData['setting']) ? '' : json_encode($postData['setting']);
		return $this->data($postData)->add();
	}

	/**
	 * 编辑数据
	 * @param array $postData
	 * @param array|string $setting
	 * @return Ambigous <boolean, false, number>
	 */
	public function editData($postData,$setting = array()) {
		$postData['field_setting'] = empty($postData['setting']) ? '' : json_encode($postData['setting']);
		return $this->data($postData)->save();
	}
	
}
?>