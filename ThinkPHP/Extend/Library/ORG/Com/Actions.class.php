<?php
/**
 * 用户行为设置
 * 
 *
 */
class Actions {
	
	/**
	 * 添加用户行为日志
	 * @param string $action 行为标识
	 * @param string $model 触发行为的模型名
	 * @param int $record_id 触发行为的记录id
	 * @param int $user_id 执行行为的用户id
	 * @return boolean|string
	 */
	public static function addActionsLog($action, $action_table, $record_id, $user_id = null) {
		//参数检查
		if(empty($action) || empty($action_table) || empty($record_id)){
			return false;
		}
		if(empty($user_id)){
			$user_id = intval(GBehavior::$session['id']);
		}
		
		//查询行为,判断是否执行
		$action_info = D('Member/Action')->findOneData($action);
		if (!$action_info) {
			return false;
		}
		
		//插入行为日志
		$data = array();
		$data['action_id'] = $action_info['id'];
		$data['user_id'] = $user_id;
		$data['action_table'] = $action_table;
		$data['record_id'] = $record_id;
		$data['save_time'] = NOW_TIME;
		$insertId = M('ActionLog')->add($data);
		$data = array();
		$data['acid'] = $insertId;
		$data['action_ip'] = CLIENT_IP_NUM;
		//系统日志记录操作url参数
		$data['remark'] = '操作url：'.$_SERVER['REQUEST_URI'];
		M('ActionLogData')->add($data);
		if(!empty($action_info['rule'])){
			//解析行为
			$rules = self::parseActions($action, $user_id);
			//执行行为
			$res = self::executeActions($rules, $action_info['id'], $user_id);
			return $res;
		}
	}
	
	/**
	 * 解析行为规则
	 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
	 *              field->要操作的字段；
	 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
	 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
	 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
	 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
	 * 单个行为后可加 ； 连接其他规则
	 * @param string $action 行为id或者name
	 * @param int $self 替换规则里的变量为执行用户的id
	 * @return boolean|array: false解析出错 ， 成功返回规则数组
	 */
	public static function parseActions($action, $self){
		if(empty($action)){
			return false;
		}
	
		$info = D('Member/Action')->findOneData($action);
		if (!$info) {
			return false;
		}

		//解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
		$rules = $info['rule'];
		$rules = str_replace('{$self}', $self, $rules);
		$rules = explode(';', $rules);
		$return = array();
		foreach ($rules as $key=>&$rule){
			$rule = explode('|', $rule);
			foreach ($rule as $k=>$fields){
				$field = empty($fields) ? array() : explode(':', $fields);
				if(!empty($field)){
					$return[$key][$field[0]] = $field[1];
				}
			}
			//cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
			if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
				unset($return[$key]['cycle'],$return[$key]['max']);
			}
		}
	
		return $return;
	}
	
	/**
	 * 执行行为
	 * @param array $rules 解析后的规则数组
	 * @param int $action_id 行为id
	 * @param array $user_id 执行的用户id
	 * @return boolean false 失败 ， true 成功
	 * @author huajie <banhuajie@163.com>
	 */
	public static function executeActions($rules, $action_id, $user_id){
		if(!$rules || empty($action_id) || empty($user_id)){
			return false;
		}
	
		$return = true;
		$ActionLog = M('ActionLog');
		foreach ($rules as $rule){
	
			//检查执行周期
			$map = array('action_id'=>$action_id, 'user_id'=>$user_id);
			$map['save_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
			$exec_count = $ActionLog->where($map)->count();
			if($exec_count > $rule['max']){;
				continue;
			}
	
			//执行数据库操作
			$Model = M(ucfirst($rule['table']));
			$field = $rule['field'];
			$res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));
			if(!$res){
				$return = false;
			}
		}
		return $return;
	}
	
// 	/**
// 	 * 获取此条Action信息
// 	 * @param string $action
// 	 * @return boolean|array|Ambigous <array, mixed, boolean, NULL, multitype:, unknown, string>
// 	 */
// 	private static function getAction($action) {
// 		//查询行为信息
// 		$info = (array)S('action');
// 		if (isset($info[$action]) && !empty($info[$action])) {
// 			if ($info[$action]['a_status']!=1) {
// 				return false;
// 			} else {
// 				return $info[$action];
// 			}
// 		} else {
// 			$Action = D('Member/Action');
// 			$info = $Action->where("name='$action'")->find();
// 			//正常模式，加入缓存
// 			if ($info && $info['a_status'] == 1) {
// 				$Action->_update_cache($info);
// 				return $info;
// 			} else {
// 				return false;
// 			}
// 		}
// 	}
}
?>