<?php
/**
 * 系统日志模型操作
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class LogModel extends GlobalModel {
	
	/**
	 * 运行日志模型分页
	 * return array
	 */
	public function page() {
		$where = '1=1';
		$options = $this->compareGET('save_time',array('start_time','end_time'),'time',true);
		$options['where'] = $where.$options['where'] ;
		$ipoptions = $this->compareGET('ip',array('start_ip','end_ip'),'ip');
		$options['where'] .= $ipoptions['where'];
		$options['url'] .= $ipoptions['url'];
		$resolveOptions = $this->resolveGET(array('user_type','log_type','operate_model'));	
		$options['where'] .= $resolveOptions['where'];
		$options['url'] .= $resolveOptions['url'];
		if (!empty($_GET['username'])) {
			if (is_numeric($_GET['username'])) {
				$userid = intval($_GET['username']);
				$options['where'] .= " AND userid={$userid}";
			} else {
				$username = Input::getVar($_GET['username']);
				$options['where'] .= " AND username='{$username}'";
			}
			$options['url'] .= "&username={$_GET['username']}";
		}
		$total = $this->where($options['where'])->count(1);
		$Page = new Page($total,__ACTION__.'?page={PAGE}'.$options['url']);
		$data = array();
		$data[0] = $this->where($options['where'])->order('save_time DESC')->limit($Page->limit())->select();
		if ($data[0]) {
			$IpLocation = new IpLocation();
			foreach ($data[0] as &$values) {
				$values['ip'] = long2ip($values['ip']);
				$values['ip_location'] = $IpLocation->getlocation($values['ip']);
			}
		}
		$data[1] = $Page->setList('inner',2)->other('<span>{PAGE}/{SUM}页&nbsp;{LIMIT}/{TOTAL}条</span>')->show();
		return $data;
	}
	/**
	 * 登录成功的分页查询
	 * @return array
	 */
	public function successLoing() {
		$where = '1=1';
		$options = $this->compareGET('login_time',array('start_time','end_time'),true);
		$options['where'] = $where.$options['where'] ;
		$ipoptions = $this->compareGET('login_ip',array('start_ip','end_ip'),'ip');
		$options['where'] .= $ipoptions['where'];
		$options['url'] .= $ipoptions['url'];
		$resolveOptions = $this->resolveGET(array('login_type'));
		$options['where'] .= $resolveOptions['where'];
		$options['url'] .= $resolveOptions['url'];
		if (!empty($_GET['username'])) {
			if (is_numeric($_GET['username'])) {
				$userid = intval($_GET['username']);
				$options['where'] .= " AND userid={$userid}";
			} else {
				$username = Input::getVar($_GET['username']);
				$options['where'] .= " AND username='{$username}'";
			}
			$options['url'] .= "&username={$_GET['username']}";
		}
		$total = $this->table($this->tablePrefix.'login_success')->where($options['where'])->count(1);
		$Page = new Page($total,__ACTION__.'?page={PAGE}'.$options['url']);
		$data = array();
		$data[0] = $this->table($this->tablePrefix.'login_success')->where($options['where'])->order('login_time DESC')->limit($Page->limit())->select();
		if ($data[0]) {
			$IpLocation = new IpLocation();
			foreach ($data[0] as &$values) {
				$values['ip'] = long2ip($values['login_ip']);
				$values['ip_location'] = $IpLocation->getlocation($values['ip']);
			}
		}
		$data[1] = $Page->setList('inner',2)->other('<span>{PAGE}/{SUM}页&nbsp;{LIMIT}/{TOTAL}条</span>')->show();
		return $data;
	}
	/**
	 * 登录出错的数据查询
	 * @return array
	 */
	public function errorLoing() {
		$where = '1=1';
		$options = $this->compareGET('error_time',array('start_time','end_time'),'time',true);
		$options['where'] = $where.$options['where'] ;
		$ipoptions = $this->compareGET('error_ip',array('start_ip','end_ip'),'ip');
		$options['where'] .= $ipoptions['where'];
		$options['url'] .= $ipoptions['url'];
		$resolveOptions = $this->resolveGET(array('login_type','test_username'=>'username'));
		$options['where'] .= $resolveOptions['where'];
		$options['url'] .= $resolveOptions['url'];
		$total = $this->table($this->tablePrefix.'login_error')->where($options['where'])->count(1);
		$Page = new Page($total,__ACTION__.'?page={PAGE}'.$options['url']);
		$data = array();
		$data[0] = $this->table($this->tablePrefix.'login_error')->where($options['where'])->order('error_time DESC')->limit($Page->limit())->select();
		if ($data[0]) {
			$IpLocation = new IpLocation();
			foreach ($data[0] as &$values) {
				$values['ip'] = long2ip($values['error_ip']);
				$values['ip_location'] = $IpLocation->getlocation($values['ip']);
			}
		}
		$data[1] = $Page->setList('inner',2)->other('<span>{PAGE}/{SUM}页&nbsp;{LIMIT}/{TOTAL}条</span>')->show();
		return $data;
	}
}
?>