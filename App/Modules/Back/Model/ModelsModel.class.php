<?php
/**
 * 数据模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class ModelsModel extends GlobalModel {

	/**
	 * 数据验证
	 * @param array|string $setting
	 * @return Ambigous <boolean, unknown, multitype:unknown string >
	 */
	public function checkData($setting = array()) {
		$postData = Tool::filterData($_POST);
		return ValiData::_vail()->_check(array(
			'model_name'=>array('s1-30','模型名称格式不正确！！！'),
			'table_name'=>array('r/^[a-z][\w]{0,29}$/i','模型表名格式不正确！！！'),
			'remark'=>array('a|s1-255','模型备注格式不正确！！！'),
		), $postData);
	}

	/**
	 * 添加数据
	 * @param array $postData
	 * @param array|string $setting
	 * @return Ambigous <mixed, boolean, string, unknown, false, number>
	 */
	public function addData($postData,$setting = array()) {
		$postData['save_time'] = NOW_TIME;
		$postData['setting'] = json_encode($postData['setting']);
		return $this->data($postData)->add();
	}
	
	/**
	 * 编辑数据
	 * @param array $postData
	 * @param array|string $setting
	 * @return Ambigous <boolean, false, number>
	 */
	public function editData($postData,$setting = array()) {
		$postData['setting'] = json_encode($postData['setting']);
		return $this->where("id={$postData['id']}")->save($postData);
	}

	
	/**
	 * 添加模型表
	 * @param Array $postData
	 * @param numeric $modelId
	 * @return multitype:numeric||boolean string array
	 */
	public function addModelTable($postData,$modelId = '') {
		if ($postData['model_type'] == 'content') {
			// 		独立模型
			if ($postData['setting']['is_alone'] == 1) {
				// 		添加主表
				$content = file_get_contents(REQUIRE_PATH.'Sql/content_alone_table.sql');
				if (!$content) {
					$this->writeLog("读取内容独立模型表失败，路径：".REQUIRE_PATH.'Sql/content_alone_table.sql', 'SYSTEM_ERROR');
					return false;
				}
				$status = $this->handleSql($content,$postData['table_name'],$modelId);
				if ($status === false) {
					$this->writeLog("添加内容独立数据主表失败", 'SYSTEM_ERROR');
					return false;
				}
			}else {//附加模型双表
				// 		添加主表
				$content = file_get_contents(REQUIRE_PATH.'Sql/content_main_table.sql');
				if (!$content) {
					$this->writeLog("读取内容双表主模型表失败，路径：".REQUIRE_PATH.'Sql/content_main_table.sql', 'SYSTEM_ERROR');
					return false;
				}
				$status = $this->handleSql($content,$postData['table_name'],$modelId);
				if ($status === false) {
					$this->writeLog("添加内容双表数据主表失败", 'SYSTEM_ERROR');
					return false;
				}
				// 		添加附加表
				$content = file_get_contents(REQUIRE_PATH.'Sql/content_main_table_data.sql');
				if (!$content) {
					$this->writeLog("读取系统附加数据表失败！", 'SYSTEM_ERROR');
					return false;
				}
				$status = $this->handleSql($content,$postData['table_name'],$modelId);
				if ($status === false) {
					$this->writeLog("添加数据附加表失败！", 'SYSTEM_ERROR');
					return false;
				}
			}
		} elseif ($postData['model_type'] == 'member') {
			/* 会员主表添加 */
			if ($postData['setting']['is_main'] == 1) {
				$content = file_get_contents(REQUIRE_PATH.'Sql/member_table.sql');
				if (!$content) {
					$this->writeLog("读取会员主表失败！", 'SYSTEM_ERROR');
					return false;
				}
				$status = $this->handleSql($content,$postData['table_name'],$modelId);
				if ($status === false) {
					$this->writeLog("添加会员主表失败！", 'SYSTEM_ERROR');
					return false;
				}
			} else {
				/* 添加会员附加表 */
				$content = file_get_contents(REQUIRE_PATH.'Sql/member_append_table.sql');
				if (!$content) {
					$this->writeLog("读取会员附加表失败！", 'SYSTEM_ERROR');
					return false;
				}
				$status = $this->handleSql($content,$postData['table_name'],$modelId);
				if ($status === false) {
					$this->writeLog("添加会员附加表失败！", 'SYSTEM_ERROR');
					return false;
				}
			}
		}elseif ($postData['model_type'] == 'form') {
			$content = file_get_contents(REQUIRE_PATH.'Sql/form_table.sql');
			if (!$content) {
				$this->writeLog("表单数据表失败！", 'SYSTEM_ERROR');
				return false;
			}
			$status = $this->handleSql($content,$postData['table_name'],$modelId);
			if ($status === false) {
				$this->writeLog("添加表单数据表失败！", 'SYSTEM_ERROR');
				return false;
			}
		}
		return true;
	}

	/**
	 * 处理执行SQL
	 * @param string $content
	 * @param string $tableName
	 * @param numeric $mid
	 * @return Ambigous <false, number, boolean>
	 */
	private function handleSql($content,$tableName = '',$mid) {
		$sql = str_replace(array('$prefix','$table','$mid','$charset',"\r\n"), array($this->tablePrefix,$tableName,$mid,C('DB_CHARSET'),''), $content);
		return $this->execute($sql);
	}
	
	/**
	 * 添加数据模型字段
	 * @param numeric $mid
	 * @param array|string $setting
	 * @return boolean
	 */
	public function addModelField($mid,$setting) {
// 		内容非独立模型字段内容
		if ($setting['model_type'] == 'content') {
			$sqlFile = $setting['setting']['is_alone']==1 ? 'alone_table_field.sql' : 'main_table_field.sql';
			$content = file_get_contents(REQUIRE_PATH.'Sql/'.$sqlFile);
			$status = $this->handleSql($content,'',$mid);
		}elseif ($setting['model_type'] == 'member') {
			/* 主表 */
			if ($setting['setting']['is_main']==1) {
				$status = true;
			} else {//附加表
				$content = file_get_contents(REQUIRE_PATH.'Sql/member_append_field.sql');
				$status = $this->handleSql($content,'',$mid);
			}
		}else {
			$status = true;
		}
		return $status===false ? false :true;
	}
	
}
?>