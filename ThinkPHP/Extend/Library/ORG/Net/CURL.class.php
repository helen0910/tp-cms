<?php
/**
 * CURL远程操作类
 * 
 *
 */
class CURL {
	
	/**
	 * 数据采集
	 * @param string $url
	 * @param string $setting
	 */
	public static function collection($url,$setting = array()) {
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_TIMEOUT=>isset($setting['timeout']) ? $setting['timeout'] : 30,
			CURLOPT_RETURNTRANSFER=>true,
			CURLOPT_HEADER=>false,
		));
		$result = curl_exec($ch);
		if ($result === false) {
			$string = '抓取页面出错：<hr />';
			$string .= '<hr />错误号：'.curl_errno($ch);
			$string .= '<hr />错误信息：'.curl_error($ch);
			return array('result'=>false,'error_info'=>$string);
		}
		curl_close($ch);
		return array('result'=>$result);
	}
	
	/**
	 * CURL HTTP数据发送
	 * @param string $url
	 * @param string|array $params
	 * @param string $method
	 * @param array $header
	 */
	public static function http($url, $params = null,$method = 'GET',$header = array()) {
		$options = array(
			CURLOPT_TIMEOUT=>20,
			CURLOPT_RETURNTRANSFER=>true,//true  不自动输出   false需要使用print或echo 打印结果
			CURLOPT_HTTPHEADER=>$header,
		);
		if ($method == 'GET') {
			$options[CURLOPT_URL] = $url.'?'.(is_array($params)&&!empty($params)) ? http_build_query($params) : ltrim($params,'?');
		} else {
			$options[CURLOPT_URL] = $url;
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = $params;
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		if (!$result) {
			$Model = new GlobalModel();
			$Model->writeLog("CURL执行错误，错误号：".curl_errno($ch).'，错误信息：'.curl_error($ch), 'SYSTEM_ERROR');
			unset($Model);
		}
		curl_close($ch);
		return $result;
	}
}
?>