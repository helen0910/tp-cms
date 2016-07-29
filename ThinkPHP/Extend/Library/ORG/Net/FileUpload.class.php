<?php
/*
 +-------------------------------------------------------------
 | PHP文件上传类 
 +-------------------------------------------------------------
 | @filename	: FileUpload.class.php
 | @auther		: Mr.Kin
 | @date		: 2012-08-25
 +-------------------------------------------------------------
*/

class FileUpload{

		//文件保存路径
		public $saveFilePath 				= '';
		
		//文件保存路径不存在是否自动创建
		public $createFolders  				= true;
		
		//允许上传文件的扩展名(非真实Mime类型)  空数组 不限制文件扩展名
		public $allowExtType				= array('jpg', 'jpeg', 'png', 'gif');
		
		//允许上传文件的MimeType 空数组 不限制文件MimeType
		public $allowMimeType 				= array();
		
		//允许文件上传的最大尺寸
		public $maxSize 					= '2MB';
		
		//允许文件上传的最小尺寸
		public $minSize 					= 0;
		
		//允许上传图片文件的「最大」宽度和高度
		public $imgMaxWH 					= '0,0';
		
		//允许上传图片文件的「最小」宽度和高度
		public $imgMinWH 					= '0,0';
		
		//是否重命名文件
		public $isRenameFile				= true;	
		
		//重命名规则 函数名称
		public $renameRule					= NULL;
		
		//重命名规则 函数附加数据(函数第二个参数，第一个参数为文件信息fileinfo)
		public $renameRuleAppendData		= NULL;		
		//如：$renameRule = makename
		//function makename($fileinfo,jkjk)
		

		//错误信息列表
		private $errorList;
		//上传操作后返回上传信息
		private $FlieUploadReturnInfo;
		
		//构造函数
		public function __construct($uploadConfig=array()){
			//可以设置类对象属性修改上传参数 也可以数组传递 上传参数  建议使用数组方式 方便不同项目调用 可以放在配置文件中
			//数组传递时 键名不区分大小写 array('saveFilePath'=>'./upfiles'),array('savefilepath'=>'./upfiles') 效果一样 程序自动识别
			$this->_setUploadConfig($uploadConfig);
			//初始化错误信息列表
			$this->errorList = array(
									 0	=>	'上传成功',
									 1	=>	'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
									 2	=>	'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
									 3	=>  '文件只有部分被上传',
									 4  =>	'没有文件被上传',
									 6	=>	'找不到临时文件夹',
									 7	=>	'文件写入失败',
									 8	=>	'文件最大上传尺寸超过设定值',
									 9	=>	'文件最小上传尺寸低于设定值',
									 10	=>	'图片文件最大宽和高超过设定值',
									 11	=>	'图片文件最小宽和高低于设定值',
									 12	=>	'未知错误',
									 13 =>	'自动自动创建目录失败 权限不足',
									 14 =>	'上传文件保存路径没有设置',
									 15 =>	'文件扩展名不在设定值范围内',
									 16 =>	'文件MimeType不在设定值范围内',
									 17 =>	'文件保存时发生错误(拷贝重命名时)',
									 18 =>  '重命名规则函数不存在',
									 19 =>  '上传文件保存路径不存在',
									 20 =>  '表单中没有文件域',
									 
									);
				
		}
		//解析数组传递上传参数配置
		private function _setUploadConfig($uploadConfig){
			if(is_array($uploadConfig) && !empty($uploadConfig)){
				//返回所有当前类中 属性名列表 (索引数组)
				$class_vars_list = array_keys(get_class_vars(__CLASS__));
				//大小写对应数组
				$class_vars_corr = array();
				foreach($class_vars_list as $val){
						$key = strtolower($val);
						$class_vars_corr[$key] = $val;
				}
				foreach($uploadConfig as $key=>$val){
						if(property_exists($this,$class_vars_corr[strtolower($key)])){
							$this->$class_vars_corr[strtolower($key)] = $val;
						}
				}
			}			
		}
		
		//上传调用
		public function upload($fileField=''){
			//文件域检测
			if(empty($_FILES)){
					$this->_setReturnInfo(20);
					return $this->FlieUploadReturnInfo;
			}
			
			//检测文件路径
			if(!$this->verifySaveFilePath()){
				return 	$this->FlieUploadReturnInfo;
			}
			
			if(!$fileField){
				//自动上传>>无文件域名称传递 启用自动检测文件域上传
				$this->autoUpload();
				
			}else if(strstr($fileField,'!')){
				//筛选上传>>上传除了传递过来的$fileField 以外的文件域 
				$this->autoFilterUpload($fileField);
				
			}else{
				//指定上传>>仅上传指定文件域
				$this->designatedUpload($fileField);
			}
			$result = $this->FlieUploadReturnInfo;
			//清空FlieUploadReturnInfo  防止多文件配置参数不同的文件上传 保留上次上传结果
			$this->FlieUploadReturnInfo = NULL;
			return 	$result;
			
		}
		
		//保存路径检测
		private function verifySaveFilePath(){
			
			if(!$this->saveFilePath){
				//文件保存路径没有设置
					$this->_setReturnInfo(14);
					return false;
			}else{
				if(!file_exists($this->saveFilePath)){
					//文件保存路径不存在且不允许自动创建 抛出错误
					if(!$this->createFolders){
						$this->_setReturnInfo(19);
						return false;
					}
					if($this->createDir()){
						//创建目录成功
						return true;	
					}else{
						//创建目录失败
						$this->_setReturnInfo(13);
						return false;
					}
				}
				//去除末尾正反斜杠
				$this->saveFilePath = rtrim($this->saveFilePath,'/');
				$this->saveFilePath = rtrim($this->saveFilePath,'\\');
				return true;
			}				
		}
		
		//无文件域名称传递 自动检测上传
		private function autoUpload(){
			//客服端所有文件域名称集合
			$clientFileField = array_keys($_FILES);
			//逗号分隔转成可执行参数
			$clientFileFieldPara = implode(',',$clientFileField);
			//指定上传
			$this->designatedUpload($clientFileFieldPara);				
			
		}
		
		//筛选上传（仅上传除了递过来的$fileField 以外的文件域）
		private function autoFilterUpload($fileField){
			//序列文件上传 传递文件域名称不需要加[] 此处防止意外传递 替换除去
			$fileField = str_replace('[]','',$fileField);
			$fileField = str_replace('!','',$fileField);
			//不允许上传文件域名称集合
			$fileField = explode(',',$fileField);
			//客服端所有文件域名称集合
			$clientFileField = array_keys($_FILES);
			//计算差集 返回允许上传文件域名称集合
			$allowFileField = array_diff($clientFileField,$fileField);
			//逗号分隔转成可执行参数
			$allowFileFieldPara = implode(',',$allowFileField);
			//指定上传
			$this->designatedUpload($allowFileFieldPara);
			
		}		
		
		//指定文件域名称 指定上传
		private function designatedUpload($fileField){
			//序列文件上传 传递文件域名称不需要加[] 此处防止意外传递 替换除去
			$fileField = str_replace('[]','',$fileField);
			if(strstr($fileField,',')){
				$fileFieldList = explode(',',$fileField);
				foreach($fileFieldList as $v){
					$this->_oneUpload($v);		
				}
			}else{
				$this->_oneUpload($fileField);	
			}
				
			
		}
		
		//上传一个文件域名称(单个或多个)
		private function _oneUpload($fileField){
			$fileInfo = $_FILES[$fileField];
			if(is_array($fileInfo['name'])){
				$fileInfo = $this->reArrayFiles($fileInfo);
				foreach($fileInfo as $v){
					if($this->verifyFile($v)){
						$this->_saveFile($v);		
					}					
				}
			}else{
				if($this->verifyFile($fileInfo)){
					$this->_saveFile($fileInfo);		
				}
			}
			
		}
		
		//验证文件
		private function verifyFile($fileInfo){
			//如果重命名 检测文件重命名规则是否存在
			if($this->renameRule!==NULL && !function_exists($this->renameRule)){
				$this->_setReturnInfo(18);
				return false;					
			}
			if($fileInfo['error']!=0){
				////PHP错误
				$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']];
				$this->_setReturnInfo($fileInfo);
				return false;
			}else{
				////设定值不符合 错误
				
				//文件扩展名检测
				if(!$this->verifyExtType($fileInfo['name'])){
					$fileInfo['error'] = 15;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.implode(',',$this->allowExtType).']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}
				
				//文件Mime检测
				if(!$this->verifyMimeType($fileInfo['type'])){
					$fileInfo['error'] = 16;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.implode(',',$this->allowMimeType).']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}
				
				//文件上传最大尺寸检测
				if(!$this->verifyMaxSize($fileInfo['size'])){
					$fileInfo['error'] = 8;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.$this->maxSize.']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}
				
				//文件上传最小尺寸检测
				if(!$this->verifyMinSize($fileInfo['size'])){
					$fileInfo['error'] = 9;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.$this->minSize.']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}
				//图片文件上传最大宽和高检测
				if(!$this->verifyMaxWH($fileInfo['tmp_name'])){
					$fileInfo['error'] = 10;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.$this->imgMaxWH.']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}
				
				//图片文件上传最小宽和高检测
				if(!$this->verifyMinWH($fileInfo['tmp_name'])){
					$fileInfo['error'] = 11;
					$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']].' ['.$this->imgMinWH.']';
					$this->_setReturnInfo($fileInfo);
					return false;
				}													
										
			}
			
			return true;
		
		}
				
		//保存文件
		private function _saveFile($fileInfo){
			
			//取得文件原扩展名
			$fileExt = '.'.strtolower(end(explode('.',$fileInfo['name'])));
			//是否设置重命名规则
			if($this->isRenameFile){
				$newFileName 	= $this->renameRule===NULL?$this->_createFilename($fileExt):call_user_func($this->renameRule,$fileInfo['name'],$this->renameRuleAppendData).$fileExt;
			}else{
				$newFileName 	= $fileInfo['name'];
			}
			//文件保存全路径
			$fullFilePath 	= $this->saveFilePath.'/'.$newFileName;
			
			if(@move_uploaded_file($fileInfo['tmp_name'],$fullFilePath)){
				//返回信息  新文件名
				$fileInfo['new_name']  = $newFileName;
				//返回信息  文件保存路径
				$fileInfo['save_path'] = $this->saveFilePath;
				$this->_setReturnInfo($fileInfo);
			}else{
				$fileInfo['error'] = 17;
				$fileInfo['errorMsg'] = $this->errorList[$fileInfo['error']];
				$this->_setReturnInfo($fileInfo['error']);
			}
		}
		
		//扩展名检测
		private function verifyExtType($fileName){
			//如果验证Ext列表为空则不验证
			if(empty($this->allowExtType)) return true;
			$fileExt = strtolower(end(explode('.',$fileName)));
			$this->allowExtType = array_filter($this->allowExtType,'strtolower');
			if(in_array($fileExt,$this->allowExtType)){
				return true;	
			}else{
				return false;	
			}
		}
		
		//Mime检测
		private function verifyMimeType($fileMime){
			//如果验证Mime列表为空则不验证 默认为空 不验证
			if(empty($this->allowMimeType)) return true;
			if(in_array($fileMime,$this->allowMimeType)){
				return true;	
			}else{
				return false;	
			}
		}
		
		//上传最大尺寸检测
		private function verifyMaxSize($fileSize){
			//获取文件允许大小 Bytes单位
			$allowMaxSize = $this->sizeToBytes($this->maxSize);
			//值为零时不限制 最大尺寸
			if($allowMaxSize==0 || $fileSize<=$allowMaxSize){
				return true;	
			}else{
				return false;		
			}
		}
		
		//上传最小尺寸检测
		private function verifyMinSize($fileSize){
			//获取文件允许大小 Bytes单位
			$allowMinSize = $this->sizeToBytes($this->minSize);
			//值为零时不限制 最小尺寸
			if($allowMinSize==0 || $fileSize>=$allowMinSize){
				return true;	
			}else{
				return false;		
			}
		}
		
		//图片最大宽和高检测
		private function verifyMaxWH($tmpFileName){
			if($this->imgMaxWH=='0,0') return true;
			$imgWH = explode(',',$this->imgMaxWH);
			$imgFileInfo = @getimagesize($tmpFileName);
			if($imgFileInfo){
				if($imgWH[0]>=$imgFileInfo[0] && $imgWH[1]>=$imgFileInfo[1]){
					return true;	
				}else{
					return false;	
				}
			}
			return true;
		}
		
		//图片最小宽和高检测
		private function verifyMinWH($tmpFileName){
			if($this->imgMinWH=='0,0') return true;
			$imgWH = explode(',',$this->imgMinWH);
			$imgFileInfo = @getimagesize($tmpFileName);
			if($imgFileInfo){
				if($imgWH[0]<=$imgFileInfo[0] && $imgWH[1]<=$imgFileInfo[1]){
					return true;	
				}else{
					return false;	
				}
			}
			return true;
		}		

		
		
		//设置返回信息
		private function _setReturnInfo($fileInfo){
			if(!is_array($fileInfo)){
				$this->FlieUploadReturnInfo = array('error'=>$fileInfo,'errorMsg'=>$this->errorList[$fileInfo]);	
			}else{
				$this->FlieUploadReturnInfo[] = $fileInfo;
			}
			
		}
		
		//带单位尺寸转字节 Bytes
		private function sizeToBytes($size){
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
	
	//整理多文件$_FILES 数组
	private function reArrayFiles(&$file_post) {
	   $file_ary = array();
	   $file_count = count($file_post['name']);
	   $file_keys = array_keys($file_post);
	
	   for ($i=0; $i<$file_count; $i++) {
		   foreach ($file_keys as $key) {
			   $file_ary[$i][$key] = $file_post[$key][$i];
		   }
	   }
	
	   return $file_ary;
	}
	
	//创建上传目录
	private function createDir(){
		$result = @mkdir($this->saveFilePath,0777,true);
		@chmod($this->saveFilePath,0777);
		return $result;
	}
	
	//默认上传文件命名规则
	private function _createFilename($fileExt){
		$fileName = md5(uniqid(rand(), true)).$fileExt;
		return  $fileName;
	}
	
}


?>