<?php

/**
 * 任务管理
 * 
 *
 */
class MessionAction extends GlobalAction {

    protected function _initialize() {
        parent::_initialize();
        parent::BackEntranceInit();
        $this->model = D('Mession');
        $this->modelCheck = D('Check');
        $this->modelSP = D('ShopProduct');
    }

    /* 任务列表 */

    public function index() {
        $this->assign('mession_status', $_GET['mession_status']);
        $taskArray = $this->model->messionList('m');
        $optUserArray = $this->model->getUserGroup('2');
        $dataUserArray = $this->model->getUserGroup('3');
        $this->assign('dataUserArray', $dataUserArray);
        $this->assign('optUserArray', $optUserArray);
        $this->assign('taskArray', $taskArray);
        $this->display();
    }

    /*
     * 新增任务页面
     */

    public function add() {
        $dataArray = $this->modelCheck->dataList();
        $this->assign('dataArray', $dataArray);
        $this->display();
    }

    /*
     * 取消任务/任务关闭
     * 该任务信息下所有数据信息状态更改为”待校验“，任务单状态为”已关闭“
     */

    public function cancel_mession() {
        if (IS_POST) {
            $id = $this->checkData('id');
            $postData['mession_status'] = 6;
            $checkData['check_status'] = 1;
            $checkData['mession_id'] = NULL;
            $spData['status'] = 1;
            $status = $this->model->updateMessionStatus($id, $postData);
            $pro_array = $this->modelCheck->selectPro($id);
            $proId_array = array();
            foreach ($pro_array as $key => $val) {
                $proId_array[] = $val['ProductId'];
            }
            $proid = implode(',', $proId_array);
            if ($status !== false) {
                $this->modelCheck->updateDataStatus("ProductId IN ($proid)", $checkData);
                $this->modelSP->updateStatus("product_id IN ($proid)",$spData);
                $this->writeLog('任务' . $id . '取消成功', 'INFO');
            } else {
                $this->writeLog('任务' . $id . '取消失败', 'INFO');
            }
            $this->editPublicMsg($status);
        }
    }

    /*
     * 确认任务，查询任务下的数据
     */

    public function confirm() {
        $mid = $this->checkData('id');
        $dataArray = $this->modelCheck->findMessionData($mid);
        $this->assign('dataArray', $dataArray);
        $this->assign('mid', $mid);
        $this->display();
    }

    /*
     * 打回数据
     * 该任务”已打回“状态，出错数据状态更改为”已打回“状态
     */

    public function back() {
        $mid = $_GET['mid'];
        $dataid = $this->checkData('id');
        $checkData['check_status'] = 2;
        $status = $this->modelCheck->updateDataStatus("id IN($dataid)", $checkData);
        if ($status !== false) {
            $status = true;
            $this->writeLog('任务'.$mid.'打回成功', 'INFO');
        }
        $this->editPublicMsg($status, array('打回成功', '打回未成功'), U('back/mession/index'));
    }

    /*
     * 保存数据
     * 该任务”已完成“状态，任务下数据更改为” 待入库“状态
     */

    public function save() {
        $dataid = $this->checkData('id');
        $checkData['check_status'] = 4;
        $status = $this->modelCheck->updateDataStatus("id IN($dataid)", $checkData);
        if ($status !== false) {
            $status = true;
            $this->writeLog('数据状态修改为待入库', 'INFO');
        }
        $this->editPublicMsg($status, array('保存成功', '保存未成功'), U('back/mession/index'));
    }

    /*
     * 任务确认，保存
     */

    public function update_mession() {
        $mid = $_GET['mid'];
        $status = $this->modelCheck->updateMession($mid);
        if ($status !== false) {
            $status = true;
            $this->writeLog('任务'.$mid.'确认完成', 'INFO');
        }
        $this->editPublicMsg($status, array('保存成功', '保存未成功'), 'save');
    }

    /*
     * 点击分派弹出分派页面
     */

    public function dispatch() {
        $dataid = $this->checkData('id');
        $dataUserArray = $this->model->getUserGroup('3');
        $this->assign('dataid', $dataid);
        $this->assign('dataUserArray', $dataUserArray);
        $this->display();
    }

    /*
     * 人员分派确认 
     */

    public function dispatch_save() {
        $dataid = $_GET['dataid'];
        $checkData['data_user_id'] = $this->checkData('id');
        $checkData['mession_id'] = "mes" . rand(1000, 9999);
        $messionData['mession_id'] = $checkData['mession_id'];
        $messionData['data_user_id'] = $this->checkData('id');
        $messionData['dispatch_time'] = time();
        $messionData['mession_status'] = 2;
        $messionData['operator_id'] = $_SESSION['admin_session']['id'];
        $status = $this->model->insertMession($messionData);
        if ($status !== false) {
            $this->modelCheck->updateDataStatus("id IN($dataid)", $checkData);
            $status = true;
            $this->writeLog('新增任务人员分派完成', 'INFO');
        }
        $this->editPublicMsg($status, array('保存成功', '保存未成功'), 'save');
    }

    /* 付款 */

    public function pay() {
        $mid = $this->checkData('id');
        $postData['mession_status'] = 7;
        $status = $this->model->updateMessionStatus($mid, $postData);
        $this->writeLog('任务'.$mid.'付款完成', 'INFO');
        $this->editPublicMsg($status, array('付款成功', '付款未成功'));
    }

    /* 对已完成的任务进行查看 */

    public function view_mession() {
        if (IS_POST) {
            $status = $this->modelSP->updateInfo($_POST['product']);
            if ($status) {
                $this->writeLog('更新免税店信息成功', 'INFO');
                $this->redirect('Back/Mession/index');
            }
        } else {
            $mid = $this->checkData('id');
            $dataArray = $this->modelCheck->findMessionData($mid);
            $this->assign('dataArray', $dataArray);
            $this->assign('mid', $mid);
            $this->display();
        }
    }

    /* 对已打回的任务进行查看 */

    public function view_back_mession() {
        if (IS_POST) {
            $status = $this->modelSP->updateInfo($_POST['product']);
            if ($status) {
                $mid = $_POST['mid'];
                $dataid_array = $_POST['id_all'];
                $dataid = implode(",", $dataid_array);
                $checkData['check_status'] = 4;
                $this->modelCheck->updateDataStatus("id IN($dataid)", $checkData);
                $postData['mession_status'] = 5;
                $postData['finished_time'] = time();
                $this->model->updateMessionStatus($mid, $postData);
                $this->writeLog('已打回任务'.$mid.'状态修改为任务完成', 'INFO');
                $this->redirect('Back/Mession/index');
            }
        } else {
            $mid = $this->checkData('id');
            $dataArray = $this->modelCheck->findMessionData($mid);
            $this->assign('dataArray', $dataArray);
            $this->assign('mid', $mid);
            $this->display();
        }
    }

    /*
     * 对已完成的任务再次进行数据打回
     * 该任务”已打回“状态，出错数据状态更改为”已打回“状态
     */

    public function back_finish() {
        $mid = $_GET['mid'];
        $dataid = $this->checkData('id');
        $checkData['check_status'] = 2;
        $status = $this->modelCheck->updateDataStatus("id IN($dataid)", $checkData);
        if ($status) {
            $postData['mession_status'] = 4;
            $this->model->updateMessionStatus($mid, $postData);
            $this->writeLog('已完成的任务'.$mid.'再次打回成功', 'INFO');
        }
        $this->editPublicMsg($status, array('打回成功', '打回未成功'), 'save');
    }

    /**
     * 更改免税店状态
     */
    public function update_sp_status() {
        $dataPost['status'] = $_POST['status'];
        $where = "id=$_POST[id]";
        $status = $this->modelSP->updateStatus($where, $dataPost);
        $this->writeLog('免税店标识状态修改成功', 'INFO');
        $this->editPublicMsg($status, array('状态修改成功', '状态修改未成功'));
    }

}

?>