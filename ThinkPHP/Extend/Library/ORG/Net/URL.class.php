<?php
class URL {
	
	/**
	 * 获取导航URL
	 * @param numeric $cid 导航id
	 * @param Array $navigate
	 * @param boolean $isPage
	 * @param boolean $pageClassPage  是否是使用分页类分页，true,只有单页设置为false
	 * @return string
	 */
	public static function get_navigate_url($cid,$navigate,$isPage = false,$pageClassPage = true) {
		//url获取
		$URLRule = D('Content/UrlRule');
		$urlRule = $URLRule->where("url_name='navigate' AND u_status=1")->getField('id,rule,is_html,model,url_name,append_var');
		if ($navigate['setting']['navigate_is_html'] == 1) {
			$root = __ROOT__.'/'.HTML_DIR.'/';
			$urlRule_id = $navigate['setting']['navigate_url_rule_html'];
			$url = $urlRule[$urlRule_id]['rule'];
		}else {
			$root = __ROOT__.'/';
			$urlRule_id = $navigate['setting']['navigate_url_rule_no_html'];
			$url = $urlRule[$urlRule_id]['rule'];
		}
		//page处理
		$url = explode('|', $url);
		if ($isPage) {
			$pageMark = $pageClassPage ? '{PAGE}' : $isPage;//兼容分页类中的{PAGE}标识符
			$url = str_replace('{$page}', $pageMark, $url[1]);
		} else {
			$url = $url[0];
		}
		//路径处理
		$root = $root.ltrim(str_replace(array('{$cid}','{$parent_navigate_dir}','{$navigate_dir}','{$mid}'), array($cid,$navigate['parent_navigate_mark'],$navigate['navigate_mark'],$navigate['mid']), $url),'/');
		/* 添加其它系统变量 */
		if(!empty($urlRule[$urlRule_id]['append_var'])) {
			$append_var = $URLRule->getAppendVars($urlRule[$urlRule_id],$navigate);
			$root = str_replace($append_var['mark'], $append_var['data'], $root);
		}
		return $root;
	}
	
	/**
	 * 获取内容URL
	 * @param numeric $aid
	 * @param array $data
	 * @param boolean $is_page
	 * @return Ambigous <string, mixed>
	 */
	public static function get_content_url($aid,$data,$is_page = false) {
		$cid = $data['cid'];
		$create_time = $data['create_time'];
		$navigate = F('navigate');
		$currentNavigate = $navigate[$cid];
		$URLRule = D('Content/UrlRule');
		$urlRule = $URLRule->where("url_name='show' AND u_status=1")->getField('id,rule,is_html,model,url_name,append_var');
		if ($currentNavigate['setting']['content_is_html'] == 1) {
			$root = __ROOT__.'/'.HTML_DIR.'/';
			$urlRule_id = $currentNavigate['setting']['content_url_rule_html'];
			$url = $urlRule[$urlRule_id]['rule'];
			$url = str_replace(array('{$parent_navigate_dir}','{$navigate_dir}','{$aid}','{$y}','{$m}','{$d}'), array($currentNavigate['parent_navigate_mark'],$currentNavigate['navigate_mark'],$aid,date('Y',$create_time),date('m',$create_time),date('d',$create_time)), $url);
		} else {
			$root = __ROOT__.'/';
			$urlRule_id = $currentNavigate['setting']['content_url_rule_no_html'];
			$url = $urlRule[$urlRule_id]['rule'];
			$url = str_replace(array('{$aid}','{$cid}'), array($aid,$cid), $url);
		}
		$url = explode('|', $url);
		$url = $is_page ? str_replace('{$page}', $is_page, $url[1]) : $url[0];
		$root = $root.ltrim($url,'/');
		/* 添加其它系统变量 */
		if(!empty($urlRule[$urlRule_id]['append_var'])) {
			$append_var = $URLRule->getAppendVars($urlRule[$urlRule_id],$data);
			$root = str_replace($append_var['mark'], $append_var['data'], $root);
		}
		return $root;
	}
	
	/**
	 * 获取内容URL的附加参数
	 * @param numeric $aid
	 * @param array $data
	 * @return multitype:array |NULL
	 */
	public static function get_content_append_params($data) {
		$navigate = F('navigate');
		$currentNavigate = $navigate[$data['cid']];
		$URLRule = D('Content/UrlRule');
		$urlRule = $URLRule->where("url_name='show' AND u_status=1")->getField('id,rule,is_html,model,url_name,append_var');
		$urlRule_id = $currentNavigate['setting']['content_is_html'] == 1 ? $currentNavigate['setting']['content_url_rule_html'] : $currentNavigate['setting']['content_url_rule_no_html'];
		/* 添加其它系统变量 */
		if(!empty($urlRule[$urlRule_id]['append_var'])) {
			return $URLRule->getAppendVarsAndValues($urlRule[$urlRule_id],$data);
		} else {
			return null;
		}
	}
	
	/**
	 * 获取导航URL的附加参数
	 * @param numeric $aid
	 * @param array $data
	 * @return multitype:array |NULL
	 */
	public static function get_navigate_append_params($navigate) {
		$URLRule = D('Content/UrlRule');
		$urlRule = $URLRule->where("url_name='navigate' AND u_status=1")->getField('id,rule,is_html,model,url_name,append_var');
		$urlRule_id = $navigate['setting']['navigate_is_html'] == 1 ? $navigate['setting']['navigate_url_rule_html'] : $navigate['setting']['navigate_url_rule_no_html'];
		/* 添加其它系统变量 */
		if(!empty($urlRule[$urlRule_id]['append_var'])) {
			return $URLRule->getAppendVarsAndValues($urlRule[$urlRule_id],$navigate);
		} else {
			return null;
		}
	}
}
?>