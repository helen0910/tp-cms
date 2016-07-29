<?php
/**
 * 基础控制器信息
 * 
 *
 */
class GlobalAction extends Action {
	protected $model = null;//当前数据模型
	protected $currentModel = null;//当前模型
	protected $currentModelField = null;//当前模型字段
	protected $tpl;
	protected function _initialize() {
		//全局行为扩展
		tag('G');
		
		//模板session调用
		$this->assign('session',GBehavior::$session);
		//非后台设置模板目录if (SESSION_TYPE != 1) 
		$this->tpl = TEMPLATE_PATH.DEFAULT_SKIN.'/';
	}
	
	/**
	 * 后台入口初始化
	 */
	protected function BackEntranceInit() {
		if (!GBehavior::$session) $this->error('请先登录！',__ROOT__.'/admin.php');
		//非超管判断权限
		if (!SUPER_ADMIN) {
			//非Public权限
			if (MODULE_NAME != 'Public') {
				$Auth = new Auth();
				$ruleUrl = APP_NAME.'/'.GROUP_NAME.'/'.MODULE_NAME.'/'.ACTION_NAME;
				if (!$Auth->check($ruleUrl, GBehavior::$session['id'])) $this->error('您无权操作！');
			}
		}
		//后台初始标签位
		$params = array('rule'=>array());
		tag('Back_init',$params);
		//当前Url
		$this->assign('ruleUrl',$ruleUrl);
		//分配子级权限
		$this->assign('currentRule',$params['rule']['current_rule']);
		//引导的父级名称
		$this->assign('parentRuleName',$params['rule']['guide']);
		//链接权限
		$this->assign('ruleLink',$params['rule']['link']);
		//操作权限
		$this->assign('ruleOperate',$params['rule']['operation']);
		//其它权限加操作权限
		$this->assign('ruleOther',$params['rule']['other']);
	}
	
	/**
	 * 简单数据过滤验证
	 * @param string $key
	 * @param string $type
	 * @param string $isPrompt
	 * @return Ambigous <string, string>
	 */
	protected function checkData($key,$type = '',$isPrompt = true) {
		if ($type) $type = strtoupper($type);
		$keyArray = (array)$key;
		$keyValueArray = array();
		foreach ($keyArray as $key) {
			if ($type == 'GET') {
				$keyVal = $_GET[$key];
			} elseif ($type == 'POST') {
				$keyVal = $_POST[$key];
			} else {
				$keyVal = IS_POST ? $_POST[$key] : $_GET[$key];
			}
			$keyVal = Input::getVar(trim($keyVal));
			if (empty($keyVal) && $isPrompt) {
				parent::error(C('PARAM_MISSING'));
			} else {
				$keyValueArray[$key] = $keyVal;
			}
		}
		unset($keyArray,$type);
		return count($keyValueArray)==1 ? $keyValueArray[$key] : $keyValueArray;
	}
	
	/**
	 * 验证操作的时间间隔，此方法如果禁用COOKIE，则无用，先用着吧
	 * @param string $checkModel
	 */
	protected function checkOperTime($checkModel = null) {
		if ($checkModel) {
			if (NOW_TIME - GBehavior::$session['OPERATE_TIME_MARK'] < C('OPERATE_TIME')) {
				$this->writeLog('此用户操作['.$checkModel.']过快！', 'USER_ERROR');
				$this->error(C('OPERATE_INFO'));
			}
		} else {
			//更新SESSION
			if (SESSION_TYPE == 1) {
				$_SESSION[C('ADMIN_SESSION')]['OPERATE_TIME_MARK'] = NOW_TIME;
			} elseif (SESSION_TYPE == 2) {
				$_SESSION[C('MEMBER_SESSION')]['OPERATE_TIME_MARK'] = NOW_TIME;
			} 
		}
		return ;
	}
	
	/**
	 * 加工提示处理
	 * @param string $info
	 * @param string $type
	 * @return Ambigous <mixed, void, NULL, unknown, multitype:>
	 */
	private function processedPrompt($info) {
		if (empty($info)) {
			$info['success_info'] = C('SUCCESS_INFO');
			$info['error_info'] = C('ERROR_INFO');
		} else {
			if (is_array($info)) {
				$info['success_info'] = empty($info[0]) ? C('SUCCESS_INFO') : $info[0];
				$info['error_info'] = empty($info[1]) ? C('ERROR_INFO') : $info[1];
			} else {
				$newInfo = array();
				$newInfo['success_info'] = $info;
				$newInfo['error_info'] = C('ERROR_INFO');
				$info = $newInfo;
				unset($newInfo);
			}
		}
		return $info;
	}
	
	/**
	 * 公共添加提示
	 * @param boolean|numeric|string $status
	 * @param string|array $info
	 * @param string $success_url
	 */
	protected function addPublicMsg($status,$info = array(),$success_url = '') {
		$info = $this->processedPrompt($info);
		$success_url = empty($success_url) ?  U('index') : $success_url;
		if ($status) {
			//更新操作时间
			$this->checkOperTime();
			$this->writeLog("添加数据成功！", 'INFO');
			parent::success($info['success_info'],$success_url);
		} else {
			$this->writeLog("添加数据失败！", 'SYSTEM_ERROR');
			parent::error($info['error_info']);
		}
	}
	
	/**
	 * 公共编辑提示
	 * @param boolean|numeric|string $status
	 * @param string|array $info
	 * @param string $success_url
	 */
	protected function editPublicMsg($status,$info = array(),$success_url = '') {
		$info = $this->processedPrompt($info);
		$success_url = empty($success_url) ?  U('index') : $success_url;
		if ($status !== false) {
			//更新操作时间
			$this->checkOperTime();
			$this->writeLog("修改数据成功！", 'INFO');
			parent::success($info['success_info'],$success_url);
		} else {
			$this->writeLog("修改数据失败！", 'SYSTEM_ERROR');
			parent::error($info['error_info']);
		}
	}
	
	/**
	 * 公共删除提示
	 * @param boolean|numeric|string $status
	 * @param string|array $info
	 * @param string $success_url
	 */
	protected function deletePublicMsg($status,$info = array(),$success_url = '') {
		$info = $this->processedPrompt($info);
		$success_url = empty($success_url) ?  U('index') : $success_url;
		if ($status) {
			//更新操作时间
			$this->checkOperTime();
			$this->writeLog("删除数据成功！", 'INFO');
			parent::success($info['success_info'],$success_url);
		} else {
			$this->writeLog("删除数据失败！", 'USER_ERROR');
			parent::error($info['error_info']);
		}
	}
	
	/**
	 * 分配网站加密值
	 * @param string $value
	 * @param string $tokenName
	 */
	protected function encryptToken($value,$tokenName = 'token') {
		$encryptValue = Tool::encrypt($value);
		$this->assign($tokenName,$encryptValue);
	}
	
	/**
	 * 网加Token值比对
	 * @param string $value
	 * @param string $tokenValue
	 * @param string $tokenName
	 */
	protected function matchToken($value = '',$tokenValue = '',$tokenName = 'token') {
		if (empty($value)) $value = IS_POST ? $_POST['id'] : $_GET['id'];
		$encryptValue = Tool::encrypt($value);
		if (empty($tokenValue)) $tokenValue = IS_POST ? $_POST[$tokenName] : $_GET[$tokenName];
		if ($tokenValue!==$encryptValue) {
			$content = 'Token值比对错误，数据值被拒绝，非法数据,原因：“可能因恶意篡改”！';
			$this->writeLog($content, 'USER_ERROR');
			$this->error(C('MALICE_INFO'));
		}
	}
	
	/**
	 * 记录日志
	 * @param string $content
	 * @param string $logType
	 * @param string $Model
	 */
	protected function writeLog($content,$logType,$Model = null) {
		if ($Model) {
			if (!method_exists($Model, 'writeLog')) $Model = D('Global');
		} else {
			$Model = $this->model ? $this->model : D('Global');
		}
		$Model->writeLog($content,$logType,get_class($Model));
		unset($Model);
	}
	
	/**
	 * 查出一条完整的数据
	 * @param string $value
	 * @param string $key
	 * @param string|object $model
	 * @return Ambigous <mixed, boolean, NULL, multitype:, unknown, string>
	 */
	protected function findOneData($value,$key = 'id',$model = null) {
		if ($model) {
			if (is_object($model)) {
				$Model = $model;
			} else {
				$Model = M($model);
			}
		} else {
			$Model = $this->model;
		}
		$data = $Model->where("$key='$value'")->find();
		unset($Model);
		return $data;
	}
	
	/******==========================模型数据操作============================********/
	
	/**
	 * 模型添加显示
	 */
	protected function modelAddDisplay($action_mark = '') {
		$Models = new Models();
		$fieldArray = $Models->getDisplay($this->currentModel,1,array(),$action_mark);
		$this->assign('fieldArray',$fieldArray);
		unset($Models);
		return $fieldArray;
	}
	/**
	 * 模型提交处理
	 * @param string|array $setting
	 * @param array $action  会员模型动作
	 * @return numeric
	 */
	protected function modelAddPost($setting = array(),$action = '') {
		//获取模型字段
		$modelFields = F("models_fields_{$this->currentModel['id']}");
		$Models = new Models();
		//会员模型处理动作
		if ($action) $modelFields = $Models->getMemberActionFields($modelFields, $action);
		//数据验证
		$postData = $this->model->modelCheckData($modelFields);
		if (!$postData['vail_status']) $this->error($postData['vail_info']);
		//入库前数据处理
		$postData = $Models->before_storage($postData, $modelFields,$this->model);
		if (!$postData['vail_status']) $this->error($postData['vail_info']);
		//写入数据，并获取最新处理的数据
		$postData = $this->model->modelAddData($this->currentModel,$postData,$setting);
		if ($postData['insert_id']) $Models->after_storage($postData, $modelFields,$postData['insert_id']);
		return $postData['insert_id'];
	}
	
	/**
	 * 模型编辑显示
	 * @param string $id
	 * @return array
	 */
	protected function modelEditDisplay($id = '',$action_mark = '') {
		$id = empty($id) ? $this->checkData('id') : $id;
		$current = $this->model->modelFindOneFull($this->currentModel,$id);
		$current ? $this->assign('current',$current) : $this->error(C('FIND_ERROR'));
		// 		修改需要修改的模型数据
		if (method_exists($this,'modelModelData')) $current = $this->modelModelData($current);
		// 		生成表单
		$Models = new Models();
		$fieldArray = $Models->getDisplay($this->currentModel,2, $current,$action_mark);
		unset($Models);
		$this->assign('fieldArray',$fieldArray);
		return $current;
	}
	
	/**
	 * 模型编辑处理
	 * @param string|array $setting
	 * @param array $action  会员模型动作
	 * @return array
	 */
	protected function modelEditPost($setting = array(),$action='') {
		//获取模型字段
		$modelFields = F("models_fields_{$this->currentModel['id']}");
		$Models = new Models();
		//会员模型处理动作
		if ($action) $modelFields = $Models->getMemberActionFields($modelFields, $action);
		//数据验证
		$postData = $this->model->modelCheckData($modelFields);
		if (!$postData['vail_status']) $this->error($postData['vail_info']);
		//入库前数据处理
		$postData = $Models->before_storage($postData, $modelFields,$this->model);
		if (!$postData['vail_status']) $this->error($postData['vail_info']);
		//修改数据
		$postData = $this->model->modelEditData($this->currentModel,$postData,$setting);
		if ($postData['save_status']) $Models->after_storage($postData, $modelFields,$_POST['id']);
		return $postData['save_status'];
	}
	

	/**
	 * 验证验证码
	 * @param string $code
	 * @return boolean
	 */
	protected  function checkCode($code) {
		//其实这里应该不对就销毁的，但是为了方便，先用着吧
		return $_SESSION['code']===strtolower($code);
	}
	
	
	/**===============公共子类直接调用 =================****/
	
	/**
	 * 公共排序操作  后台
	 * @param string $model
	 */
	public function sort($model = '') {
		$model = empty($model) ? $this->model : $model;
		$sort = $this->checkData('sort');
		$sortArr = explode('|', $sort);
		foreach ($sortArr as $values) {
			$valueArr = explode('#', $values);
			$status = $model->where("id='{$valueArr[0]}'")->setField('sort',$valueArr[1]);
		}
		$this->ajaxReturn('','排序设置成功！',1);
	}
	
	
	/* 得到验证码图片 */
	public function get_code() {
		$width = isset($_GET['w']) ? intval($_GET['w']) : 70;
		$height = isset($_GET['h']) ? intval($_GET['h']) : 28;
		$codeLen = isset($_GET['l']) ? intval($_GET['l']) : 4;
		$fontSize = isset($_GET['fs']) ? intval($_GET['fs']) : 14;
		$Code = new Code();
		$Code->width = $width;
		$Code->height = $height;
		$Code->font_size = $fontSize;
		$Code->code_len = $codeLen;
		echo $Code->doimage();
		$_SESSION['code'] = strtolower($Code->get_code());
	}
	
	/**
	 * 一些自适应图像的自动缩放输出显示
	 * 主要用于前台
	 */
	public function crop() {
		static $path;
		$path = $path ? $path : str_replace(array('\\',__ROOT__),array('/',''),__PATH__);
		AutoImages::autoOutput($path.ltrim(base64_decode($_GET['f']),'/'), intval($_GET['w']), intval($_GET['h']));
	}
	
	/* Ajax验证，验证码 */
	public function ajax_check_code() {
		$param = strtolower($this->checkData('param'));
		$this->checkCode($param) ? $this->ajaxReturn('','','y') : $this->ajaxReturn('','验证码不正确！','n');
	}

}
?>