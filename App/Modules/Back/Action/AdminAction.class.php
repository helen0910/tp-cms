<?php

/**
 * 后台管理员处理模型
 * 
 *
 */
class AdminAction extends GlobalAction {

    protected function _initialize() {
        parent::_initialize();
        parent::BackEntranceInit();
        $this->model = D('Admin');
    }

    /* 管理员列表 */

    public function index() {
        $adminArray = $this->model->adminPage();
        //$adminArray = $this->model->select();
        $this->assign('adminArray', $adminArray);
        $this->display();
    }

    /* 添加管理员 */

    public function add() {
        if (IS_POST) {
            $postData = $this->model->checkData(array('type' => 'add'));
            if (!$postData['vail_status'])
                $this->error($postData['vail_info']);
            $insertId = $this->model->addData($postData);
            //绑定用户组
            if ($insertId)
                D('AdminGroupAccess')->setAccess($postData['group'], $insertId);
            $this->addPublicMsg($insertId);
        }else {
            $this->getGroup();
            $this->display();
        }
    }

    /* 编辑管理员 */

    public function edit() {
        if (IS_POST) {
            $this->matchToken();
            $postData = $this->model->checkData(array('type' => 'edit'));
            if (!$postData['vail_status'])
                $this->error($postData['vail_info']);
                $status = $this->model->editData($postData);
            if ($status !== false) {
                D('AdminGroupAccess')->setAccess($postData['group'], $_POST['id']);
                $status = true;
            }
            $this->editPublicMsg($status);
        } else {
            $id = $this->checkData('id');
            $current = $this->findOneData($id);
            $current ? $this->assign('current', $current) : $this->error(C('FIND_ERROR'));
            $this->encryptToken($id);
            $this->assign('group', M('AdminGroupAccess')->where("uid=$id")->getField('group_id', true));
            $this->getGroup();
            $this->display();
        }
    }

    /* 初始化密码 */

    public function resetpwd() {
        $id = $this->checkData('id');
        $postData['id'] = $id;
        $postData['password'] = '123456';
        $status = $this->model->editData($postData);
        if ($status !== false) {
            $status = true;
        }
        $this->editPublicMsg($status);
    }

    /* 删除管理员 */

    public function delete() {
        $id = $this->checkData('id');
        $status = $this->model->where("id IN($id)")->delete();
        if ($status) {
            $this->model->table(DB_PREFIX . 'admin_group_access')->where("uid IN($id)")->delete();
        }
        $this->deletePublicMsg($status);
    }

    /* 得到所属组 */

    private function getGroup() {
        $groupData = M('AdminGroup')->where('status=1')->select();
        $this->assign('groupData', $groupData);
    }

}

?>