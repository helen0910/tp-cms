<?php
/**
 * 模型处理类
 * 
 * 常用模型方法  index -->列表  add -->添加  edit-->编辑  delete-->删除
 */
class ModelsAction extends GlobalAction {
	
	protected function _initialize(){
		parent::_initialize();
		parent::BackEntranceInit();
		$this->model = D('Models');
	}
	
	/* 模型列表 */
	public function index() {
		$modelData = $this->model->order('id DESC')->select();
		$this->assign('modelData',$modelData);
		$this->display();
	}
	
	/* 内容模型添加 */
	public function add_content() {
		if (IS_POST) {
			$this->add_post('content');
		} else {
			$contentTplPath = TMPL_PATH.'/Content/';
			$backList = str_replace($contentTplPath, '', glob($contentTplPath.'list*.php'));
			$backContent = str_replace($contentTplPath, '', glob($contentTplPath.'content*.php'));
			$memberContentTplPath = TEMPLATE_PATH.C('DEFAULT_SKIN').'/Member/Content/';
			$memberList = str_replace($memberContentTplPath, '', glob($memberContentTplPath.'list*.php'));
			$memberContent = str_replace($memberContentTplPath, '', glob($memberContentTplPath.'content*.php'));
			$this->assign('backList',$backList);
			$this->assign('backContent',$backContent);
			$this->assign('memberList',$memberList);
			$this->assign('memberContent',$memberContent);
			$this->display();
		}
	}
	
	/* 会员模型添加 */
	public function add_member() {
		IS_POST ? $this->add_post('member') : $this->display();
	}
	
	/* 表单模型添加 */
	public function add_form() {
		if (IS_POST) {
			$this->add_post('form') ;
		} else {
			$contentTplPath = TMPL_PATH.'/Form/';
			$listTpl = str_replace($contentTplPath, '', glob($contentTplPath.'list*.php'));
			$contentTplPath = TEMPLATE_PATH.C('DEFAULT_SKIN').'/Form/';
			$contentTpl = str_replace($contentTplPath, '', glob($contentTplPath.'content*.php'));
			$this->assign('listTpl',$listTpl);
			$this->assign('contentTpl',$contentTpl);
			$this->display();
		}
	}
	
	/**
	 * 模型添加核心处理
	 * @param string $model_type
	 */
	private function add_post($model_type) {
		/* 基本数据验证 */
		$postData = $this->model->checkData();
		if (!$postData['vail_status']) $this->error($postData['vail_info']);
		/* 数据表名处理 */
		$postData['model_type'] = $model_type;
		if ($postData['model_type'] == 'member') {
			$postData['table_name'] = $postData['setting']['is_main']==1 ? $postData['table_name'] : 'member_'.$postData['table_name'];			
		}elseif ($postData['model_type'] == 'form') {
			$postData['table_name'] = 'form_'.$postData['table_name'];
		}
		/* 判断添加的数据表是否存在 */
		if ($this->model->where("table_name='{$postData['table_name']}'")->find()) $this->error('该数据表已经存在！！！');
		/* 数据插入 */
		$insertId = $this->model->addData($postData);
		if ($insertId) {
// 			添加数据表
			$status = $this->model->addModelTable($postData,$insertId);
			if ($status) {
// 				//添加模型字段
				$status = $this->model->addModelField($insertId,$postData);
				if ($status) {
					//更新模型字段缓存
					R('Back/Public/_models');
				}
				$this->addPublicMsg($status);
			}else {
				//删除此条数据模型
				$this->model->where("id=$insertId")->delete();
				$this->model->dropTable($postData['table_name']);
				$this->model->dropTable("{$postData['table_name']}_data");
				$this->addPublicMsg($status);
			}
		}else {
			$this->addPublicMsg($insertId);
		}
	}
	
	/* 模型编辑 */
	public function edit() {
		if (IS_POST) {
			$this->matchToken($_POST['id'].$_POST['model_type'].$_POST['table_name']);
	 		$postData = $this->model->checkData();
	 		if (!$postData['vail_status']) $this->error($postData['vail_info']);
// 	 		判断添加的数据表是否存在
			$isChecked = $this->model->where("table_name='{$postData['table_name']}' AND id<>{$postData['id']}")->find();
			if ($isChecked) $this->error('该数据表已经存在！！！');
// 			修改数据
			$status = $this->model->editData($postData);
			if ($status !== false) {
				//更新模型字段缓存
				R('Back/Public/_models');
			}
			$this->editPublicMsg($status);
		}else {
			$id = $this->checkData('id');
			$current = $this->findOneData($id);
			$current ? $this->assign('current',$current) : $this->error(C('FIND_ERROR'));
			$this->assign('setting',json_decode($current['setting'],true));
			$this->encryptToken($id.$current['model_type'].$current['table_name']);
			switch ($current['model_type']) {
				case 'content':
					$contentTplPath = TMPL_PATH.'/Content/';
					$backList = str_replace($contentTplPath, '', glob($contentTplPath.'list*.php'));
					$backContent = str_replace($contentTplPath, '', glob($contentTplPath.'content*.php'));
					$memberContentTplPath = TEMPLATE_PATH.C('DEFAULT_SKIN').'/Member/Content/';
					$memberList = str_replace($memberContentTplPath, '', glob($memberContentTplPath.'list*.php'));
					$memberContent = str_replace($memberContentTplPath, '', glob($memberContentTplPath.'content*.php'));
					$this->assign('backList',$backList);
					$this->assign('backContent',$backContent);
					$this->assign('memberList',$memberList);
					$this->assign('memberContent',$memberContent);
					$tpl = 'edit_content';
					break;
				case 'member':
					$tpl = 'edit_member';
					break;
				case 'form';
					$contentTplPath = TMPL_PATH.'/Form/';
					$listTpl = str_replace($contentTplPath, '', glob($contentTplPath.'list*.php'));
					$contentTplPath = TEMPLATE_PATH.C('DEFAULT_SKIN').'/Form/';
					$contentTpl = str_replace($contentTplPath, '', glob($contentTplPath.'content*.php'));
					$this->assign('listTpl',$listTpl);
					$this->assign('contentTpl',$contentTpl);
					$tpl = 'edit_form';
					break;	
			}
			$this->display($tpl);
		}
    }

	/* 模型删除 */
	public function delete() {
 		$id = $this->checkData('id');
 		$modelData = $this->findOneData($id);
 		if (!$modelData) $this->error(C('FIND_ERROR'));
 		$status = $this->model->where("id='$id'")->delete();
 		if ($status) {
 			//删除数据表
 			$this->model->dropTable($modelData['table_name']);
 			if ($modelData['model_type'] == 'content') $this->model->dropTable($modelData['table_name'].'_data');
			//删除modelField字段
			$ModelField = D('ModelsFields');
			$ModelField->where("mid=$id")->delete();
 		}
 		if ($status) {
 			//更新模型字段缓存
 			R('Back/Public/_models');
 		}
 		$this->deletePublicMsg($status);
    }

}
?>