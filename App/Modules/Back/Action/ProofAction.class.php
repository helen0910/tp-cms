<?php

/**
 * 校对管理
 * 
 *
 */
class ProofAction extends GlobalAction {

    protected function _initialize() {
        parent::_initialize();
        parent::BackEntranceInit();
        $this->model = D('Mession');
        $this->modelSP = D('ShopProduct');
        $this->modelCheck = D('Check');
    }

    /* 任务列表 */

    public function index() {

        $taskArray = $this->model->messionList('p');
        $optUserArray = $this->model->getUserGroup('2');
        foreach ($_SESSION['admin_session']['admin_group'] as $k => $v) {
            if ($v['title'] == '数据人员') {
                $dataUserArray[] = array('id' => $_SESSION['admin_session']['id'], 'username' => $_SESSION['admin_session']['username']);
            } else {
                $dataUserArray = $this->model->getUserGroup('3');
            }
        }
        $this->assign('dataUserArray', $dataUserArray);
        $this->assign('optUserArray', $optUserArray);
        $this->assign('taskArray', $taskArray);
        $this->display();
    }

    /*
     * 免税店信息
     */

    public function shop_info() {
        if (IS_POST) {
            $checkData['check_status'] = 4;
            $mid = $_POST['mid'];
            $pid = $_POST['pid'];
            $this->modelSP->updateInfo($_POST['product']);
            $this->modelCheck->updateDataStatus("ProductId={$pid}", $checkData);
            $this->modelCheck->selectCheck($mid);
            $this->redirect('Back/Proof/index');
        } else {
            $id = ($_REQUEST['id']);
            $mid = $_REQUEST['mid'];
            $dataArray = $this->modelSP->dataList($id);
            $count_s = $this->modelSP->countShop($id);
            $this->assign('count_shop',$count_s);
            $this->assign('dataArray', $dataArray);
            $this->assign('mid', $mid);
            $this->assign('pid', $id);
            $this->display();
        }
    }
}

?>