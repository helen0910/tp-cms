<?php
class Tool {
// 	数据字段串过滤
	public static function filterData($data,$filterStripTags = true) {
		if (is_array($data)) {
			// 			判断数组是否循环过滤
			if(!empty($data)) {
				foreach ($data as &$value) {
					$value = self::filterData($value,$filterStripTags);
				}
			}
		}else{
			//过滤空格
			$data = trim($data);
			//过滤html字符串，php字符串
			if($filterStripTags) $data = strip_tags($data);//Input::deleteHtmlTags()
			$data = Input::getVar($data);
		}
		return $data;
	}
	
	public static function mkDir($dir, $mode = 0777) {   
		if (is_dir($dir) || mkdir($dir, $mode)) return true;
	     if (!self::mkDir(dirname($dir), $mode)) return false;
	     return mkdir($dir, $mode);
	}
	
	public static function encrypt($value,$key = '',$type = 'sha1') {
		if (empty($key)) $key = C('WEB_ENCRYPTION_KEY') ;
		return $type=='sha1' ? sha1(Hmac::sha1($value,$key)) : md5(Hmac::md5($value,$key));
	}
	
	/**
	 * 远程url内容获取但设置了超时间
	 * @param string $url
	 * @return string
	 */
	public static function remoteUrl($url) {
		$ctx = stream_context_create(
			array(
				'http'=>array('timeout'=>5),//设置超时时间为5秒
			)
		);
		return file_get_contents($url,0,$ctx);
	}
	/**
	 * 友好的时间显示
	 *
	 * @param int    $sTime 待显示的时间
	 * @param string $type  类型. normal | mohu | full | ymd | other
	 * @param string $alt   已失效
	 * @return string
	 */
	public static function easyFormatData($sTime,$type = 'normal',$alt = 'false') {
		//sTime=源时间，cTime=当前时间，dTime=时间差
		$cTime        =    time();
		$dTime        =    $cTime - $sTime;
		$dDay        =    intval(date("z",$cTime)) - intval(date("z",$sTime));
		//$dDay        =    intval($dTime/3600/24);
		$dYear        =    intval(date("Y",$cTime)) - intval(date("Y",$sTime));
		//normal：n秒前，n分钟前，n小时前，日期
		if($type=='normal'){
			if( $dTime < 60 ){
				return $dTime."秒前";
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
				//今天的数据.年份相同.日期相同.
			}elseif( $dYear==0 && $dDay == 0  ){
				//return intval($dTime/3600)."小时前";
				return '今天'.date('H:i',$sTime);
			}elseif($dYear==0){
				return date("m月d日 H:i",$sTime);
			}else{
				return date("Y-m-d H:i",$sTime);
			}
		}elseif($type=='mohu'){
			if( $dTime < 60 ){
				return $dTime."秒前";
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
			}elseif( $dTime >= 3600 && $dDay == 0  ){
				return intval($dTime/3600)."小时前";
			}elseif( $dDay > 0 && $dDay<=7 ){
				return intval($dDay)."天前";
			}elseif( $dDay > 7 &&  $dDay <= 30 ){
				return intval($dDay/7) . '周前';
			}elseif( $dDay > 30 ){
				return intval($dDay/30) . '个月前';
			}
			//full: Y-m-d , H:i:s
		}elseif($type=='full'){
			return date("Y-m-d , H:i:s",$sTime);
		}elseif($type=='ymd'){
			return date("Y-m-d",$sTime);
		}else{
			if( $dTime < 60 ){
				return $dTime."秒前";
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
			}elseif( $dTime >= 3600 && $dDay == 0  ){
				return intval($dTime/3600)."小时前";
			}elseif($dYear==0){
				return date("Y-m-d H:i:s",$sTime);
			}else{
				return date("Y-m-d H:i:s",$sTime);
			}
		}
	}
	
	/**
	 * 内容表情转换
	 * @param String $content
	 * @return mixed
	 */
	public static function faceChange($content) {
		return preg_replace('/\[(.*)\s?\/\]/Ui', '<img src="'.__ROOT__.'/Public/images/face/$1.gif">', $content);
	}
	
	/**
	 * IP，操作限制，多种
	 * @param key $key
	 * @param string $value
	 * @return Ambigous <>|multitype:string
	 */
	public static function ipLimit($key,$value = '') {
		$ip = get_client_ip(1);
		$cache = S($ip);
		if (!$cache) $cache = array();
		if (empty($value)) {//取
			return $cache[$key];
		} else {//存
			$cache[$key] = $value;
			S($ip,$cache,360*24*3600);//此种缓存一年
			return $cache;
		}
	}
	
	/**
	 * @desc  im:十进制数转换成三十六机制数
	 * @param (int)$num 十进制数
	 * return 返回：三十六进制数
	 */
	public static function get10To36($num) {
		$num = intval($num);
		if ($num <= 0)
			return false;
		$charArr = array('0','1','2','3','4','5','6','7','8','9','A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$char = '';
		do {
			$key = ($num - 1) % 36;
			$char= $charArr[$key] . $char;
			$num = floor(($num - $key) / 36);
		} while ($num > 0);
		return $char;
	}
	
	/**
	 * @desc  im:三十六进制数转换成十机制数
	 * @param (string)$char 三十六进制数
	 * return 返回：十进制数
	 */
	public static function get36To10($char){
		$array=array('0','1','2','3','4','5','6','7','8','9','A', 'B', 'C', 'D','E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O','P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z');
		$len=strlen($char);
		for($i=0;$i<$len;$i++){
			$index=array_search($char[$i],$array);
			$sum+=($index+1)*pow(36,$len-$i-1);
		}
		return $sum;
	}
	
	//创建Hash目录
	public static function createHashDir($hashDirName,$filepath = '',$Layer = 2) {
		$path = self::getHashDir($hashDirName, $filepath,$Layer);
		Tool::mkDir($path);
		return $path;
	}
	
// 	得到hash目录
	public static function getHashDir($hashDirName,$filepath = '',$Layer = 2) {
		$len = strlen($hashDirName);
		$dirPath = '';
		for ($i=1;$i<=$len;$i++) {
			$dirName = substr($hashDirName, $i-1,1);
			$dirPath = $dirPath.'/'.$dirName;
			if ($i == $Layer) break;
		}
		return empty($filepath) ? ltrim($dirPath,'/').'/' : rtrim($filepath,'/').$dirPath.'/';
	}
	
/**
	 * 得到一个文件或目录的大小
	 * @param string $path
	 */
	public static function getDirSize($path) {
		if (is_dir($path)) {
			$size = 0;
			$dirArr = scandir($path);
			foreach ($dirArr as $dirVal) {
				if ($dirVal == '.' || $dirVal == '..') continue;
				$filePath = "{$path}/{$dirVal}";
				$size += is_dir($filePath) ? self::getDirSize($filePath) : filesize($filePath);
			}
		}else{
			$size = filesize($path);
		}
		return $size;
	}
	
	/**
	 * 
	 * 单位转换，字节转换为常用单位量
	 * @param numeric $size => Beat
	 * @return string
	 */
	public static function unitConversion($size,$delimiter = ''){
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		for ($i = 0; $size >= 1024 && $i < 6; $i++) $size /= 1024;
		return round($size, 2) . $delimiter . $units[$i];
	}
	
	
	/**
	 *
	 * 文件大小转换为字节
	 * @param numeric $size => Beat
	 * @return numeric
	 */
	public static function sizeToBytes($size) {
		//如果是数字 返回原值 单位 Bytes
		if(is_numeric($size)) return $size;
		//获取单位
		$unit = strtoupper(substr($size,-2,2));
		//获取数值
		$size = rtrim($size,$unit);
		//真实Bytes尺寸
		$realSize = 0;
		switch($unit){
			case 'KB' : $realSize = $size * pow(2,10); break;
			case 'MB' : $realSize = $size * pow(2,20); break;
			case 'GB' : $realSize = $size * pow(2,30); break;
			default	  : $realSize = 0;
		}
		return $realSize;
	}
	/**
	 * 删除文件或目录
	 * @param string $path
	 */
	public static function removeDir($path) {
		$path = rtrim($path,'/');
		if (is_dir($path)) {
			$dirArr = scandir($path);
			foreach ($dirArr as $dirVal) {
				if ($dirVal == '.' || $dirVal == '..') continue;
				$filePath = $path.'/'.$dirVal;
				is_dir($filePath) ? self::removeDir($filePath) : @unlink($filePath);
			}
		}else{
			return @unlink($path);
		}
		chmod($path, 0777);
		return @rmdir($path);
	}
	
	/**
	 * 取得一个目录下面的文件
	 * @param string $path
	 */
	public static function getFile($path) {
		$fileArr = scandir($path);
		$newFileArr = array();
		foreach ($fileArr as $value) {
			if($value == '.' || $value == '..') continue;
			$newFileArr[] = $value;
		}
		return $newFileArr;
	}
	/**
	 * 隐藏部分字符串
	 * @param string $string
	 * @param number $start
	 * @param number $length
	 * @param string $symbol
	 * @return mixed
	 */
	public static function hideString($string,$start = 0,$length = 5,$symbol = '*') {
		$symbolLen = strlen(substr($string, $start,$length));//防止$length有负数，再次判断长度
		return substr_replace($string, str_repeat($symbol, $symbolLen), $start,$length);
	}
	
	/**
	 * 返回数组原型
	 * @param array $array
	 * @param numeric $type  转换数组的类型1常用类型，2解析类型
	 * @param string|numeric $m_key   2手动解析类型，主要为模板解析使用
	 * @return string
	 */
	//private static $arrayString = '';
	public static function arrayPrototype($array,$type = 1,$m_key = '') {
		if ($type == 1) {
			ob_start();
			var_export((array) $array);
			return ob_get_clean();
		} else {
			if (is_array($array)) {
				$array_str = empty($m_key) ? 'array(' : "'$m_key'=>array(";
				foreach ($array as $key=>$values) {
					if (is_array($values)) {
						$array_str .= self::arrayPrototype($values,$type,$key).',';
					}else {
// 						$array_str .= "'{$key}'=>'".preg_replace('/(\#|\{)(.*)(\#|\})/U', "'.(stripslashes($2)).'", addslashes($values))."',";
// 						return $matches[2] ? \'.('.eval("return stripslashes('\$matches[2]');").').\' : "";
// 						$array_str .= "'{$key}'=>'".
// 								preg_replace_callback('/(\#|\{)(.*)(\#|\})/U',
// 								create_function('$matches', '
// 										if($matches[2]) {
// 	 										return stripslashes($matches[2]);
// 										} else {
// 	 										return "";
// 	 									}
// 								')
// 								,addslashes($values))."',";
						/* 这种写法，无法支持直在在where或sql里面写intval($_GET['id'])，只能在外面$id变量 */
						$array_str .= "'{$key}'=>'".preg_replace('/(\#|\{)(.*)(\#|\})/U',"'.$2.'", addslashes($values))."',";
// 						if (strpos($values,'#') === false && strpos($values,'{') === false && strpos($values,'}') === false) {
// 							$array_str .= "'{$key}'=>'".addslashes($values)."',";
// 						} else {
// 							$array_str .= "'{$key}'=>'".preg_replace('/(\#|\{)(.*)(\#|\})/U',"'.$2.'", addslashes($values))."',";
// 						}
					}
				}
				return $array_str .')';
			}
		}
	}
	
// 	function b('.$matches.') {
// 		return $matches[2] ? '\''.stripslashes($matches[2]).'\'' : "";
// 	}
}
?>