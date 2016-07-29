<?php
// +----------------------------------------------------------------------
// | ValiData 数据验证类
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://crcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | 版本：1.0.2
// +----------------------------------------------------------------------
// | Author: CHENG [28737164@qq.com]
// +----------------------------------------------------------------------
class ValiData {
	private static $vailData;
	private $vailStatus = 'vail_status';
	private $vailError = 'vail_info';
	private $value = '';
	private $data;
	private $internalFunc = array('a','e','f','s','n','r','q','t','p','u','l','c');
	private function __construct() {}
	private function __clone(){}
	
	public function _check($checkData,$data) {
		$this->data = $data;
		foreach ($checkData as $checkKey=>$checkRule) {
// 			赋值
			$this->value = $this->getCurrentValue($data[$checkKey]);
// 			|中可以有&，但&不可有|，必须先判断顺
			// 取出错误提示消息
			$errorPrompt = array_pop($checkRule);
			if (is_object($checkRule[0])) {
				$call = array();
				$call[] = array_shift($checkRule);//对像
				$call[] = array_shift($checkRule);//方法
				array_unshift($checkRule, $this->value);//值
				/***新增反引用支持****/
				if (strpos($call[1], '!') === 0) {
					$call[1] = substr($call[1], 1);
					if (call_user_func_array($call, $checkRule)) return $this->checkInfo(false, $errorPrompt);
				} else {
					if (!call_user_func_array($call, $checkRule)) return $this->checkInfo(false, $errorPrompt);
				}
			}elseif (stripos($checkRule[0],'|' ) !== false) {//或者，必须 要在&之间判断
				if (!$this->checkOr($checkRule[0])) return $this->checkInfo(false, $errorPrompt);
			}elseif (stripos($checkRule[0], '&') !== false) {//与
				if (!$this->checkAnd($checkRule[0])) return $this->checkInfo(false, $errorPrompt);
			}else{
				$call_back_type = strtolower(substr($checkRule[0], 0,1));
				if (in_array($call_back_type, $this->internalFunc,true)) {
					if (!call_user_func_array(array($this,"_$call_back_type"), array(substr($checkRule[0], 1)))) return $this->checkInfo(false, $errorPrompt);
				}else {//普通函数以"_"开头
					$call = substr(array_shift($checkRule), 1);//函数
					array_unshift($checkRule, $this->value);//追加值
					/***新增反引用支持****/
					if (strpos($call, '!') === 0) {
						$call = substr($call, 1);
						if (call_user_func_array($call, $checkRule)) return $this->checkInfo(false, $errorPrompt);
					} else {
						if (!call_user_func_array($call, $checkRule)) return $this->checkInfo(false, $errorPrompt);
					}
				}
			}
		}
// 		添加状态码为true,验证通过，为了每次在外部，都先isset
		$this->data[$this->vailStatus] = true;
		return $this->data;
	}
	
// 	去除当前值 的两边空白
	private function getCurrentValue($value) {
		if (is_array($value)) {
			foreach ($value as &$_val) {
				$_val = trim($_val);
			}
		}else {
			$value = trim($value);
		}
		return $value;
	}
	
// 	或者
	private function checkOr($checkRule) {
		$rule = explode('|', $checkRule);
		foreach ($rule as $ruleVal) {
			if (stripos($ruleVal,'&') !== false) {
				$result = $this->checkAnd($ruleVal);
			}else {
				//取得第一个字符判断表示什么，只要有一个为真，那么则返回true
				$result = call_user_func_array(array($this,'_'.substr($ruleVal, 0,1)), array(substr($ruleVal, 1)));
			}
			if ($result) break;
		}
		return $result;
	}
// 	&&
	private function checkAnd($checkRule) {
		$rule = explode('&', $checkRule);
		foreach ($rule as $ruleVal) {
			$result = call_user_func_array(array($this,'_'.substr($ruleVal, 0,1)), array(substr($ruleVal, 1)));
			if (!$result) break;
		}
		return $result;
	}
	
// 	验证字符串
	private function _s($rule) {
		if (!is_string($this->value)) return false;
		$valueLen = mb_strlen($this->value,'UTF-8');
		return $this->checkLen($valueLen, $rule);
	}
	
// 	验证数组数据个数
	private function _l($rule) {
		if (!is_array($this->value)) return false;
		return $this->checkLen(count($this->value), $rule);
	}
	
// 	验证数字
	private function _n($rule) {
		if (!is_numeric($this->value)) return false;
		$valueLen = strlen($this->value);
		return $this->checkLen($valueLen, $rule);
	}
	
// 	验证Email
	private function _e($rule) {
		return  filter_var($this->value,FILTER_VALIDATE_EMAIL);
	}

// 	判断两值是否相等
	private function _f($rule) {
		return $this->value==trim($this->data[$rule]);
	}
	
// 	验证允许为空
	private function _a($rule) {
		return empty($this->value);
	}
	
// 	正则表达式验证
	private function _r($rule) {
		return preg_match($rule, $this->value);
	}
	
// 	验证qq
	private function _q($rule) {
		return preg_match('/^[1-9][\d]{4,10}$/',$this->value);
	}
	
// 	验证手机
	private function _t() {
		return preg_match('/^[1-9][\d]{10}$/',$this->value);
	}
	
// 	验证电话
	private function _p() {
		return preg_match('/^([\d]{3}\-[\d]{8})|([\d]{4}\-[\d]{7})$/',$this->value);
	}
	
// 	验证url
	private function _u() {
		return preg_match('/^(http|https):\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/',$this->value);
	}
	
// 	验证是否是中文
	private function _z() {
		return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $this->value);
	}
	
	/* 验证身份证 */
	private function _c() {
		$strlen = strlen($this->value);
		// 将15位身份证升级到18位
		if ($strlen === 15) {
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (array_search(substr($this->value, 12, 3), array('996', '997', '998', '999')) !== false){
				$this->value = substr($this->value, 0, 6).'18'.substr($this->value, 6, 9);
			}else{
				$this->value = substr($this->value, 0, 6).'19'.substr($this->value, 6, 9);
			}
			$this->value = $this->value .$this->idcard_verify_number($this->value);
		} 
		$idcard_base = substr($this->value, 0, 17);
		return $this->idcard_verify_number($idcard_base) == strtoupper(substr($this->value, 17, 1)) ? true : false;
	}
	
	// 计算身份证校验码，根据国家标准GB 11643-1999，主要依附于_c方法
	private function idcard_verify_number($idcard_base){
		// 　加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		// 	校验码对应值
		$verify_number_list = array('1','0','X','9','8','7','6','5','4','3','2');
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++){
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	
// 	验证长度
	private function checkLen($valueLen,$rule) {
		if (stripos($rule, ',')) {//1,1个以上
			$ruleArr = explode(',', $rule);
			return $valueLen >= $ruleArr[0];
		}elseif (stripos($rule,'-')) {//1-10，1-10个之间
			$ruleArr = explode('-',$rule);
			return $valueLen >= $ruleArr[0] && $valueLen <= $ruleArr[1];
		}else {//10，只能是10个
			return $valueLen == $rule;
		}
	}
	
	// 	返回验证信息
	private function checkInfo($status,$error = '') {
		return array($this->vailStatus=>$status,$this->vailError=>$error);
	}
	
	public static function _vail($vailStatus = '',$vailError = '') {
		if (!empty($vailStatus)) $this->vailStatus = $vailStatus;
		if (!empty($vailError)) $this->vailError = $vailError;
		return self::$vailData = new ValiData();
	}
	
}
?>