<?php
/**
 * 系统基础模型
 * 
 *
 */
class GlobalModel extends Model {
	
	/**
	 * 得到数据表信息
	 * @param string $tableName
	 */
	public function getTableInfo($tableName = '') {
		$tableName = $tableName ? $this->tablePrefix.$tableName : $this->trueTableName;
		$tableInfo = $this->query("SHOW TABLE STATUS LIKE '$tableName'");
		return $tableInfo ? $tableInfo[0] : false;
	}
	/**
	 * 删除表
	 */
	public function dropTable($tablename) {
		return $this->query("DROP TABLE {$this->tablePrefix}$tablename");
	}
	
	/**
	 * 读取全部表名
	 */
	public function listTables() {
		$tables = array();
		$data = $this->query("SHOW TABLES");
		foreach ($data as $k => $v) {
			$tables[] = $v['Tables_in_' . C("DB_NAME")];
		}
		return $tables;
	}
	
	/**
	 * 得到表字段
	 * @param unknown $tableName
	 * @param string $field
	 * @return unknown|boolean
	 */
	public function getTableField($tableName = '',$field = '') {
		$tableName = $tableName ? $this->tablePrefix.$tableName : $this->trueTableName;
		$sql = "SHOW COLUMNS FROM `$tableName`";
		$fields = parent::query($sql);
		if ($fields) {
			$fieldArr = array();
			if (empty($field)) {
				foreach ($fields as $values) {
					$fieldArr[$values['Field']] = $values['Field'];
				}
			}else {
				foreach ($fields as $values) {
					if ($values['Field'] == $field) {
						$fieldArr = $values;
						break;
					}
				}
			}
			return $fieldArr;
		}else {
			return false;
		}
	}
	
	/**
	 * 检查表是否存在
	 * $table 不带表前缀
	 */
	public function tableExists($table) {
		$tables = $this->listTables();
		return in_array($this->tablePrefix. $table, $tables) ? true : false;
	}
	
	/**
	 * 返回系统模型
	 * @param unknown $tableName
	 * @return Model
	 */
	public function returnModel($tableName) {
		$modelName = ucfirst(preg_replace('/\_(.{1})/Ue', "ucfirst('$1')", $tableName));
		return M($modelName);
	}
	
	/**
	 * 写入系统日志
	 * @param string $content 内容
	 * @param string $logType 日志类型
	 * @param number $operateModelName 操作模型名称
	 */
	public function writeLog($content,$logType,$operateModelName = ''){
		if (empty($operateModelName)) $operateModelName = get_class($this) ;
		if (C('START_SQL_LOGS') && stripos(C('SQL_LOGS_TYPE'), $logType)!==false) {
			$logArray = array();
			$logArray['user_type'] = SESSION_TYPE;
			$logArray['ip'] = CLIENT_IP_NUM;
			$logArray['save_time'] = NOW_TIME;
			$logArray['log_type'] = strtoupper($logType);
			$logArray['userid'] = intval(GBehavior::$session['id']);
			$logArray['username'] = GBehavior::$session['username'] ? GBehavior::$session['username'] : GBehavior::$session['email'];
			$logArray['operate_model'] = $operateModelName;
			$logArray['content'] = (string) $content;
			$logArray['source_url'] = (string) $_SERVER['HTTP_REFERER'];
			$logArray['possible_sql'] = $this->getLastSql();
			$Log = M('Log');
			$status = $Log->data($logArray)->add();
		} 
		//同时写入框架日志
		Log::write($content);
		unset($Log,$logArray,$content,$operateModelName);
		return $status;
	}
	
	/**
	 * 验证登录IP
	 * @param numeric $loginType
	 * @return boolean
	 */
	public function checkLoginIP($loginType) {
		$currentIP = CLIENT_IP_NUM;
		$LoginError = M('LoginError');
		$userLoginError = $LoginError->where("error_ip={$currentIP} AND login_type=$loginType")->find();
		$time = NOW_TIME;
		if ($userLoginError && ($userLoginError['error_num'] >= C('LOGIN_ERROR_NUM') && $time-$userLoginError['error_time'] <= C('LOGIN_ERROR_TIME'))) {
			$content = "IP：".CLIENT_IP.'在不断尝试登录，已被封锁规定时间内不可再登录！';
			$this->writeLog($content,'USER_ERROR');
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * 处理登录出错
	 * @param unknown $loginType
	 * @param unknown $testData
	 */
	public function handleLoginError($loginType,$testData) {
		$testData['username'] = $testData['username'] ? $testData['username'] : $testData['email'];
		$ip = CLIENT_IP_NUM;
		$LoginError = M('LoginError');
		$time = NOW_TIME;
		$data = $LoginError->where("error_ip=$ip AND login_type=$loginType")->find();
		if ($data) {
			$LoginError->where("error_ip=$ip")->save(array('error_num'=>$data['error_num']+1,'error_time'=>$time,'test_username'=>$testData['username'],'test_password'=>$testData['password']));
		}else {
			$LoginError->data(array('login_type'=>$loginType,'error_ip'=>$ip,'error_num'=>$data['error_num']+1,'error_time'=>$time,'test_username'=>$testData['username'],'test_password'=>$testData['password']))->add();
		}
	}
	
	/**
	 * 写入成功登录的日志
	 * @param array $userData
	 * @param numeric $loginType
	 */
	public function handleLoginSuccess($userData,$loginType) {
		$LoginSuccess = M('LoginSuccess');
		$loginInfo = array();
		$loginInfo['login_ip'] = CLIENT_IP_NUM;
		$loginInfo['login_time'] = NOW_TIME;
		$loginInfo['userid'] = $userData['id'];
		$loginInfo['username'] = $userData['username'] ? $userData['username'] : $userData['email'];
		$loginInfo['login_type'] = $loginType;
		if (!$LoginSuccess->data($loginInfo)->add()) {
			$this->writeLog("添加成功的登录日志失败！", 'SYSTEM_ERROR');
		} else {
			$loginInfo;
		}
	}
	

	/****************************常用搜索设置Search*******************************/
	
	/**
	 * GET比较型数据
	 * @param string $field
	 * @param array $getKey
	 * @param string $type
	 * @param boolean $isToday
	 * @return array
	 */
	protected function compareGET($field,array $getKey,$type = 'time',$isToday = true) {
		$where = '';
		$url = '';
		$start_key = $getKey[0];
		$end_key = $getKey[1];
		switch ($type) {
			case 'time':
// 				时间设置==>没有设定时间，默认为拿出今天
				if ($isToday && empty($_GET[$start_key]) && empty($_GET[$end_key])) {
					$start_time = TODAY_START;
					$end_time = $start_time + 86400 - 1 ;
					$where .= " AND $field BETWEEN $start_time AND $end_time";
				} else {
					$start_type = strtotime($_GET[$start_key]);
					$end_type = strtotime($_GET[$end_key]);
				}
				break;
			case 'ip';
				$start_type = sprintf("%u",ip2long(Input::getVar($_GET[$start_key])));
				$end_type = sprintf("%u",ip2long(Input::getVar($_GET[$end_key])));
				break;
			default:
				$start_type = input::getVar($_GET[$start_key]);
				$end_type = input::getVar($_GET[$end_key]);
				break;
		}
		if (isset($_GET[$start_key]) && !empty($_GET[$start_key])) {
			$where .= " AND $field >= $start_type";
			$url .= "&$start_key={$_GET[$start_key]}";
		}
		if (isset($_GET[$end_key]) && !empty($_GET[$end_key])) {
			$where .= " AND $field <= $end_type";
			$url .= "&$end_key={$_GET[$end_key]}";
		}
		return array('url'=>$url,'where'=>$where);
	}
	
	/**
	 * 解析搜索常用其它GET数据
	 * @param array $array
	 * @param string $condition
	 * @return array
	 */
	protected function resolveGET($array,$condition = '=') {
		$result = array();
		foreach ($array as $key=>$value) {
			//数字索引，使用接收键为字段
			if (is_numeric($key)) $key = $value;
			if (isset($_GET[$value]) && $_GET[$value] != '') {
				$get_value = Input::getVar($_GET[$value]);
				$where = $condition=='=' ? " AND $key='$get_value'" : " AND $key LIKE '%$get_value%'";
				$url = "&$key=$get_value";
				$result['where'] .= $where;
				$result['url'] .= $url;
			}
		}
		return $result;
	}
	
	/****************************常用搜索设置Search  End*******************************/
	
	/****************************模型数据操作  Start*******************************/
	/**
	 * 模型数据验证
	 * @param array $setting
	 * @return array
	 */
	public function modelCheckData($setting = array()) {
		$postData = Tool::filterData($_POST['info']);
		$ruleData = array();
		//如果是会员模型，请在validata验证中都加上判断	a|规则，如果不允许为空，则下面的入库前会判断
		foreach ($postData as $key=>$values) {
			if (!empty($setting[$key]['pattern'])) {
				$ruleData[$key] = array($setting[$key]['pattern'],"{$setting[$key]['nick_name']}格式不正确！");
			}
		}
		return ValiData::_vail()->_check($ruleData,$postData);
	}
	
	/**
	 * 验证惟一字段
	 * @param string $key
	 * @param mixed $value
	 * @param numeric $mid
	 * @return boolean
	 */
	public function modelCheckUniqid($key,$value,$mid) {
		$model = F("models_$mid");
		$attachWhere = isset($_POST['id']) ? " AND id<>{$_POST['id']}" : '';
		return $this->table($this->tablePrefix.$model['table_name'])->where("`$key`='{$value}' $attachWhere")->find();
	}
	
	/**
	 * 模型_查找完整数据
	 * @param array $currentModel
	 * @param numeric $id
	 */
	public function modelFindOneFull($currentModel,$id,$key = 'id') {
		// 	内容，多表模型
		if ($currentModel['model_type'] == 'content' && $currentModel['setting']['is_alone'] == 2) {
			return $this->table("{$this->tablePrefix}{$currentModel['table_name']} AS t")->join("LEFT JOIN {$this->tablePrefix}{$currentModel['table_name']}_data AS d ON t.id=d.aid")->where("t.$key='$id'")->find();
		} else {
			return $this->table("{$this->tablePrefix}{$currentModel['table_name']}")->where("`$key`='$id'")->find();
		}
	}
	
	/**
	 * 模型添加数据
	 * @param array $currentModel
	 * @param array $postData
	 * @param array|string $setting
	 * @return boolean|Ambigous <mixed, boolean, string, unknown, false, number>
	 */
	public function modelAddData($currentModel,$postData, $setting = array()) {
		// 		添加主表
		$Model = $this->returnModel($currentModel['table_name']);
		$insertId = $Model->data($postData['main'])->add();
		// 		内容模型，非独立表
		if ($currentModel['model_type'] == 'content' && $currentModel['setting']['is_alone'] == 2 && $insertId) {
			$postData['append']['aid'] = $insertId;
			$AttachModel = $this->returnModel($currentModel['table_name'].'_data');
			$status = $AttachModel->data($postData['append'])->add();
			if (!$status) {
				//删除主表添加的数据
				$Model->where("id=$insertId")->delete();
				$postData['insert_id'] = false;
				return $postData;
			}
		}
		$postData['insert_id'] = $insertId;
		//返回最后处理的最新数据
		return $postData;
	}
	
	/**
	 *  模型修改数据
	 * @param array $currentModel
	 * @param array $postData
	 * @param array|string $setting
	 * @return boolean
	 */
	public function modelEditData($currentModel,$postData, $setting = array()) {
		// 		修改主表
		$Model = $this->returnModel($currentModel['table_name']);
		$status = $Model->where("id={$_POST['id']}")->save($postData['main']);
		// 		内容模型，非独立表
		if ($currentModel['model_type'] == 'content' && $currentModel['setting']['is_alone'] ==2 && $status !== false) {
			$AttachModel = $this->returnModel($currentModel['table_name'].'_data');
			$status = $AttachModel->where("aid={$_POST['id']}")->save($postData['append']);
		}
		$postData['save_status'] = $status === false ? false : true;
		//返回最后处理的最新数据
		return $postData;
	}
	
	/**
	 * 模型数据删除
	 * @param numeric $id
	 * @param array $currentModel
	 * @param array|string $setting
	 * @return unknown
	 */
	public function modelDeleteData($id,$currentModel,$setting = array()) {
// 		过滤非法数据
		$status = $this->table($this->tablePrefix.$currentModel['table_name'])->where("id IN($id)")->delete();
		if ($status) {
			// 		内容模型，非独立表
			if ($currentModel['model_type'] == 'content' && $currentModel['setting']['is_alone'] ==2) {
				$status = $this->table($this->tablePrefix.$currentModel['table_name'].'_data')->where("aid IN($id)")->delete();
			} else {
				$this->writeLog("系统出错，删除模型副表数据失败，模型id{$currentModel['id']}", 'SYSTEM_ERROR');
			}
		} else {
			$this->writeLog("系统出错，删除模型数据失败，模型id{$currentModel['id']}", 'SYSTEM_ERROR');
		}
		return $status;
	}
}
?>