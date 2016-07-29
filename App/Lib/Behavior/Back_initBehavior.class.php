<?php
/**
 * 后台基础行为
 * 
 *
 */
class Back_initBehavior extends Behavior {
	
	public function run(&$params) {
// 		得到当前操作规则
		$params['rule'] = $this->getCurrentRule();
	}
	
	//得到当前同级的规则
	private function getCurrentRule() {
		//取出用户组
		$ruleData = $_SESSION[C('SUPER_ADMIN')] ? F('admin_super_rule') : D('Back/AdminGroupAccess')->findCurrentAdminRule();
// 		取出当前操作规则及同级规则
		$ruleArray = array();
		foreach ($ruleData as $values) {
			if ($values['show_status'] != 1) continue;
			$values['name'] = trim($values['name'],APP_NAME);
			$urlRule = explode('/', $values['name']);
			if ($urlRule[1] === GROUP_NAME && $urlRule[2] === MODULE_NAME) {
				$ruleArray['current_rule'][] = $values;
				if ($values['node_type'] == 2) {
					$ruleArray['link'][] = $values;
				}elseif ($values['node_type'] == 3) {
					$ruleArray['operation'][] = $urlRule[3];
				} elseif ($values['node_type'] == 1) {//引导类型，（其实是父级）
					$ruleArray['guide'] = $values['title'];
				}
				if ($values['node_type'] == 4 || $values['node_type'] == 3) {
					$ruleArray['other'][] = $urlRule[3];
				}
			}
		}
		unset($ruleData);
		return $ruleArray;
	}
	
	
}
?>