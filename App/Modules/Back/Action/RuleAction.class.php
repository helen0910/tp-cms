<?php
/**
 * 后台权限节点规则控制器
 * 
 * 常用模型方法  index -->列表  add -->添加  edit-->编辑  delete-->删除
 */
class RuleAction extends GlobalAction {
	
	protected function _initialize() {
		parent::_initialize();
		parent::BackEntranceInit();
		$this->model = D('AdminRule');
	}
	
	public function index() {
		$nodeData = $this->model->order('sort DESC')->select();
		$Tree = new Tree();
		$Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($nodeData as &$values) {
			$values['str_manage'] = '<a href="'.U('add',array('id'=>$values['id'])).'" class="operate">添加子菜单</a>&nbsp;&nbsp;<a class="operate" href="'.U('edit',array('id'=>$values['id'])).'">编辑</a>&nbsp;&nbsp;<a class="operate" onclick="(_confirm(\'是否确定要删除？\',function(){location.href=\''.U('delete',array('id'=>$values['id'])).'\'}))" href="###">删除</a>';
			$values['status'] = $values['status']==1 ? "正常" : "<span class='setRed'>禁用</span>";
			$values['show_status'] = $values['show_status']==1 ? "显示" : "<span class='setRed'>隐藏</span>";
		}
		$Tree->init($nodeData);
		$str = "<tr>
	    <td><input type='checkbox' name='id_all[]' value='\$id' /></td>
	    <td>\$id</td>
		<td><input name='sort[\$id]' class='text' tabindex='10' type='text' size='3' value='\$sort' class='input'></td>
		<td style='text-align:left;padding-left:10px;'>\$spacer\$title</td>
                    <td align='center'>\$status</td>
                    <td align='center'>\$show_status</td>
		<td>\$str_manage</td>
		</tr>";
		$nodeString = $Tree->get_tree(0, $str);
		$this->assign('nodeString',$nodeString);
		$this->display();
	}
	
	/* 添加数据 */
	public function add() {
		if (IS_POST) {
			$postData = $this->model->checkData();
			if (!$postData['vail_status']) $this->error($postData['vail_info']);
			$this->addPublicMsg($this->model->addData($postData));
		}else {
			$this->getNodeTree($this->checkData('id','',false));
			$this->display();
		}
	}
	
	/* 编辑数据 */
	public function edit() {
		if (IS_POST) {
			$this->matchToken();
			$postData = $this->model->checkData();
			if (!$postData['vail_status']) $this->error($postData['vail_info']);
			// 		单独验证密码
			if ($postData['password']) {
				$passwordLen = strlen($postData['password']);
				if ($passwordLen < 6 || $passwordLen > 16) $this->error('密码格式不正确！！！');
			}else {
				unset($postData['password']);
			}
			$this->editPublicMsg($this->model->editData($postData));
		}else {
			$id = $this->checkData('id');
			$current = $this->findOneData($id);
			$current ? $this->assign('current',$current) : $this->error(C('FIND_ERROR'));
			$this->encryptToken($id);//加密值
			$this->assign('node',explode('/', $current['name']));
			$this->getNodeTree($current['pid']);
			$this->display();
		}
	}
	

	
	/* 删除数据 */
	public function delete() {
		$this->deletePublicMsg($this->model->deleteData($this->checkData('id')));
	}
	
	/* 得到节点树 */
	private function getNodeTree($pid = '') {
		$nodeData = $this->model->order('sort DESC')->select();
		if ($nodeData) {
			$Tree = new Tree();
			foreach ($nodeData as &$values) {
				$values['selected'] = $values['id'] == $pid ? 'selected="selected"' : '';
			}
			$str = "<option value='\$id' \$selected>\$spacer \$title</option>";
			$Tree->init($nodeData);
			$parent_node = $Tree->get_tree(0, $str);
			$this->assign('parent_node',$parent_node);
		}
	}

	
}
?>
