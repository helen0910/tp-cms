<?php
/**
 * IP封禁模型处理
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class BanIpModel extends GlobalModel {
	
	/**
	 * 数据验证
	 * @return array
	 */
	public function checkData() {
		$postData = Tool::filterData($_POST);
		return ValiData::_vail()->_check(array(
			'start_ip'=>array('r/^[\d\.]{7,15}$/','起始IP格式不正确！'),
			'end_ip'=>array('r/^([\d\.]{7,15})?$/','结束IP格式不正确！'),
			'lifted_time'=>array('s5,','解封时间不能为空！'),
			'remark'=>array('a|s1-150','备注格式不正确！'),
		),$postData);
	}
	
	/**
	 * 添加数据
	 * @param array $postData
	 * return boolean|numeric
	 */
	public function addData($postData) {
		$postData['start_ip'] = sprintf('%u',ip2long($postData['start_ip']));
		$postData['end_ip'] = empty($postData['end_ip']) ? $postData['start_ip'] : sprintf('%u',ip2long($postData['end_ip']));
		$postData['save_time'] = NOW_TIME;
		$postData['lifted_time'] = strtotime($postData['lifted_time']);
		return $this->data($postData)->add();
	}
	
	/**
	 * 编辑数据
	 * @param array $postData
	 * return boolean|numeric
	 */
	public function editData($postData) {
		$postData['start_ip'] = sprintf('%u',ip2long($postData['start_ip']));
		$postData['end_ip'] = empty($postData['end_ip']) ? $postData['start_ip'] : sprintf('%u',ip2long($postData['end_ip']));
		$postData['lifted_time'] = strtotime($postData['lifted_time']);
		return $this->data($postData)->save();
	}
	
	/**
	 * 数据分页
	 * return array
	 */
	public function getPage() {
		if (!empty($_GET['ip'])) {
			$ip = sprintf('%u',ip2long(Input::getVar($_GET['ip'])));
			$where = "$ip BETWEEN start_ip AND end_ip";
			$url = __ACTION__.'?ip='.$_GET['ip'];
		} else {
			$where = '';
			$url = '';
		}
		$total = $this->where($where)->count(1);
		$Page = new Page($total,$url);
		$pageData = array();
		$pageData[0] = $this->where($where)->limit($Page->limit())->select();
		$pageData[1] = $Page->show();
		return $pageData;
	}
	
	/**
	 * 更新封禁IP缓存
	 * @param numeric|string $id
	 * @param numeric $type 1添加或更新2删除
	 * return ;
	 */
	public function updateCache($id,$type =1) {
		$banIPCache = F('ban_ip');
		if (!$banIPCache) $banIPCache = array();
// 		添加
		if ($type == 1) {
			$data = $this->where("id=$id")->find();
			if (isset($banIPCache[$id])) {
				unset($banIPCache[$id]);
			}
			$banIPCache[$id] = $data;
		} else {//删除
			$idArray = explode(',',$id);
			foreach ($idArray as $idValue) {
				unset($banIPCache[$idValue]);
			} 
		}
		F('ban_ip',$banIPCache);
		return ;
	}
	
	/**
	 * IP封禁验证
	 * @return boolean
	 */
	public function checkIP() {
		$ip = CLIENT_IP_NUM;
		$cacheIP = F('ban_ip');
		if($cacheIP) {
			foreach ($cacheIP as $values) {
// 				IP在封禁列中
				if ($ip >= $values['start_ip'] && $ip <= $values['end_ip']) {
					if ($values['lifted_time'] - NOW_TIME > 0) {
						return false;
					}
				}
			}
		}
		return true;
	}
}
?>