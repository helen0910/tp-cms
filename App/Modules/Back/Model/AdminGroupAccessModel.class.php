<?php
/**
 * 管理员组和管理员关联模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class AdminGroupAccessModel extends GlobalModel {
	
	/**
	 * 设置管理员和组的关联性
	 * @param array $groupArray
	 * @param numeric $adminId
	 */
	public function setAccess($groupArray,$adminId) {
		//删除旧数据
		$this->where("uid=$adminId")->delete();
		$accessArray = array();
		$i = 0;
		foreach ($groupArray as $groupVal) {
			$accessArray[$i]['uid'] = $adminId;
			$accessArray[$i]['group_id'] = $groupVal;
			$i++;
		}
		$status = $this->addAll($accessArray);
		if (!$status) {
			$this->writeLog("写入管理员和管理员关联组失败，管理员用户ID:{$adminId}", 'SYSTEM_ERROR');
		}
		return $status;
	}
	
	/**
	 * 查找管理员组
	 * @param string|numeric $id
	 * @return Ambigous <mixed, NULL, multitype:Ambigous <unknown, string> unknown , unknown, multitype:>
	 */
	public function findAdminGroup($id = '') {
		$id = empty($id) ? $_SESSION[C('ADMIN_SESSION')]['id'] : $id;
		return $this->where("uid=$id")->getField('group_id',true);
	}
	
	/**
	 * 	取得当前管理员的所有权限
	 * @return Ambigous <multitype:, mixed, number, boolean, string>
	 */
	public function findCurrentAdminRule() {
		$groupArray = $this->findAdminGroup();
		$ruleData = array();
		// 		该用户的所有权限
		foreach ($groupArray as $groupVal) {
			$data = F("admin_rule_$groupVal");
			if (!$data) continue;
			$ruleData += $data;
		}
		return $ruleData;
	}

	/**
	 * 取得管理员的导航权限
	 * @return Ambigous <multitype:, mixed, number, boolean, string>
	 */
	public function findCurrentAdminNavigate() {
		$groupArray = $this->findAdminGroup();
		$navigateData = array();
		// 		该用户的所有权限
		foreach ($groupArray as $groupVal) {
			$data = F("group_navigate_$groupVal");
			if (!$data) continue;
			$navigateData += $data;
		}
		return $navigateData;
	}
	
	
}
?>