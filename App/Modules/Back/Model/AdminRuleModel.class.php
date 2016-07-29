<?php
/**
 * 管理员规则模型
 * 
 *
 */
class AdminRuleModel extends GlobalModel {

	/**
	 * 验证数据
	 * @param array $setting
	 * @return Ambigous <boolean, unknown, multitype:unknown string >
	 */
	public function checkData($setting = array()) {
		$postData = Tool::filterData($_POST);
		return ValiData::_vail()->_check(array(
				'title'=>array('s1-30','节点名称格式不正确！！！'),
				'app_name'=>array('r/^[a-z\_][\w]{0,29}$/i','项目名称格式不正确！'),
				'group_name'=>array('r/^[a-z\_][\w]{0,29}$/i','分组名称格式不正确！'),
				'module_name'=>array('r/^[a-z\_][\w]{0,29}$/i','模块名称格式不正确！'),
				'action_name'=>array('r/^[a-z\_][\w]{0,29}$/i','方法名称格式不正确！'),
				'condition'=>array('a|s1-100','规则条件格式不正确！'),
				'sort'=>array('n1-8','字段排序格式不正确！'),
				'remark'=>array('a|s1-255','备注格式不正确！'),
		), $postData);
	}
	
	/**
	 * 添加数据
	 * @param array $postData
	 * @param array $setting
	 * @return Ambigous <mixed, boolean, string, unknown, false, number>
	 */
		public function addData($postData,$setting = array()) {
			$postData['name'] = "{$postData['app_name']}/{$postData['group_name']}/{$postData['module_name']}/{$postData['action_name']}";
			return $this->data($postData)->add();
		}
	
		/**
		 * 编辑数据
		 * @param array $postData
		 * @param array $setting
		 * @return Ambigous <boolean, false, number>
		 */
		public function editData($postData,$setting = array()) {
			$postData['name'] = "{$postData['app_name']}/{$postData['group_name']}/{$postData['module_name']}/{$postData['action_name']}";
			return $this->where("id={$_POST['id']}")->save($postData);
		}
	
		/**
		 * 数据删除
		 * @param numeric|string $id
		 * @return Ambigous <mixed, boolean, false, number>|boolean
		 */
		public function deleteData($id) {
			// 		查出它的子级
			$delData = $this->where("pid IN($id)")->field('id')->select();
			// 		删除自己
			$status = $this->where("id IN($id)")->delete();
			if (!$status) return $status;
			if (!empty($delData)) {
				$_sonId = '';
				foreach ($delData as $_delVal) {
					$_sonId .= $_delVal['id'].',';
				}
				$_sonId = trim($_sonId,',');
				// 			递归查询
				$this->deleteData($_sonId);
			}
			return true;
		}
	
}
?>