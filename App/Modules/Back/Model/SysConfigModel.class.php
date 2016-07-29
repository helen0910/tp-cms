<?php
/**
 * 系统配置模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class SysConfigModel extends  GlobalModel {
	
	/**
	 * 添加数据
	 * @param array $data
	 * @return Ambigous <mixed, boolean, string, unknown, false, number>
	 */
	public function addData($data) {
		$data['save_time'] = NOW_TIME;
		$data['var_name'] = strtoupper($data['var_name']);
		if ($data['var_type'] == 'boolean') $data['var_value'] = strtoupper( $data['var_value']);
		$status = $this->data($data)->add();
		if ($status) {
			if ($data['var_type'] == 'array') $data['var_value'] = explode("\r\n", $data['var_value']);
			//更新文件
			$configArray = require(CONF_PATH.$data['var_group'].'.php');
			$configArray[$data['var_name']] = $data['var_value'];
			$this->putFile($data['var_group'],$configArray);
		}
		return $status;
	}
	
	/**
	 * 数据验证
	 * @return boolean|Ambigous <boolean, unknown, multitype:unknown string >
	 */
	public function checkData() {
		$postData = Tool::filterData($_POST);
		$postData['var_value'] = $_POST['var_value'];
		// 		如果是布尔值
		if ($postData['var_type'] == 'boolean' && (strtolower($postData['var_value']) != 'y' && strtolower($postData['var_value']) != 'n')) {
			$postData['vail_info'] = '布尔值的值必须为Y或N！';
			$postData['vail_status'] = false;
			return $postData;
		}
		if ($postData['var_type'] == 'numeric' && !is_numeric($postData['var_value'])) {
			$postData['vail_info'] = '数值型的数值必须为数字！';
			$postData['vail_status'] = false;
			return $postData;
		}
		$postData = ValiData::_vail()->_check(array(
			'var_name'=>array('r/^[\_a-z][\w]{1,29}$/i','参数名称格式不正确！'),
// 			'var_value'=>array('s1,','参数值格式不正确！'),
			'var_desc'=>array('a|s1-255','参数说明格式不正确！'),
		),$postData);
		return $postData;
	}
	
	/**
	 * 编辑数据
	 * @return boolean|number
	 */
	public function editData() {
		$postData = $_POST;
		$newArr = array();
		foreach ($postData['name'] as $key=>$value) {
			$keyArr = explode('#', $key);
			$status = $this->where("id='{$keyArr[0]}'")->setField('var_value',$value);
			if ($status === false) {
				$this->writeLog('修改配置['.$postData['var_group'].']失败！', 'SYSTEM_ERROR');
				return false;
				break;
			}
// 			判断是否是数组类型
			$newArr[$keyArr[1]] = $keyArr[2] == 'array' ? explode("\r\n", $value) : $value;
		}
		//修改文件
		return $this->putFile($postData['var_group'], $newArr);
	}
	
	/**
	 * 数据删除
	 * @param numeric|string $id
	 * @return number|Ambigous <mixed, boolean, false, number>
	 */
	public function deleteData($id) {
// 		取出删除数组的类型
		$delData = $this->where("id='{$id}'")->find();
// 		取出同一组并且非删除数据的所有数据
		$selectData = $this->where("var_group='{$delData['var_group']}' AND id<>{$id}")->select();
		$status = $this->where("id='{$id}'")->delete();
		if ($status) {
			$newArr = array();
			foreach ($selectData as $selectVal) {
				$newArr[$selectVal['var_name']] = ($selectVal['var_type'] == 'array') ? explode("\r\n", $selectVal['var_value']) : $selectVal['var_value'];
			}
			return $this->putFile($delData['var_group'],$newArr);
		}
		return $status;
	}
	
	/**
	 * 写入配置文件
	 * @param string $group
	 * @param array $data
	 * @return number
	 */
	private function putFile($group,$data) {
		$path = CONF_PATH."$group.php";
		$string = "<?php\r\nreturn array(\r\n";
		foreach ($data as $key=>$value) {
// 			if ($value == '') continue;
			if ($value == 'Y') {
				$string .= "'{$key}'=>true,\r\n";
			}elseif ($value == 'N') {
				$string .= "'{$key}'=>false,\r\n";
			}elseif (is_array($value)){
				$sString = '';
				foreach ($value as $sVal) {
// 					if ($sVal == '') continue;
					$sValArr = explode('=>', $sVal);
					$sString .= count($sValArr) == 1 ? "'{$sValArr[0]}'," : "'{$sValArr[0]}'=>'{$sValArr[1]}',";
				}
				$string .= "'{$key}'=>array(".str_replace("\n",'', $sString)."),\r\n";
			}elseif (is_numeric($value)) {
				$string .= "'{$key}'=>{$value},\r\n";
			}else{
				$string .= "'{$key}'=>'{$value}',\r\n";
			}
		}
		$string .= ");\r\n?>";
		return  file_put_contents($path, $string);
	}
	
	//检查惟一性
	public function checkUnique($data){
		return $this->where("var_name='{$data['var_name']}'")->find();
	}
	
}
?>