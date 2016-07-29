<?php

/**
 * 管理员组模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class AdminGroupModel extends GlobalModel {

    /**
     * 验证数据
     * @param array|string $setting
     * @return Ambigous <boolean, unknown, multitype:unknown string >
     */
    public function checkData() {
        $postData = Tool::filterData($_POST);
        return ValiData::_vail()->_check(array(
                    'title' => array('s1-30', '组角色名格式不正确！'),
                    'remark' => array('a|s1-255', '备注格式不正确！'),
                        ), $postData);
    }

    /* 查询 */

    public function selectData() {
        $groupData = $this->table("{$this->tablePrefix}admin_group AS c")->join("LEFT JOIN {$this->tablePrefix}admin_group_access AS g ON g.group_id=c.id")->field('g.uid,c.*')->select();
        return $groupData;
    }

    /**
     * 数据添加
     * @param array $postData
     * @return Ambigous <mixed, boolean, string, unknown, false, number>
     */
    public function addData($postData) {
        return $this->data($postData)->add();
    }

    /**
     * 数据编辑
     * @param array $postData
     * @return Ambigous <boolean, false, number>
     */
    public function editData($postData) {
        return $this->where("id={$_POST['id']}")->save($postData);
    }

    /**
     * 设置权限规则
     * @return Ambigous <boolean, false, number>
     */
    public function setRule() {
        // 			写入数据库
        $status = $this->where("id={$_POST['id']}")->setField('rules', implode(',', $_POST['rule']));
        if ($status !== false) {
            $ruleId = implode(',', $_POST['rule']);
            $ruleData = $this->table($this->tablePrefix . 'admin_rule')->where("id IN($ruleId) AND status=1")->order('sort DESC')->getField('id,name,title,show_status,node_type,condition,pid,sort');
            //更新后台管理员规则列表
            F("admin_rule_{$_POST['id']}", $ruleData);
            $status = true;
        }
        return $status;
    }

}

?>
