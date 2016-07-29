<?php
/**
 * 日志模型控制器
 * 
 * 常用模型方法  index -->列表  add -->添加  edit-->编辑  delete-->删除
 */
class LogAction extends GlobalAction {
	
	protected function _initialize() {
		parent::_initialize();
		parent::BackEntranceInit();
		$this->model = D('Log');
	}
	
	public function index() {
		$logData = $this->model->page();
		$this->assign('logData',$logData);
// 		当前模型
		$modelFile = str_replace(LIB_PATH.'Model/', '', str_replace('.class.php', '', glob(LIB_PATH.'Model/*.php')));
		$this->assign('modelFile',$modelFile);
		$this->display();
	}
	
	/* 登录成功日志 */
	public function login_success_log() {
		$logData = $this->model->successLoing();
		$this->assign('logData',$logData);
		$this->display();
	}
	
	/* 登录错误日志 */
	public function login_error_log() {
		$logData = $this->model->errorLoing();
		$this->assign('logData',$logData);
		$this->display();
	}
	
	public function delete() {
		$id = $this->checkData('id');
		$type = $this->checkData('type','GET');
		$status = $this->model->table(DB_PREFIX.$type)->where("id IN($id)")->delete();
		$this->deletePublicMsg($status);
	}
}
?>