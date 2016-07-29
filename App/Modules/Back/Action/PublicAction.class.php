<?php
/**
 * 后台公共文件处理显示，无权限验证
 * 
 *
 */
class PublicAction extends GlobalAction {
	
	protected function _initialize() {
		parent::_initialize();
		if (!GBehavior::$session) $this->error('请先登录！',__ROOT__.rtrim(C('BACK_LOGIN_URL'),'/'));
	}
	
	/* ====================================Ajax Start========================================= */
	
	// 	验证后台管理员用户名是否重复
	public function check_back_username() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$id = $this->checkData('id','GET',false);
		$where = $id ? " AND id<>$id" : '';
		$username = $this->checkData('param');
		$status = M('Admin')->where("username='$username' $where")->find();
		$status ? $this->ajaxReturn('','用户名已存在！！','n') : $this->ajaxReturn('','','y') ;
	}
	
	// 	验证模型数据表是否重复
	public function check_models_table() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$model_type = $this->checkData('model_type','GET');
		$tableName = $this->checkData('param');
		if ($model_type == 'member') {
			$tableName = 'member_'.$tableName;
		}elseif ($model_type == 'form') {
			$tableName = 'form_'.$tableName;
		}
		$result = M('Models')->where("table_name='$tableName'")->find();
		$result ? $this->ajaxReturn('','该数据表已存在！！！','n') : $this->ajaxReturn('','','y');
	}
	
	// 	得到字段配置
	public function get_fields_setting() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$field_type = $this->checkData('field_type');
		$mid = $this->checkData('mid');
		$fieldTpl = str_replace(TMPL_PATH."Fields/$field_type/", '', glob(TMPL_PATH."Fields/$field_type/*.php"));
		ob_start();
		require REQUIRE_PATH."Fields/setting/$field_type.php";
		$data_setting = ob_get_clean();
		$settings = array('status'=>'success' ,'setting_data' => $data_setting,'mid'=>$mid);
		$this->ajaxReturn($settings);
	}
	
	// 	得到数据表字段
	public function get_field() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$table = $this->checkData('table');
		$ModelsFields = D('Back/ModelsFields');
		$fieldArray = $ModelsFields->getTableField(str_replace(DB_PREFIX, '', $table));
		$fieldArray ? $this->ajaxReturn($fieldArray,'','success') : $this->ajaxReturn($fieldArray,'','error');
	}
	
	// 	验证栏目标识
	public function check_navigate_mark() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$columnMark = $this->checkData('param');
		$id = $this->checkData('id','GET',false);
		$where = $id ? "AND id<>'$id'" : '';
		$Model = M('Navigate');
		$data = $Model->where("navigate_mark='$columnMark' $where")->find();
		$data ? $this->ajaxReturn('','导航标识(目录)已存在！！！','n') : $this->ajaxReturn('','','y');
	}
	
	//验证Admin是否重复登录
	public function check_admin_login() {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$cache = F('online/admin_login');
		$session_id = session_id();
		if ($cache[GBehavior::$session['id']]['session_id'] === $session_id) {
			$Admin = D('Back/Admin');
			$Admin->setOnline();
			$this->ajaxReturn('','','success');
		} else {
			$this->ajaxReturn('','','error');
		}
	}
	
	/* 得到模型表字段 */
	public function get_Model_Fields($id) {
		if (!IS_AJAX) $this->error(C('NOT_AJAX'));
		$data = M('ModelsFields')->where("mid=$id")->getField('field_name',true);
		$this->ajaxReturn($data);
	}
	/* ====================================Ajax End========================================= */
	
	/* ====================================Cache Clear ========================================= */
	/* 清理接口 */
	public function update_cache() {
		if (isset($_GET['type']) && !empty($_GET['type'])) {
			if ($_GET['type'] == 'all') {
				//Data目录
				$dataPath = RUNTIME_PATH.'Data/';
				Tool::removeDir(RUNTIME_PATH);
				$this->_memcache();
				$this->_navigate();
				$this->_modules();
				$this->_models();
				$this->_member_rule();
				$this->_admin_rule();
				$this->_admin_navigate();
				$this->_field();
				$this->_logs();
				$this->_compile();
				$this->_temp();
				// $this->_html();
			} else {
				$name = '_'.strtolower($this->checkData('type'));
				call_user_func_array(array($this,$name), array());
			}
			$this->success('成功更新缓存！',__ACTION__);
		} else {
			$this->display();
		}
	}
	
	//更新memcache
	public function _memcache() {
		if (class_exists('Memcache')) {
			$CacheMemcache = new CacheMemcache();
			$CacheMemcache->clear();
		}
	}
	
	//导航缓存
	public function _navigate() {
		$navigate = M('Navigate')->select();
		$newNavigate = array();
		foreach ($navigate as $values) {
			$values['setting'] = json_decode($values['setting'],true);
			$newNavigate[$values['id']] = $values;
		}
		F('navigate',$newNavigate);
	}
	//模块缓存
	public function _modules() {
		$data = M('Modules')->select();
		$modules = array();
		foreach ($data as $values) {
			$modules[$values['module_dir']] = $values;
		}
		F('modules',$modules);
	}
	//系统模型和模型字段
	public function _models() {
		//Models 模型
		$modelsData = M('Models')->where('m_status=1')->getField('id,model_name,table_name,model_type,setting,m_status');
		foreach ($modelsData as $key=>$values) {
			$values['setting'] = json_decode($values['setting'],true);
			F("models_$key",$values);
		}
		//Models Fields 模型字段
		$fields = M('ModelsFields')->where('field_status=1')->select();
		$newFields = array();
		foreach ($fields as $values) {
			$values['field_setting'] = json_decode($values['field_setting'],true);
			$newFields[$values['mid']][$values['field_name']] = $values;
		}
		foreach ($newFields as $key=>$fieldValues) {
			F('models_fields_'.$key,$fieldValues);
		}
		unset($fields,$modelsData,$newFields);
	}
	// 	会员组 更新 会员组权限规则
	public function _member_rule() {
		$MemberGroup = M('MemberGroup')->where('status=1')->field('id,rules')->select();
		$MemberRule = M('MemberRule');
		foreach ($MemberGroup as $groupValue) {
			$ruleCache = F("member_rule_{$groupValue['id']}");
			if ($ruleCache) continue;
			$ruleData = $MemberRule->where("id IN({$groupValue['rules']})  AND status=1")->order('sort DESC')->getField('id,name,title,show_status,node_type,condition,append,pid,sort');
			F("member_rule_{$groupValue['id']}",$ruleData);
		}
		unset($MemberGroup,$MemberRule);
	}
	// 	设置管理员组规则列表
	public function _admin_rule() {
		if ($_SESSION[C('SUPER_ADMIN')]) {
			$ruleData = M('AdminRule')->where("status=1")->order('sort DESC')->getField('id,name,title,show_status,node_type,condition,pid,sort');
			F('admin_super_rule',$ruleData);
			unset($ruleData);
		} else {
			$AdminGroup = M('AdminGroup')->where('status=1')->field('id,rules,is_super')->select();
			$AdminRule = M('AdminRule');
			foreach ($AdminGroup as $groupValue) {
				$ruleCache = F("admin_rule_{$groupValue['id']}");
				if ($ruleCache) continue;
				$ruleData = $AdminRule->where("id IN({$groupValue['rules']})  AND status=1")->order('sort DESC')->getField('id,name,title,show_status,node_type,condition,pid,sort');
				F("admin_rule_{$groupValue['id']}",$ruleData);
			}
			unset($AdminGroup,$AdminRule);
		}
	}
	
	// 	管理员组导航
	public function _admin_navigate() {
		$AdminGroup = M('AdminGroup')->where('status=1')->field('id,navi_rules')->select();
		foreach ($AdminGroup as $values) {
			$values['navi_rules'] = json_decode($values['navi_rules'],true);
			F("group_navigate_{$values['id']}",$values['navi_rules']);
		}
	}
	
	//Logs
	public function _logs() {
		Tool::removeDir(RUNTIME_PATH.'Logs');
	}
	
	/* 编辑缓存 */
	public function _compile() {
		Tool::removeDir(RUNTIME_PATH.'Cache');
	}
	
	/* 临时缓存 */
	public function _temp() {
		Tool::removeDir(RUNTIME_PATH.'Temp');
	}
	
	// 	静态文件
	public function _html() {
		Tool::removeDir(HTML_PATH);
	}
	// 	字段缓存
	public function _field() {
		Tool::removeDir(RUNTIME_PATH.'Data/_fields');
	}
	
	/* ====================================Cache Clear  End========================================= */
	/* ====================================hooray_start========================================= */
	
	/* hooray_ajax_desktop */
	public function hooray_ajax() {
		$ac = '_'.$this->checkData('ac');
		$AdminDesktop = M('AdminDesktop');
		$DESKTOP = $AdminDesktop->where("admin_id=".GBehavior::$session['id'])->find();
		$this->$ac($DESKTOP,$AdminDesktop);
	}
	
	/* //添加桌面图标 */
	private function _addMyApp($DESKTOP,$AdminDesktop) {
		$params = $this->checkData(array('desk','id','type'));
		$deskapp = empty($DESKTOP['desk'.$params['desk']]) ? $params['type'].'_'.$params['id'] : $DESKTOP['desk'.$params['desk']].','.$params['type'].'_'.$params['id'];
		$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField('desk'.$params['desk'],$deskapp);
	}
	
	/* 新建文件夹 */
	private function _addFolder() {
		$params = $this->checkData(array('icon','name'));
		$data = array(
			'icon'=>$params['icon'],
			'name'=>$params['name'],
			'member_id'=>GBehavior::$session['id'],
			'dt'=>date('Y-m-d H:i:s')
		);
		$insert_id = M('Folder')->data($data)->add();
		echo $insert_id;
	}
	
	/* 获得主题 */
	private function _getWallpaper($DESKTOP) {
		switch($DESKTOP['wallpaperstate']){
			case '1':
				$wallpaper = M('wallpaper')->where("tbid=".$DESKTOP['wallpaper_id'])->find();
				$wallpaper_array = array(
						$DESKTOP['wallpaperstate'],//壁纸类型
						$wallpaper['url'],
						$DESKTOP['wallpapertype'],//壁纸显示方式
						$wallpaper['width'],
						$wallpaper['height']
				);
				break;
			case '2':
				$wallpaper = M('pwallpaper')->where("tbid=".$DESKTOP['wallpaper_id'])->find();
				$wallpaper_array = array(
						$DESKTOP['wallpaperstate'],
						$wallpaper['url'],
						$DESKTOP['wallpapertype'],
						$wallpaper['width'],
						$wallpaper['height']
				);
				break;
			case '3':
				$wallpaper_array = array(
				$DESKTOP['wallpaperstate'],
				$DESKTOP['wallpaperwebsite']
				);
				break;
		}
		echo implode('<{|}>', $wallpaper_array);
	}
	/* 获得文件夹内图标 */
	private function _getMyFolderApp($DESKTOP) {
		$folder_id = $this->checkData('folderid');
		$Folder = M('Folder');
		$folder = $Folder->where("member_id=".GBehavior::$session['id']." AND tbid=$folder_id")->find();
		if (!empty($folder['content'])) {
			$folderArray = explode(',', $folder['content']);
			$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
			import('@.ORG.Com.PinYin');
			$Pinyin = new PinYin();
			$data = array();
			foreach ($folderArray as $v) {
				$v = explode('_', $v);
				switch($v[0]){
					case 'app':
					case 'widget':
						$tmp = $ruleData[$v[1]];
						$tmp['type'] = 'app';//=============先默认为app=====================
						$tmp['url'] = $tmp['name'];
						$tmp['name'] = $tmp['title'];
						$pinyinName = $Pinyin->getFirstPY($tmp['name']);
						$tmp['icon'] = WEB_URL.'/Public/hooray/img/sysicon/'.$pinyinName.'.png';
						break;
					case 'papp':
					case 'pwidget':
						$rs = $db->select(0, 1, 'tb_papp', '*', 'and tbid ='.$v[1]);
						$tmp['type'] = $rs['type'];
						$tmp['id'] = $rs['tbid'];
						$tmp['name'] = $rs['name'];
						$tmp['icon'] = $rs['icon'];
						break;
				}
				$data[] = $tmp;
				unset($tmp);
			}
			echo json_encode($data);
		}
	}
	
	/* 获得应用码头位置 */
	private function _getDockPos($DESKTOP) {
		echo $DESKTOP['dockpos'];
	}
	/* 获得图标排列方式 */
	private function _getAppXY($DESKTOP) {
		echo $DESKTOP['appxy'];
	}
	/* 获得桌面图标 */
	private function _getMyApp($DESKTOP) {
		//获取权限
		$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
		//得到有用的第三级树
		$idArray = array();
		foreach ($ruleData as $values) {
			if ($values['pid'] == 0) $idArray[] = $values['id'];
		}
		$idArray2 = array();
		foreach ($ruleData as $values) {
			if (in_array($values['pid'], $idArray,true)) {
				$idArray2[] = $values['id'];
			}
		}
		$newRuleData = array();
		import('@.ORG.Com.PinYin');
		$Pinyin = new PinYin();
		foreach ($ruleData as $values) {
			if (in_array($values['pid'], $idArray2,true) && $values['node_type'] == 1) {
				$values['type'] = 'app';//=============先默认为app=====================
				$values['url'] = $values['name'];
				$values['name'] = $values['title'];
				$pinyinName = $Pinyin->getFirstPY($values['name']);
				$values['icon'] = WEB_URL.'/Public/hooray/img/sysicon/'.$pinyinName.'.png';
				$newRuleData[$values['id']] = $values;
			}
		}
		$desktop = array();
		//应用码头
		if ($DESKTOP['dock']) {
			$dock_list = explode(',', $DESKTOP['dock']);
			$dock_idArray = array();
			$Folder = M('Folder');
			foreach($dock_list as $v){
				$tmp = array();
				$v = explode('_', $v);
				switch($v[0]){
					//系统应用和小工具
					case 'app':
					case 'widget':
						foreach ($newRuleData as $ruleValues) {
							if ($v[1] == $ruleValues['id']) {
								$tmp = $ruleValues;
								$dock_idArray[] = $ruleValues['id'];
								break;
							}
						}
						break;
					//自定义的应用和小工具
					case 'papp':
					case 'pwidget':
						$rs = $db->select(0, 1, 'tb_papp', '*', 'and tbid = '.$v[1].' and member_id = '.$_SESSION['member']['id']);
						$tmp['type'] = $rs['type'];
						$tmp['id'] = $rs['tbid'];
						$tmp['name'] = $rs['name'];
						$tmp['icon'] = $rs['icon'];
						break;
					//目录
					case 'folder':
						$tmp = $Folder->where("tbid={$v[1]} AND member_id=".GBehavior::$session['id'])->find();
						$tmp['type'] = 'folder';
						$tmp['id'] = $tmp['tbid'];
						break;
				}
				$data[] = $tmp;
			}
			$desktop['dock'] = $data;
			unset($data,$dock_list);
		} else {
			$desktop['dock'] = array();
		}
		//桌面图标设置
		for($i = 1; $i<=5; $i++){
			$tmp = array();
			if($DESKTOP['desk'.$i]){
				$Folder = M('Folder');
				$deskappid_list = explode(',', $DESKTOP['desk'.$i]);
				foreach($deskappid_list as $v){
					$v = explode('_', $v);
					switch($v[0]){
						//系统应用和小工具
						case 'app':
						case 'widget':
							$tmp = $newRuleData[$v[1]];
							break;
						//自定义的应用和小工具
						case 'papp':
						case 'pwidget':
							// 										$rs = $db->select(0, 1, 'tb_papp', '*', 'and tbid = '.$v[1].' and member_id = '.$_SESSION['member']['id']);
							$tmp['type'] = $rs['type'];
							$tmp['id'] = $rs['tbid'];
							$tmp['name'] = $rs['name'];
							$tmp['icon'] = $rs['icon'];
							break;
							//目录
						case 'folder':
							$rs = $Folder->where("member_id=".GBehavior::$session['id']." AND tbid={$v[1]}")->find();
							$tmp = array();
							$tmp['type'] = 'folder';
							$tmp['id'] = $rs['tbid'];
							$tmp['name'] = $rs['name'];
							$tmp['icon'] = $rs['icon'];
							break;
					}
					if ($tmp) {
						$data[] = $tmp;
						unset($tmp);
					}
				}
				$desktop['desk'.$i] = $data;
				unset($data,$deskappid_list);
			}
		}
		/* 先默认为一个桌面，此段代码暂用 */
		/*
		$newNewRuleData = array();
		foreach ($newRuleData as $_values) {
			$newNewRuleData[] = $_values;
		}
		$desktop['desk1'] = $newNewRuleData;
		*/
		/* 此段代码暂用 */
		echo json_encode($desktop);
	}
	/* 更新应用码头位置 */
	private function _setDockPos($DESKTOP,$AdminDesktop) {
		$dock = $this->checkData('dock');
		$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField('dockpos', $dock);
	}
	/* 更新图标排列方式 */
	private function _setAppXY($DESKTOP,$AdminDesktop) {
		$appxy = $this->checkData('appxy');
		$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField('appxy', $appxy);
	}
	
	/* 更新应用码头 */
	private function _updateMyApp($DESKTOP,$AdminDesktop) {
		$params = $this->checkData(array('desk','from','id','movetype','to','type','otherdesk'),'',false);
		$params['to'] = intval($params['to']);
		$setDesktop = array();
		switch($params['movetype']){
			case 'dock-folder':
				$Folder = M('Folder');
				$rs2 = $Folder->where("member_id=".GBehavior::$session['id']." AND tbid={$params['to']}")->find();
				$dock_arr = explode(',', $DESKTOP['dock']);
				$key = array_search($params['type'].'_'.$params['id'], $dock_arr);
				unset($dock_arr[$key]);
				$rs2['content'] = empty($rs2['content']) ? $params['type'].'_'.$params['id'] : $rs2['content'].','.$params['type'].'_'.$params['id'];
				$AdminDesktop->where('admin_id='.GBehavior::$session['id'])->setField('dock',implode(',', $dock_arr));
				$Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['to']}")->setField('content',$rs2['content']);
				break;
			case 'dock-dock':
				$dock_arr = explode(',', $DESKTOP['dock']);
				//判断传入的应用id和数据库里的id是否吻合
				if($dock_arr[$params['from']] == $params['type'].'_'.$params['id']){
					if($params['from'] > $params['to']){
						for($i = $params['from']; $i > $params['to']; $i--){
							$dock_arr[$i] = $dock_arr[$i-1];
						}
						$dock_arr[$params['to']] = $params['type'].'_'.$params['id'];
					}else if($params['to'] > $params['from']){
						for($i = $params['from']; $i < $params['to']; $i++){
							$dock_arr[$i] = $dock_arr[$i+1];
						}
						$dock_arr[$params['to']] = $params['type'].'_'.$params['id'];
					}
					$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField('dock',implode(',', $dock_arr));
				}
				break;
			case 'dock-desk':
				$dock_arr = explode(',', $DESKTOP['dock']);
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				unset($dock_arr[$params['from']]);
				if(empty($desk_arr[0])){
					$desk_arr[0] = $params['type'].'_'.$params['id'];
				}else{
					array_splice($desk_arr, $params['to'], 0, $params['type'].'_'.$params['id']);
				}
				$setDesktop['desk'.$params['desk']] = implode(',', $desk_arr);
				$setDesktop['dock'] = implode(',', $dock_arr);
				$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField($setDesktop);
				break;
			case 'desk-folder':
				$Folder = M('Folder');
				$rs2 = $Folder->where("member_id=".GBehavior::$session['id']." AND tbid={$params['to']}")->find();
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				$key = array_search($params['type'].'_'.$params['id'], $desk_arr);
				unset($desk_arr[$key]);
				$rs2['content'] = empty($rs2['content']) ? $params['type'].'_'.$params['id'] : $rs2['content'].','.$params['type'].'_'.$params['id'];
				$AdminDesktop->where('admin_id='.GBehavior::$session['id'])->setField('desk'.$params['desk'],implode(',', $desk_arr));
				$Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['to']}")->setField('content',$rs2['content']);
				break;
			case 'desk-dock'://桌面到码头
				$dock_arr = explode(',', $DESKTOP['dock']);
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				unset($desk_arr[$params['from']]);
				if(empty($dock_arr[0])){
					$dock_arr[0] = $params['type'].'_'.$params['id'];
				}else{
					array_splice($dock_arr, $params['to'], 0, $params['type'].'_'.$params['id']);
				}
				if(count($dock_arr) > 7){
					$desk_arr[] = $dock_arr[7];
					unset($dock_arr[7]);
				}
				$setDesktop['dock'] = implode(',', $dock_arr);
				$setDesktop['desk'.$params['desk']] = implode(',', $desk_arr);
				$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField($setDesktop);
				break;
			case 'desk-desk':
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				//判断传入的应用id和数据库里的id是否吻合
				if($desk_arr[$params['from']] == $params['type'].'_'.$params['id']){
					if($params['from'] > $params['to']){
						for($i = $params['from']; $i > $params['to']; $i--){
							$desk_arr[$i] = $desk_arr[$i-1];
						}
						$desk_arr[$params['to']] = $params['type'].'_'.$params['id'];
					}else if($params['to'] > $params['from']){
						for($i = $params['from']; $i < $params['to']; $i++){
							$desk_arr[$i] = $desk_arr[$i+1];
						}
						$desk_arr[$params['to']] = $params['type'].'_'.$params['id'];
					}
					$AdminDesktop->where('admin_id='.GBehavior::$session['id'])->setField('desk'.$params['desk'], implode(',',$desk_arr));
				}
				break;
			case 'desk-otherdesk':
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				$otherdesk_arr = explode(',', $DESKTOP['desk'.$params['otherdesk']]);
				unset($desk_arr[$params['from']]);
				empty($otherdesk_arr[0]) ? $otherdesk_arr[0] = $params['type'].'_'.$params['id'] : array_splice($otherdesk_arr, $params['to'], 0, $params['type'].'_'.$params['id']);
				$desktop = array();
				$desktop['desk'.$params['desk']] = implode(',', $desk_arr);
				$desktop['desk'.$params['otherdesk']] = implode(',', $otherdesk_arr);
				$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField($desktop);
				break;
			case 'folder-folder':
				$Folder = M('Folder');
				$rs1 = $Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['from']}")->find();
				$rs2 = $Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['to']}")->find();
				$folder1appid_arr = explode(',', $rs1['content']);
				$folder2appid_arr = explode(',', $rs2['content']);
				$key = array_search($params['type'].'_'.$params['id'], $folder1appid_arr);
				unset($folder1appid_arr[$key]);
				$rs2['content'] = empty($rs2['content']) ? $params['type'].'_'.$params['id'] : $rs2['content'].','.$params['type'].'_'.$params['id'];
				$Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['from']}")->setField('content',implode(',', $folder1appid_arr));
				$Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['to']}")->setField('content',$rs2['content']);
				break;
			case 'folder-dock':
				$Folder = M('Folder');
				$rs2 = $Folder->where("tbid={$params['from']} AND member_id=".GBehavior::$session['id'])->find();
				$dock_arr = explode(',', $DESKTOP['dock']);
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				$folderappid_arr = explode(',', $rs2['content']);
				$key = array_search($params['type'].'_'.$params['id'], $folderappid_arr);
				unset($folderappid_arr[$key]);
				if($dock_arr[0] == ''){
					$dock_arr[0] = $params['type'].'_'.$params['id'];
				}else{
					array_splice($dock_arr, $params['to'], 0, $params['type'].'_'.$params['id']);
				}
				if(count($dock_arr) > 7){
					$desk_arr[] = $dock_arr[7];
					unset($dock_arr[7]);
				}
				$adminDesk = array();
				$adminDesk['dock'] = implode(',', $dock_arr);
				$adminDesk['desk'.$params['desk']] = implode(',', $desk_arr);
				$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField($adminDesk);
				$Folder->where("tbid={$params['from']} AND member_id=".GBehavior::$session['id'])->setField('content',implode(',', $folderappid_arr));
				break;
			case 'folder-desk':
				$Folder = M('Folder');
				$rs2 = $Folder->where("member_id=".GBehavior::$session['id']." AND tbid={$params['from']}")->find();
				$desk_arr = explode(',', $DESKTOP['desk'.$params['desk']]);
				$folderappid_arr = explode(',', $rs2['content']);
				$key = array_search($params['type'].'_'.$params['id'], $folderappid_arr);
				unset($folderappid_arr[$key]);
				if($desk_arr[0] == ''){
					$desk_arr[0] = $params['type'].'_'.$params['id'];
				}else{
					array_splice($desk_arr, $params['to'], 0, $params['type'].'_'.$params['id']);
				}
				$AdminDesktop->where('admin_id='.GBehavior::$session['id'])->setField('desk'.$params['desk'],implode(',', $desk_arr));
				$Folder->where('member_id='.GBehavior::$session['id']." AND tbid={$params['from']}")->setField('content',implode(',', $folderappid_arr));
				break;
		}
	}
		/* 更新桌面图标 */
		private function _moveMyApp($DESKTOP,$AdminDesktop) {
			$params = $this->checkData(array('id','todesk','type'));
			$flag = false;
			$setMove = array();
			//应用码头
			if(!empty($DESKTOP['dock'])){
				$dockapp = explode(',', $DESKTOP['dock']);
				foreach($dockapp as $k => $v){
					if($v == $params['type'].'_'.$params['id']){
						$flag = true;
						unset($dockapp[$k]);
						break;
					}
				}
				$setMove['dock'] = implode(',', $dockapp);
			}
			//桌面
			for($i=1; $i<=5; $i++){
				if($DESKTOP['desk'.$i] != ''){
					$deskapp = explode(',', $DESKTOP['desk'.$i]);
					foreach($deskapp as $k => $v){
						if($v == $params['type'].'_'.$params['id']){
							$flag = true;
							unset($deskapp[$k]);
							break;
						}
					}
					$setMove['desk'.$i] = implode(',', $deskapp);
				}
			}
			if($flag){
				$AdminDesktop->where('admin_id='.GBehavior::$session['id'])->setField($setMove);
			}else{
				//目录操作
				$Folder = M('Folder');
				$folderData = $Folder->where("member_id=".GBehavior::$session['id']." AND content!=''")->select();
				if($folderData){
					foreach($folderData as $v){
						$flag = false;
						$folderapp = explode(',', $v['content']);
						foreach($folderapp as $key => $value){
							if($value == $params['type'].'_'.$params['id']){
								$flag = true;
								unset($folderapp[$key]);
								break;
							}
						}
						$folderappid = implode(',', $folderapp);
						if($flag){
							$Folder->where("tbid={$v['tbid']} AND member_id=".GBehavior::$session['id'])->setField('content',$folderappid);
						}
					}
				}
			}
			$DESKTOP['desk'.$params['todesk']] = empty($DESKTOP['desk'.$params['todesk']]) ? $params['type'].'_'.$params['id'] : $DESKTOP['desk'.$params['todesk']].','.$params['type'].'_'.$params['id'];
			$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField('desk'.$params['todesk'], $DESKTOP['desk'.$params['todesk']]);
		}
		
		/*根据id获取图标 */
		private function _getMyAppById($DESKTOP,$AdminDesktop) {
			$params = $this->checkData(array('type','id'));
			$AdminRule = M('AdminRule');
			switch($params['type']){
				case 'app':
				case 'widget':
					//获取权限
					$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
					foreach ($ruleData as $values) {
						if ($values['id'] == $params['id']) {
							$app = $values;
							break;
						}
					}
					/* 重置数据 */
					$app['type'] = 'app';//=============先默认为app=====================
					$app['url'] = __APP__.'/'.ltrim($app['name'],APP_NAME);
					$app['name'] = $app['title'];
					import('@.ORG.Com.PinYin');
					$Pinyin = new PinYin();
					$pinyinName = $Pinyin->getFirstPY($app['name']);
					$app['icon'] = WEB_URL.'/Public/hooray/img/sysicon/'.$pinyinName.'.png';
					$app['width'] = $DESKTOP['width'];;
					$app['height'] = $DESKTOP['height'];;
					$app['isresize'] = $DESKTOP['isresize'];
					$app['issetbar'] = $DESKTOP['issetbar'];
					//$app['isflash'] = $DESKTOP['isflash'];
					break;
				case 'papp':
				case 'pwidget':
					$rs = $db->select(0, 1, 'tb_papp', '*', 'and tbid = '.$id.' and member_id = '.$_SESSION['member']['id']);
					if($rs != NULL){
						$app['type'] = $rs['type'];
						$app['id'] = $rs['tbid'];
						$app['name'] = $rs['name'];
						$app['icon'] = $rs['icon'];
						$app['url'] = $rs['url'];
						$app['width'] = $rs['width'];
						$app['height'] = $rs['height'];
						$app['isresize'] = $rs['isresize'];
						$app['issetbar'] = 0;
						$app['isflash'] = 1;
					}
					break;
				case 'folder':
					$rs = M('Folder')->where('member_id='.GBehavior::$session['id']." AND tbid={$params['id']}")->find();
					$app['type'] = 'folder';
					$app['id'] = $rs['tbid'];
					$app['name'] = $rs['name'];
					$app['icon'] = $rs['icon'];
					$app['width'] = '650';
					$app['height'] = '400';
					break;
			}
			echo json_encode($app);
		}
		
		/* //更新主题 */
		private function _setWallpaper() {
			$params = $this->checkData(array('wpstate','wptype','wp'),'',false);
			$postData = array();
			$postData['wallpaperstate'] = $params['wpstate'];
			$postData['wallpapertype'] = $params['wptype'];
			if (!empty($params['wp'])) {
				switch($params['wpstate']){
					case '1':
					case '2':
						$postData['wallpaper_id'] = $params['wp'];
						break;
					case '3':
						$postData['wallpaperwebsite'] = $params['wp'];
						break;
				}
			}
			$AdminDesktop = M('AdminDesktop');
			$AdminDesktop->where("admin_id=".GBehavior::$session['id'])->setField($postData);
		}
		
		/* 获得窗口皮肤 */
		private function _getSkin() {
			$AdminDesktop = M('AdminDesktop');
			$data = $AdminDesktop->where("admin_id=".GBehavior::$session['id'])->find();
			echo $data['skin'];
		}
			
		
		/* 主题设置 */
		public function wallpaper_set() {
			$Wallpaper = M('Wallpaper');
			$data = $Wallpaper->order('tbid ASC')->select();
			$this->assign('wallpaperList', $data);
			$AdminDesktop = M('AdminDesktop');
			$DESKTOP = $AdminDesktop->where("admin_id=".GBehavior::$session['id'])->find();
			$this->assign('wallpaperType', $DESKTOP['wallpapertype']);
			/*
			$this->assign('wallpaperType', $DESKTOP['wallpapertype']);
			$this->assign('wallpaperWebsite', $DESKTOP['wallpaperwebsite']);
			/*
			$rs = $db->select(0, 0, 'tb_pwallpaper');
			foreach($rs as &$value){
				$value['surl'] = getSimgSrc($value['url']);
			}
			$this->assign('wallpaper', $DESKTOP);
			*/
			$this->display();
		}
		
		/* 桌面设置 */
		public function desktop() {
			$AdminDesktop = M('AdminDesktop');
			$data = $AdminDesktop->where(array('admin_id'=>GBehavior::$session['id']))->find();
			$this->assign('dockpos',$data['dockpos']);
			$this->display();
		}
		
		/* 桌面权限图标设置，注：如果权限被更新，则这些图标权限需要手动更新 */
		public function desktop_rule() {
			$AdminDesktop = M('AdminDesktop');
			if (IS_POST) {
				$postData = Tool::filterData($_POST);
				if (empty($postData['desk_type'])) $this->error('请选择桌面！');
				if (empty($postData['desk_rule_options'])) {
					$postData['desk_rule_options'];
				} else {
					foreach ($postData['desk_rule_options'] as &$val) {
						$val = "app_{$val}";
					}
					$postData['desk_rule_options'] = implode(',', $postData['desk_rule_options']);
				}
				$status = $AdminDesktop->where(array('admin_id'=>GBehavior::$session['id']))->setField('desk'.$postData['desk_type'], $postData['desk_rule_options']);
				$this->editPublicMsg($status,'',U('desktop_rule'));
			} elseif (IS_AJAX) {
				$desk_type = $this->checkData('desk_type');
				$data = $AdminDesktop->where(array('admin_id'=>GBehavior::$session['id']))->getField('desk'.$desk_type);
				$data = explode(',', $data);
				$ruleArray = array_map(create_function('$string_value', '$temp_value = explode("_",$string_value);return $temp_value[1];'), $data);;
				$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
				$newRuleData = array();
				foreach ($ruleData as $values) {
					if (in_array($values['id'],$ruleArray)) $newRuleData[] = $values;
				}
				$this->ajaxReturn($newRuleData);
			} else {
				$data = $AdminDesktop->where(array('admin_id'=>GBehavior::$session['id']))->find();
				$ruleString = '';
				for ($i=1;$i<=5;$i++) {
					$ruleString .= empty($data['desk'.$i]) ? '' : $data['desk'.$i].',';
				}
				$ruleArray = explode(',', $ruleString);
				$ruleArray = array_map(create_function('$string_value', '$temp_value = explode("_",$string_value);return $temp_value[1];'), $ruleArray);
				$ruleData = $_SESSION[C('SUPER_ADMIN')] ? $ruleData = F('admin_super_rule') : D('AdminGroupAccess')->findCurrentAdminRule();
				$newRuleData = array();
				foreach ($ruleData as $values) {
					if ($values['node_type'] == 1 && !in_array($values['id'],$ruleArray)) $newRuleData[$values['id']] = $values;
				}
				$this->assign('data',$newRuleData);
				$this->display();
			}
		}
		
		public function update_desktop() {
			
		}
		
	/* ====================================hooray_End========================================= */
}
?>