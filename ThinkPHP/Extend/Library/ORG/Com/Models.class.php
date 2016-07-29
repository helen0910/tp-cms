<?php
class Models {
	
	
	/**
	 * 显示模型
	 * @param array $model
	 * @param numeric|string $type
	 * @param array $data
	 * @param string $action_mark
	 * @return Ambigous <Ambigous, multitype:, number, unknown>
	 */
	public function getDisplay($model,$type,$data = array(),$action_mark ='') {
		$mid = $model['id'];
		$modelField = F("models_fields_$mid");
		uasort($modelField, create_function('$a,$b', 'return $a["sort"] < $b["sort"];'));
		require_once REQUIRE_PATH.'Fields/Field.class.php';
		//注意两边参数以及两边参数顺序，都不一样
		$modelField = $model['model_type']=='member'&&!empty($action_mark) ? $this->getMemberDisplay($modelField,$type,$action_mark,$data) : $this->getContentDisplay($modelField,$type,$data);
		return $modelField;
	}
	
	/**
	 * * 得到会员显示模型
	 * @return Ambigous <multitype:, number>
	 * @param array $modelField
	 * @param string $type
	 * @param string $model_mark
	 * @param array $data
	 * @return Ambigous <multitype:, number>
	 */
	private function getMemberDisplay($modelField,$type,$model_mark,array $data) {
		$modelAction = M('ModelAction')->where("name='$model_mark'")->limit(1)->getField('fields');
		$modelAction = explode(',', trim($modelAction,','));
		$newModelField = array();
		$fieldArray = array();
		foreach ($modelAction as $modelValues) {
			$modelValues = explode(':', $modelValues);
			$modelValues[0] = trim($modelValues[0]);
			$newModelField = $modelField[$modelValues[0]];
			/* 去除非前台投搞字段和内置字段 */
			if (SESSION_TYPE != 1 && ($newModelField['field_setting']['is_posted'] == 2 || $newModelField['field_setting']['is_internal'] == 1)) continue;
			/* 重新判断是否必填 */
			$newModelField['field_setting']['is_required'] = $modelValues[1] == 1 ? 1 : 2;
			/* 获取类名 */
			$className = ucfirst($newModelField['form_type']);
			if (!file_exists(REQUIRE_PATH."Fields/$className.class.php")) continue;
			/* 实例化，初始化 */
			require_once REQUIRE_PATH."Fields/$className.class.php";
			$fieldsObject = new $className();
			$fieldsObject->form = $newModelField;
			$fieldsObject->setting = $newModelField['field_setting'];
			$fieldsObject->type = $type;
			//1添加，其它表示修改
			$newModelField['html'] = $type==1 ? $fieldsObject->show() : $fieldsObject->show($data[$newModelField['field_name']]);
			$newModelField['html'] = $modelValues[1] == 1 ? str_replace('ignore="ignore"', '', $newModelField['html']) : preg_replace('/<([^\s]*)(.*)/', '<$1 ignore="ignore" $2',$newModelField['html']);
			$newModelField['field_setting']['is_basic'] == 1 ? $fieldArray['is_basic'][] = $newModelField : $fieldArray['is_append'][] = $newModelField;
		}
		return $fieldArray;
	}
	
	/**
	 *  * 得到内容或表单显示模型
	 * @param unknown $modelField
	 * @param numeric $type
	 * @param array $data
	 * @return Ambigous <multitype:, unknown>
	 */
	private function getContentDisplay($modelField,$type,array $data) {
		$fieldArray = array();
		foreach ($modelField as $model_key=>$model_values) {
			/* 去除非前台投搞字段和内置字段 */
			if (SESSION_TYPE != 1 && ($model_values['field_setting']['is_posted'] == 2 || $model_values['field_setting']['is_internal'] == 1)) continue;
			/* 获取类名 */
			$className = ucfirst($model_values['form_type']);
			if (!file_exists(REQUIRE_PATH."Fields/$className.class.php")) continue;
			/* 实例化，初始化 */
			require_once REQUIRE_PATH."Fields/$className.class.php";
			$fieldsObject = new $className();
			$fieldsObject->form = $model_values;
			$fieldsObject->setting = $model_values['field_setting'];
			$fieldsObject->type = $type;
			$model_values['html'] = $type == 1 ? $fieldsObject->show() : $fieldsObject->show($data[$model_key]);
			$model_values['field_setting']['is_basic'] == 1 ? $fieldArray['is_basic'][] = $model_values : $fieldArray['is_append'][] = $model_values;
		}
		return $fieldArray;
	}
	
	/**
	 * 得到会员动作处理字段
	 * @param array $modelField
	 * @param string $model_mark
	 * @return Ambigous <multitype:, number>
	 */
	public function getMemberActionFields($modelField,$model_mark) {
		$modelAction = M('ModelAction')->where("name='$model_mark'")->limit(1)->getField('fields');
		$modelAction = explode(',', trim($modelAction,','));
		$newModelField = array();
		$fieldArray = array();
		foreach ($modelAction as $modelValues) {
			$modelValues = explode(':', $modelValues);
			$modelValues[0] = trim($modelValues[0]);
			$newModelField = $modelField[$modelValues[0]];
			/* 去除非前台投搞字段[非内置字段] */
			if (SESSION_TYPE != 1 && ($newModelField['field_setting']['is_posted'] == 2 && $newModelField['field_setting']['is_internal']==2) ) continue;
			/* 重新判断是否必填 */
			$newModelField['field_setting']['is_required'] = $modelValues[1] == 1 ? 1 : 2;
			$fieldArray[$newModelField['field_name']] = $newModelField;
		}
		return $fieldArray;
	}
	
	/**
	 * 入库前执行的操作！
	 * 执行显示字段和内置字段
	 * @param array $postData
	 * @param array $modelField
	 * @param array $Model
	 * @return string|mixed|multitype:mixed
	 */
	public function before_storage($postData,$modelField,$Model) {
		$systemData = array();
		$appendData = array();
		/* 赋值全部数据 */
		require_once REQUIRE_PATH.'Fields/Field.class.php';
		/* 增加内置字段 */
		$postData = $this->set_internal($modelField,$postData);
		
		Field::$postData = $postData;
		
		foreach ($postData as $key=>&$value) {
			/* 安全过滤，防止恶意伪造表单充入库，前台非前台填写字段[但排除内置字段]，则过滤 */
			if (SESSION_TYPE != 1 && ($modelField[$key]['field_setting']['is_posted'] == 2) && $modelField[$key]['field_setting']['is_internal']==2) continue;
			/* 验证必填字段 */
			if ($modelField[$key]['field_setting']['is_required'] == 1 && empty($value)) {
				$postData['vail_status'] = false;
				$postData['vail_info'] = $modelField[$key]['nick_name'].'不能为空！';
				return $postData;
			}
			/* 验证数据惟一性 */
			if ($modelField[$key]['field_setting']['is_unique'] == 1) {
				$uniqidResult = $Model->modelCheckUniqid($key,$value,$modelField[$key]['mid']);
				if ($uniqidResult) {
					$postData['vail_status'] = false;
					$postData['vail_info'] = $modelField[$key]['nick_name'].'必须惟一！';
					return $postData;
				} 
			}
			$className = ucfirst($modelField[$key]['form_type']);
			/* 不存在的直接跳出 */
			if (!file_exists(REQUIRE_PATH."Fields/$className.class.php")) continue;
			/* 实例化并执行 */
			require_once REQUIRE_PATH."Fields/$className.class.php";
			$newClass = new $className();
			
			/* 开始执行1 ---> 执行公共方法 */
			if (method_exists($newClass, 'before_storage')) {
				$result = call_user_func_array(array($className,'before_storage'), array($value,$modelField[$key]));
				if (isset($result['vail_status']) && $result['vail_status'] === false) {
					return $result;
				} elseif ($result == '$delete$') {
					continue;//反回此标识则表示删除此字段值，不入库更新
				} elseif (!empty($result)) {
					$value = $result;
				}
			}
			
			/* 开始执行2 ---> 执行自身设置方法 */
			if (SESSION_TYPE == 1) {
				$func_name = $modelField[$key]['field_setting']['back_func'];
				$func_type = $modelField[$key]['field_setting']['back_func_type'];
			}else {
				$func_name = $modelField[$key]['field_setting']['front_func'];
				$func_type = $modelField[$key]['field_setting']['front_func_type'];
			}
			if (method_exists($newClass, $func_name) && $func_type != 2) {
				// 这里是$className非$newClass,为了共享静态区=============废弃
				$result = call_user_func_array(array($newClass,$func_name), array($value,$modelField[$key]));
				if (isset($result['vail_status']) && $result['vail_status'] === false) {
					return $result;
				} elseif ($result == '$delete$') {
					continue;//反回此标识则表示删除此字段值，不入库更新
				} elseif (!empty($result)) {
					$value = $result;
				}
			}
			//组合数据
			$modelField[$key]['field_setting']['is_system']==1 ? $systemData[$key] = $value : $appendData[$key] = $value;
		}
		$postData['main'] = $systemData;
		$postData['append'] = $appendData;
		return $postData;
	}
	
	/**
	 * 入库后执行的函数操作
	 * 执行显示字段和内置字段
	 * @param array $postData
	 * @param array $modelField
	 * @param numeric $insertId
	 */
	public function after_storage($postData,$modelField,$insertId) {
		require_once REQUIRE_PATH.'Fields/Field.class.php';
		/* 增加内置字段 */
		$postData = $this->set_internal($modelField,$postData);
		
		Field::$postData = $postData;//赋值新数据，与入库前的数据是不一致的，注意
		foreach ($postData as $key=>$value) {
			
			/* 安全过滤，防止恶意伪造表单充入库，前台非前台填写字段，则过滤 */
			if (SESSION_TYPE != 1 && ($modelField[$key]['field_setting']['is_posted'] == 2) && $modelField[$key]['field_setting']['is_internal']==2) continue;
			
			$className = ucfirst($modelField[$key]['form_type']);
			/* 不存在的直接跳出 */
			if (!file_exists(REQUIRE_PATH."Fields/$className.class.php")) continue;
			/* 实例化 */
			require_once REQUIRE_PATH."Fields/$className.class.php";
			$newClass = new $className();
			/* 执行公共方法 */
			if (method_exists($newClass, 'after_storage')) {
				call_user_func_array(array($className,'after_storage'), array($value,$modelField[$key],$insertId));
			}
			/* 执行自身函数 */
			if (SESSION_TYPE == 1) {
				$func_name = $modelField[$key]['field_setting']['back_func'];
				$func_type = $modelField[$key]['field_setting']['back_func_type'];
			}else {
				$func_name = $modelField[$key]['field_setting']['front_func'];
				$func_type = $modelField[$key]['field_setting']['front_func_type'];
			}
			if (method_exists($newClass, $func_name) && $func_type != 1) {
				//这里是$className非$newClass,为了共享静态区===============废弃，已改为object非object Name
				call_user_func_array(array($newClass,$func_name), array($value,$modelField[$key],$insertId));
			}
		}
	}
	
	/**
	 * 系统内置字段处理，只是为了让postData增加元素，默认设置为''，internal必须要有入库前的方法处理
	 * @param array $modelField
	 * @param array $postData
	 */
	private function set_internal($modelField,$postData = array()) {
		$internalArray = array();
		foreach ($modelField as $values) {
			/* 跳过非is_internal字段   isset兼容新增，很多字段没有is_internal*/
			if (!isset($values['field_setting']['is_internal']) || $values['field_setting']['is_internal'] == 2) continue;
			/* ==1时，即为内置字段 */
			$internalArray[$values['field_name']] = isset($values['field_setting']['default_value']) ? $values['field_setting']['default_value'] : '';
		}
		/*
		 $postData[$values['field_name']] = isset($values['field_setting']['default_value']) ? $values['field_setting']['default_value'] : '';
		 使用array_merge而非上面的 $postData[$values['field_name']]主要是为了后台修改时，一次性提交，不会被原有内置值覆盖
		*/
		return array_merge($internalArray,$postData);
	}
}
?>