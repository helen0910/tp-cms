<?php
/**
 * 集成模块插件操作
 * 
 */
class ModulesAction extends GlobalAction {
	
	protected function _initialize() {
		parent::_initialize();
		parent::BackEntranceInit();
		$this->model = D('Modules');
	}
	
	/* 模块插件列表 */
	public function index() {
		$modules = $this->model->getModules();
		$this->assign('modules',$modules);
		$this->display();
	}

	/* 模块按装 */
	public function add() {
		/* 检测模块是否按装 */
		$module_dir = $this->checkData('module_dir');
		$moduleData = $this->findOneData($module_dir,'module_dir');
		if ($moduleData) $this->error('此模块已存在，不可再按装！');
		if (IS_POST) {
			$moduleDirPath = APP_PATH."Modules/Modules/_src/$module_dir/";
			/* 插入数据表 */
			if (file_exists("$moduleDirPath/Install/sql.sql")) {
				$result = $this->model->splitSql("$moduleDirPath/Install/sql.sql");
				if (!$result) $this->error('增加模块数据表失败！');
			}
			/* 插入节点SQL */
			if ("$moduleDirPath/Install/Extention.inc.php") {
				$status = require "$moduleDirPath/Install/Extention.inc.php";
				if (!$status['status']) $this->error($status['error_info']);
			}
			/* /* 读取配置 */
			if (!file_exists("$moduleDirPath/Install/Config.inc.php")) $this->error('未找到此模块对应配置文件，无法进行按装！');
			$config = require "$moduleDirPath/Install/Config.inc.php";
			$status = $this->model->addData($config);
			if ($status) {
				//更新缓存
				R('Back/Public/_modules');
				R('Back/Public/_admin_rule');
			}
			$this->addPublicMsg($status,array('模块安装成功！','模块安装失败！'));
		}else {
			//拿出配置
			$config = require APP_PATH."Modules/Modules/_src/$module_dir/Install/Config.inc.php";
			$this->assign('config',$config);
			$this->display();
		}
	}
	
	/* 更改模块状态，是否禁用 */
	public function m_status() {
		$module_dir = $this->checkData('module_dir');
		$m_status = $this->checkData('m_status');
		$status = $this->model->where("module_dir='$module_dir'")->setField('is_disabled',$m_status);
		if ($status!==false) {
			//修改节点状态
			$likeName = APP_NAME.'/Modules/'.$module_dir;
			$status = M('AdminRule')->where("name LIKE '$likeName%'")->setField('status',$m_status);
		}
		if ($status !== false) {
			//更新缓存
			R('Back/Public/_modules');
			R('Back/Public/_admin_rule');
		}
		$this->editPublicMsg($status,array('成功更改模块状态，请及时更新缓存！','更新模块状态失败！'));
	}
	
	/* 模块卸载 */
	public function delete() {
		$status = $this->model->deleteData($this->checkData('module_dir'));
		if ($status) {
			//更新缓存
			R('Back/Public/_modules');
			R('Back/Public/_admin_rule');
		}
		$this->deletePublicMsg($status);
	}
	
	/* 模块zip包上传 */
	public function upload() {
		if (IS_POST) {
			$postData = Tool::filterData($_POST['up_module']['file']);
			$Attachment = D('Attachment/Attachment');
			$newAttachment = array();
			foreach ($postData as $value) {
				$result = $Attachment->checkToken($value);
				/* 启用的是公共上传模块，过滤非zip格式 */
				if (end(explode('.', $result[0])) !== 'zip') continue;
				if ($result) $newAttachment[] = $result;
			}
			foreach ($newAttachment as $attachValues) {
				$Zip = new PclZip(__PATH__.$attachValues[0]);
				$list = $Zip->extract(PCLZIP_OPT_PATH, APP_PATH.'Modules/Modules/_src/');
				if (!$list) {
					continue;
				}
			}
			$this->success('模块已成功上传，并成功过滤非正常模块压缩文件！',U('index'));
		} else {
			$this->display();
		}
	}

}
?>