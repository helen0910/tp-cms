<?php

/**
 * 管理员组控制器
 * 
 *
 */
class AdmingroupAction extends GlobalAction {

    protected function _initialize() {
        parent::_initialize();
        parent::BackEntranceInit();
        $this->model = D('AdminGroup');
    }

    /* 组列表 */

    public function index() {
        $adminGroup = $this->model->order('id DESC')->select();
        $this->assign('adminGroup', $adminGroup);
        $this->display();
    }

    /* 添加组 */

    public function add() {
        if (IS_POST) {
            $postData = $this->model->checkData();
            if (!$postData['vail_status'])
                $this->error($postData['vail_info']);
            $this->addPublicMsg($this->model->addData($postData));
        } else {
            $this->display();
        }
    }

    /* 编辑组 */

    public function edit() {
        if (IS_POST) {
            $this->matchToken();
            $postData = $this->model->checkData();
            if (!$postData['vail_status'])
                $this->error($postData['vail_info']);
            $this->addPublicMsg($this->model->editData($postData));
        } else {
            $id = $this->checkData('id');
            $current = $this->findOneData($id);
            $current ? $this->assign('current', $current) : $this->error(C('FIND_ERROR'));
            $this->encryptToken($id);
            $this->display();
        }
    }

    /* 设置权限规则 */

    public function set_rule() {
        if (IS_POST) {
            $this->matchToken();
            $status = $this->model->setRule();
            if ($status !== false) {
                //更新缓存
                R('Back/Public/_admin_rule');
            }
            $this->editPublicMsg($status);
        } else {
            $id = $this->checkData('id');
            $current = $this->findOneData($id);
            $this->encryptToken($id);
            $current ? $this->assign('current', $current) : $this->error(C('FIND_ERROR'));
            if ($current['is_super'] == 1)
                $this->error('超级管理员权限不可修改！！！', U('/Back/Admin_group/index'));
            //分配已有权限规则
            $this->assign('power', explode(',', $current['rules']));
            $this->assign('ruleData', $this->model->table(DB_PREFIX . 'admin_rule')->where('status=1')->order('sort DESC')->field('id,pid,title')->select());
            $this->display();
        }
    }

    /* 设置导航权限 */

    public function set_navigate() {
        if (IS_POST) {
            $this->matchToken();
            $navi_rules = json_encode($_POST['navi_rules']);
            $status = $this->model->where("id={$_POST['id']}")->setField('navi_rules', $navi_rules);
            $status = $status !== false ? true : false;
            if ($status) {
                //更新缓存
                R('Back/Public/_admin_navigate');
            }
            $this->editPublicMsg($status);
        } else {
            $id = $this->checkData('id');
            $current = $this->findOneData($id);
            $current ? $this->assign('current', $current) : $this->error(C('FIND_ERROR'));
            $this->encryptToken($id);
            $current['navi_rules'] = json_decode($current['navi_rules'], true);

            $navigate = F('navigate');
            foreach ($navigate as &$values) {
                $values['display_checked'] = $current['navi_rules'][$values['id']]['display'] == 1 ? 'checked="checked"' : '';
                $values['add_checked'] = $current['navi_rules'][$values['id']]['add'] == 1 ? 'checked="checked"' : '';
                $values['edit_checked'] = $current['navi_rules'][$values['id']]['edit'] == 1 ? 'checked="checked"' : '';
                $values['delete_checked'] = $current['navi_rules'][$values['id']]['delete'] == 1 ? 'checked="checked"' : '';
                $values['sort_checked'] = $current['navi_rules'][$values['id']]['sort'] == 1 ? 'checked="checked"' : '';
            }

            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $str = "<tr>
			<td style='padding-left:0px;text-align:center;'><input type='checkbox' name='id_all[]' value='\$id' /></td>
			<td style='padding-left:0px;text-align:left;text-indent:10px;'>\$spacer\$navigate_name</td>
			<td style='padding-left:0px;'><label><input type='checkbox' name='navi_rules[\$id][display]' value='1' \$display_checked />查看</label></td>
			<td style='padding-left:0px;'><label><input type='checkbox' name='navi_rules[\$id][add]' value='1' \$add_checked />添加</label></td>
			<td style='padding-left:0px;'><label><input type='checkbox' name='navi_rules[\$id][edit]' value='1' \$edit_checked />编辑</label></td>
			<td style='padding-left:0px;'><label><input type='checkbox' name='navi_rules[\$id][delete]' value='1' \$delete_checked />删除</label></td>
			<td style='padding-left:0px;'><label><input type='checkbox' name='navi_rules[\$id][sort]' value='1' \$sort_checked />排序</label></td>
			</tr>";
            $tree->init($navigate);
            $data = $tree->get_tree(0, $str);
            $this->assign("navigate_str", $data);
            $this->display();
        }
    }

    /* 删除 */

    public function delete() {
        $id = $this->checkData('id');
        $status = $this->model->where("id IN($id)")->delete();
        if ($status) {
            $this->model->table(DB_PREFIX . 'admin_group_access')->where("group_id IN($id)")->delete();
        }
        if ($status) {
            //更新缓存
            R('Back/Public/_admin_rule');
            R('Back/Public/_admin_navigate');
        }
        $this->deletePublicMsg($status);
    }

}

?>