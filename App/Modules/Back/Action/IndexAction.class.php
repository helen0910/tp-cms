<?php
/**
 * 后台首页
 * 
 *
 */
class IndexAction extends GlobalAction {
	
	protected function _initialize() {
		parent::_initialize();
		parent::BackEntranceInit();
	}
	
	public function index() {
		$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
		$newRuleArray = array();
		foreach ($ruleData as $ruleValue) {
			if ($ruleValue['show_status'] == 1) {
				$ruleValue['name'] = trim($ruleValue['name'],APP_NAME);
				$newRuleArray[$ruleValue['id']] = $ruleValue;
			}
		}
		$Tree = new Tree();
		$Tree->init($newRuleArray);
		$ruleData = $Tree->get_tree_array(0);
		$this->assign('ruleData',$ruleData);
		unset($newRuleArray);
		unset($ruleData);
		$this->display();
	}
	
}
?>