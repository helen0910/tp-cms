<?php

/**
 * 管理员模型
 * 
 * 常用模型处理方法  checkData->数据验证  addData->添加数据  editData->编辑数据  deleteData->删除数据
 */
class AdminModel extends GlobalModel {

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
     * 验证密码规则 添加或编辑管理员时
     * @param string $password
     * @param string $type 类型，添加或删除add edit
     * @return boolean
     */
    public function checkPasswordRule($password, $type) {
        $passwordLen = strlen($password);
        if ($type == 'add') {
            return ($passwordLen < 6 || $passwordLen > 16) ? false : true;
        } else {//edit
            if ($passwordLen == 0) {
                return true;
            } else {
                return ($passwordLen < 6 || $passwordLen > 16) ? false : true;
            }
        }
    }

    /**
     * 验证我的信息[修改]
     */
    public function checkMyInfo() {
        $postData = Tool::filterData($_POST);
        return ValiData::_vail()->_check(array(
                    'qq' => array('a|q', 'QQ号码格式不正确！！！'),
                    'password' => array('a|s6-16', '密码格式不正确！'),
                        ), $postData);
    }

    /**
     * 更新管理员SESSION及时数据
     * @param array $data
     */
    public function updateAdminSession($data) {
        foreach ($data as $key => $value) {
            $_SESSION[C('ADMIN_SESSION')][$key] = $value;
        }
    }

    /**
     * 验证登录
     * @return Array
     */
    public function checkLogin() {
        // 		验证数据
        $postData = Tool::filterData($_POST);
        $postData = ValiData::_vail()->_check(array(
            'username' => array('r/^[a-z][\w]{4,11}$/i', '用户名格式不正确！！！'),
            'password' => array('s6-16', '密码格式不正确！！！'),
                ), $postData);
        return $postData;
    }

    /**
     * 添加管理员
     * @param array $postData
     * @param array|string $setting
     * @return Ambigous <mixed, boolean, string, unknown, false, number>
     */
    public function addData($postData, $setting = array()) {
        $postData['reg_time'] = time();
        $postData['password'] = sha1(md5($postData['password']));
        return $this->data($postData)->add();
    }

    /**
     * 修改管理员的编辑数据
     * @param array $postData
     * @param array|string $setting
     * @return Ambigous <boolean, false, number>
     */
    public function editData($postData, $setting = array()) {
        if (isset($postData['password']) && !empty($postData['password'])) {
            $postData['password'] = sha1(md5($postData['password']));
        } else {
            unset($postData['password']);
        }
        return $this->where("id={$postData['id']}")->save($postData);
    }

    /**
     * 得到管理员=>用于登录
     * @param array $postData
     * @param array $userLoginError
     * @return boolean|array
     */
    public function getAdmin($postData, $userLoginError) {
        $userData = $this->where("username='{$postData['username']}'")->find();
        if ($userData) {
            if ($userData['admin_status'] == 2) {
                $this->writeLog("此用户：{$postData['username']}已被禁止登录网站后台，未能成功登录", 'USER_ERROR');
                return false;
            }
            if ($userData['password'] !== sha1(md5($postData['password']))) {
                $this->handleLoginError(1, $postData);
                $content = "用户登录失败，用户名{$postData['username']}，尝试的密码{$postData['password']}，加密后" . sha1(md5($postData['password']));
                $this->writeLog($content, 'USER_ERROR', 'AdminModel');
            } else {
                //写入登录成功日志
                $this->handleLoginSuccess($userData, 1);
                //保存登录数据
                $saveUserInfo = array();
                $saveUserInfo['login_num'] = $userData['login_num'] = $userData['login_num'] + 1;
                $saveUserInfo['login_time'] = $userData['login_time'] = NOW_TIME;
                $saveUserInfo['login_ip'] = $userData['login_ip'] = CLIENT_IP_NUM;
                $this->where("id={$userData['id']}")->save($saveUserInfo);
                //管理员组
                $userGroup = $this->table($this->tablePrefix . 'admin_group_access AS aa')->join("LEFT JOIN {$this->tablePrefix}admin_group AS ag ON ag.id=aa.group_id")->where("aa.uid={$userData['id']}")->getField('ag.id,ag.title,ag.is_super');
// 				判断是否超管 超管只显示超管组
                foreach ($userGroup as $values) {
                    if ($values['is_super'] == 1) {
                        session(C('SUPER_ADMIN'), true);
                        $userData['admin_group'] = array();
                        $userData['admin_group'][$values['id']] = $values;
                        break;
                    }
                }
// 				非超管 
                if (!isset($userData['admin_group']))
                    $userData['admin_group'] = $userGroup;
                session(C('ADMIN_SESSION'), $userData);
                //写入在线登录缓存
                $this->setOnline();
                return $userData;
            }
        }else {
            $this->handleLoginError(1, $postData);
            $this->writeLog("此IP登录后台失败尝试用户名{$postData['username']}", 'USER_ERROR', 'AdminModel');
            return false;
        }
    }

    /**
     * 更改当前在状态
     * @return array
     */
    public function setOnline() {
        $cache = F('online/admin_login');
        if (!$cache)
            $cache = array();
        $session = $_SESSION[C('ADMIN_SESSION')];
        $cacheArray = array('session_id' => session_id(), 'online_time' => time(), 'handle_status' => 3, 'username' => $session['username'], 'truename' => $session['truename']);
        $cache[$session['id']] = $cacheArray;
        F('online/admin_login', $cache);
        return $cacheArray;
    }

    /**
     * 后台用户分页
     * @return multitype:Ambigous <string, mixed> Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
     */
    public function adminPage() {
        $where = '1=1';
        $url = '';
        $resolveOptions = $this->resolveGET(array('admin_status'));
        $where .= $resolveOptions['where'];
        $url .= $resolveOptions['url'];
        
        $resolveOptionU = $this->resolveGET(array('username'),'like');
        $where .= $resolveOptionU['where'];
        $url .= $resolveOptionU['url'];
                
        $total = $this->where($where)->count(1);
        $Page = new Page($total, $url,50);
        $pageData = array();
        $pageData[0] = $this->where($where)->limit($Page->limit())->order('id ASC')->select();
        //echo $this->getLastSql();
        foreach ($pageData[0] as &$values) {
            $group = $this->table("{$this->tablePrefix}admin_group_access AS c")->join("LEFT JOIN {$this->tablePrefix}admin_group AS g ON c.group_id=g.id")->field('g.id,g.title')->where("{$values['id']}=c.uid")->select();
            $values['group'] = $group;
            $models = F("models_{$values['append_model']}");
            $values['append_model_name'] = $models['model_name'];
        }
        $pageData[1] = $Page->show();
        return $pageData;
    }
}

?>