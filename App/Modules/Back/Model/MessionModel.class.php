<?php

/**
 * 任务管理模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class MessionModel extends GlobalModel {

    /**
     * 数据验证
     * @param array $setting 扩展参数
     * @return Ambigous <boolean, unknown, multitype:unknown string >
     */
    public function checkData($setting = array()) {
        $postData = Tool::filterData($_POST);
        return ValiData::_vail()->_check(array(
                    'username' => array('r/^[a-z][\w]{4,11}$/i', '用户名格式不正确！'),
                    'group' => array('l1,', '请选择所属权限组！'),
                    'password' => array($this, 'checkPasswordRule', $setting['type'], 's6-16', '密码格式不正确！'),
                        ), $postData);
    }

    /**
     * 获取任务列表
     * @return multitype:Ambigous <string, mixed> Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
     */
    public function messionList($status = '') {
        $where = '1=1';
        if (!isset($_GET['mession_status']) && $status) {
            if ($status == 'm') {
                $where .=' and (mession_status=2 or mession_status=3)';
            } else {
                $where .=' and (mession_status=2 or mession_status=4)';
            }
        }
        $options = $this->compareGET('dispatch_time', array('start_time', 'end_time'), 'time', false);
        $options['where'] = $where . ($options['where']);
        /* 数据人员 */
        foreach ($_SESSION['admin_session']['admin_group'] as $k => $v) {
            if ($v['title'] == '数据人员') {
                $options['where'] .=' and m.data_user_id=' . $_SESSION['admin_session']['id'];
                $options['url'] .= '&data_user_id=' . $_SESSION['admin_session']['id'];
            } else {
                $resolveOptions = $this->resolveGET(array('data_user_id'));
                $options['where'] .= $resolveOptions['where'];
                $options['url'] .= $resolveOptions['url'];
            }
        }
        /* 完成时间 */
        $optionsf = $this->compareGET('finished_time', array('f_start_time', 'f_end_time'), 'time', false);
        $options['where'] .= $optionsf['where'];
        $options['url'] .= $optionsf['url'];
        /* 运营人员 */
        $resolveOptions = $this->resolveGET(array('operator_id'));
        $options['where'] .= $resolveOptions['where'];
        $options['url'] .= $resolveOptions['url'];
        /* 任务状态 */
        if ($_GET['mession_status'] != 0) {
            $resolveOptionsd = $this->resolveGET(array('mession_status'));
            $options['where'] .= $resolveOptionsd['where'];
            $options['url'] .= $resolveOptionsd['url'];
        }
        $maint = "{$this->tablePrefix}mession AS m";
        $joina = "LEFT JOIN {$this->tablePrefix}admin AS a ON m.data_user_id=a.id";
        $joinat = "LEFT JOIN {$this->tablePrefix}admin AS ad ON m.operator_id =ad.id";
        $total = $this->table($maint)->join($joina)->join($joinat)->where($options['where'])->count(1);
        $Page = new Page($total, $options['url'], 50);
        $pageData = array();
        $pageData[0] = $this->table($maint)->join($joina)->join($joinat)->field("m.*,a.username as aname,ad.username as oname")->where($options['where'])->limit($Page->limit())->order('m.id DESC')->select();
       //echo $this->getLastSql();
        foreach ($pageData[0] as &$values) {
            $group = $this->table("{$this->tablePrefix}check AS c")->field("ProductId,Name,Code,Oprice,b.cname as cname")->join("LEFT JOIN {$this->tablePrefix}brand AS b ON c.Brand=b.bid")->where("'{$values['mession_id']}'=c.mession_id")->select();
            $values['group'] = $group;
        }
        $pageData[1] = $Page->show();
        return $pageData;
    }

    /** 获取当前用户组里的用户* */
    public function getUserGroup($data) {
        $groupUser = $this->table("{$this->tablePrefix}admin AS c")->join("LEFT JOIN {$this->tablePrefix}admin_group_access AS g ON g.uid=c.id")->field('c.id,c.username')->where("g.group_id={$data} and c.admin_status=1")->select();
        return $groupUser;
    }

    /** 更新任务状态* */
    public function updateMessionStatus($mid, $postData) {
        return $this->where("mession_id='{$mid}'")->save($postData);
    }

    /** 新增任务* */
    public function insertMession($postData) {
        return $this->data($postData)->add();
    }
}
?>