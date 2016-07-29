<?php

/**
 * 任务管理模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class CheckModel extends GlobalModel {

    /**
     * 获取数据列表
     * @return multitype:Ambigous <string, mixed> Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
     */
    public function dataList() {
        $where = '1=1';
        $where .= " AND check_status=1 and mession_id is null";
        $field = "id as cid,ProductId,Name,Code,Oprice,Price,b.cname as cname";
        $joinT = "LEFT JOIN {$this->tablePrefix}brand AS b ON Brand=b.bid";
        $total = $this->join($joinT)->where($where)->count(1);
        $Page = new Page($total, '', 50);
        $pageData = array();
        $pageData[0] = $this->join($joinT)->field($field)->where($where)->limit($Page->limit())->order('id ASC')->select();
        // echo $this->getLastSql();
        $pageData[1] = $Page->show();
        return $pageData;
    }

    /*
     * 根据条件更新数据状态
     */

    public function updateDataStatus($where, $postData) {
        return $this->where($where)->save($postData);
    }

    /*
     * 一个任务下的数据列表
     */

    public function findMessionData($mid = '') {
        $where = '1=1';
        $url = '';
        if ($mid) {
            $where .=" and mession_id='{$mid}'";
        }
        $total = $this->where($where)->count(1);
        $Page = new Page($total, $url, 50);
        $pageData = array();
        $pageData[0] = $this->join("LEFT JOIN {$this->tablePrefix}brand AS b ON Brand=b.bid ")
                        ->field('id as cid,ProductId,Name,Code,Oprice,Price,b.cname as cname,check_status')
                        ->where($where)->limit($Page->limit())->order('id ASC')->select();
        foreach ($pageData[0] as &$values) {
            $group = $this->table("{$this->tablePrefix}shop_product AS sp")->field("sp.*,c.check_status")->join("LEFT JOIN {$this->tablePrefix}check AS c ON c.ProductId=sp.product_id")->where("sp.product_id ={$values['ProductId']}")->select();
            $values['group'] = $group;
        }
        $pageData[1] = $Page->show();
        return $pageData;
    }

    /*
     * 判断该任务下所有数据是否都是已打回，如果是则任务状态为已打回
     */

    public function updateMession($mid) {
        $check = M('MessionModel:Mession');
        $where = "mession_id='{$mid}'";
        $whereb = $where . " AND check_status=2";
        $total = $this->where($where)->count(1);
        $backTotal = $this->where($whereb)->count(1);
        if ($backTotal == 0) {
            $wheres = $where . " AND check_status=4";
            $saveTotal = $this->where($wheres)->count(1);
            if ($total == $saveTotal) {
                $postData['mession_status'] = 5;
                $postData['finished_time'] = time();
                $status = $check->updateMessionStatus($mid, $postData);
            } else {
                $status = true;
            }
        } else {
            $postData['mession_status'] = 4;
            $status = $check->updateMessionStatus($mid, $postData);
        }
        return $status;
    }

    /*
     * 判断任务下所有数据是否是待入库，如果是该任务状态为已完成
     */

    public function selectCheck($mid) {
        $check = M('MessionModel:Mession');
        $where = "mession_id='{$mid}'";
        $whereb = $where . " AND check_status=4";
        $total = $this->where($whereb)->count(1);
        $totals = $this->where($where)->count(1);
        if ($total == $totals) {
            $postData['mession_status'] = 5;
            $postData['finished_time'] = time();
            $status = $check->updateMessionStatus($mid, $postData);
        } else {
            $status = FALSE;
        }
        return $status;
    }
    /*
     * 查看任务下的产品
     */
    public function selectPro($mid){
        $data_p = array();
        $data_p = $this->field('ProductId')->where("mession_id='$mid'")->select();
        return $data_p;
    }
}
?>