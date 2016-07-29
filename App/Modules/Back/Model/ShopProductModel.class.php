<?php

/**
 * 任务管理模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class ShopProductModel extends GlobalModel {

    /**
     * 获取数据列表
     * @return multitype:Ambigous <string, mixed> Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
     */
    public function dataList($pid) {
        $where = '1=1';
        $where .= " AND sp.product_id =$pid";
        $dataArray = array();
        $dataArray = $this->field("sp.*,c.check_status,c.mession_id as mid,m.mession_status")
                        ->table("{$this->tablePrefix}shop_product AS sp")
                        ->join("LEFT JOIN {$this->tablePrefix}check AS c ON c.ProductId=sp.product_id")
                        ->join("LEFT JOIN {$this->tablePrefix}mession AS m ON m.mession_id = c.mession_id")
                        ->where($where)->select();
        //$dataArray = $this->field("*,c.check_status,c.mession_id as mid")->join("LEFT JOIN {$this->tablePrefix}check AS c ON c.ProductId=product_id")->where($where)->select();
        return $dataArray;
    }

    /*
     * 查询产品免税店状态
     */

    public function countShop($pid) {
        $count_shop = $this->where("product_id =$pid and status=1")->count(1);
        return $count_shop;
    }

    /*
     * 更新产品免税店信息
     */

    public function updateInfo($dataArray) {
        foreach ($dataArray as $key => $val) {
            $postData['product_url'] = $val['purl'];
            $postData['product_price'] = $val['price'];
            $postData['status'] = 0;
            $this->where("id={$val['name']}")->save($postData);
        }
        return TRUE;
    }

    /* 更改产品免税店状态 */

    public function updateStatus($where, $postdata) {
        return $this->where($where)->save($postdata);
    }

}

?>