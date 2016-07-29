<?php
/**
 * 系统全局扩展行为
 * 
 *
 */
class GBehavior extends Behavior {
	/* (non-PHPdoc)
	 * @see Behavior::run()
	 */
	public static $session = null;
	public function run(&$params) {
		//当前IP
		define('CLIENT_IP', get_client_ip());
		define('CLIENT_IP_NUM', get_client_ip(1));
		//今天开始时间
		define('TODAY_START', strtotime(date('Y-m-d')));
		//重新设置表前缀，网站名称，网站地址避免多次重复使用C函数
		define('DB_PREFIX', C('DB_PREFIX'));
		define('WEB_NAME', C('WEB_NAME'));
		define('WEB_URL', C('WEB_URL'));
		define('DEFAULT_SKIN',C('DEFAULT_SKIN'));
		//会员状态
		$this->checkUser();
	}

	/* 网站所有用户判断，前台，后台，未登录 */
	private function checkUser() {
		//后台管理员
		if (isset($_SESSION[C('ADMIN_SESSION')]) && !empty($_SESSION[C('ADMIN_SESSION')])) {
			self::$session = $_SESSION[C('ADMIN_SESSION')];
			$sessionType = 1;
			//是否是超管
			define('SUPER_ADMIN',isset($_SESSION[C('SUPER_ADMIN')]) && !empty($_SESSION[C('SUPER_ADMIN')]) ? true :false);
			/* 后台不缓存配置 */
			C('DB_SQL_BUILD_CACHE',false);//不创建Sql查询缓存
		}
		//未登录模式 
		else {
			$sessionType = 0;
			self::$session = null;
		}
		//会员SESSION类型
		define('SESSION_TYPE',$sessionType);
	}
	
}
?>