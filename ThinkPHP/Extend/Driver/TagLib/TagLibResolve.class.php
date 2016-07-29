<?php
/**
 * 系统内置SQL解析和公共方法
 * 
 *
 */
class TagLibResolve extends TagLib {
	
	
	protected $tags   =  array(
		'sql'=>array('attr'=>'sql','level'=>3),
		'single'=> array('attr'=>'table','close'=>0)
	);
	
	/**
	 * SQL	全能查询，只支持SELECT
	 * 参数
	 * type list,page，默认为list    都不填时那么则必须要有sql属性
	 * return 返回数据的值默认为data|values
	 * tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
	 * 两种SQL选择，原生态SQL，
	 * 列表形式  sql="SELECT * FROM $prefixarticle"
	 * 分页形式  支持动态，伪静态
	 * table,field,where,order,limit   不支持groupby
	 * sql 只适用于List类型  支持PHP，PHP语法需要使用{}并且不可使用双引号
	 * 支持解析 $cid,
	 * 表前缀    $prefix
	 * limit page 时，只能填充一个数值
	 * @param array $attr
	 * @param string $content
	 */
	public function _sql($attr,$content) {
		$tag = $this->parseXmlAttr($attr,'sql');
		if (!isset($tag['type']) || empty($tag['type'])) $tag['type'] = 'list';
		/* 基本数据设置 */
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';//返回的数据变量值
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		$tag['cache'] = (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache'])=='false') ? false : true;//缓存
		/* where条件过滤 */
		if (isset($tag['where']) && !empty($tag['where'])) {
			$tag['where'] = $this->replaceVar($tag['where']);
		} elseif (isset($tag['sql']) && !empty($tag['sql'])) {
			$tag['sql'] = $this->replaceVar($tag['sql']);
		}
		$param_tag = Tool::arrayPrototype($tag);
		$param_tag = $this->resolveVar($param_tag);
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = <<<str
		<?php
			import('ORG.TagLib.Resolve');
			\$contentClass = new Resolve();
			\$contentClass->tags = $param_tag;
			\${$tag['return']['return']} = \$contentClass->_sql();
		?>
str;
		return $resolve_string .= $this->addForeach($tag['return'],$content,$tag['tag'],$tag['type'] == 'page' ? true : false);
	}
	
	/**
	 * 单条数据查询，
	 * cache 默认则为true,和其它的有区别
	 * where
	 * table
	 * field 最好使用单字段，多字段只起到一个赋值的作用，必须使用$single才可以使用  强烈建议一次取出全部，然后使用$single读取
	 * return single  可能一个页面要用到多次，所以可以使用不同的变量区别上面的data
	 * @param unknown $attr
	 */
	public function _single($attr) {
		$tag = $this->parseXmlAttr($attr,'single');
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'single';//返回的数据变量值
		$tag['cache'] = (isset($tag['cache']) && (strtolower($tag['cache'])=='false' || $tag['cache']=='0')) ? false : true;//缓存
		if ($tag['where']) $tag['where'] = $this->replaceVar($tag['where']);
		$param_tag = Tool::arrayPrototype($tag);
		$param_tag = $this->resolveVar($param_tag);
		//处理return标识
		$resolve_string = <<<str
		<?php
			import('ORG.TagLib.Resolve');
			\$contentClass = new Resolve();
			\$contentClass->tags = $param_tag;
			\${$tag['return']} = \$contentClass->_single();
			if(!empty(\$contentClass->tags['field']) && stripos(\$contentClass->tags['field'], ',')===false && stripos(\$contentClass->tags['field'], '*')===false) {
				echo \${$tag['return']}[\$contentClass->tags['field']];
			}
		?>
str;
		return $resolve_string;
	}
	
	/**
	 * 处理返回值标识
	 * @param array $return
	 * @return multitype:string unknown Ambigous <string, unknown>
	 */
	protected function handleReturn($return) {
		$returnArray = array();
		if (strpos($return, '|') === false) {
			$returnArray['return'] = $return;
			$returnArray['item'] = 'values';
		} else {
			$tempArray = explode('|', $return);
			$returnArray['return'] = empty($tempArray[0]) ? 'data' : $tempArray[0]; 
			$returnArray['item'] = empty($tempArray[1]) ? 'values' : $tempArray[1]; 
		}
		return $returnArray;
	}
	
	/**
	 * 添加 TP 内置foreach标签查询
	 * @param array $returnData
	 * @param string $content
	 * @param string $tag
	 * @param boolean $ispage
	 */
	protected function addForeach($returnData,$content,$tag,$ispage = false) {
		//判断是否分页
		if ($ispage) {
			$page = '$page=$'.$returnData['return']."['pageinfo']";
			$returnData['return'] = $returnData['return']."['page']";
		}
		//选择标签内容，foreach,volist
		if ($tag == 'foreach') {
			$string = '<?php $i=1;?><foreach name="'.$returnData['return'].'" item="'.$returnData['item'].'">'.$content.'<?php $i++; ?></foreach>';
		} else {
			$string = $content;
		}
		$string .= '<?php '.(isset($page)  ? $page : '').';?>';//unset($'.$returnData.');不作销毁
		return $this->tpl->parse($string);
	}

	/**
	 * 变量替换
	 * @param string $string
	 * @return string
	 */
	protected function replaceVar($string) {
		$string = str_ireplace(
			array('eq','neq','egt','elt','gt','lt','$cid','$aid','$prefix','$time','$userid'),
			array('=','<>','>=','<=','>','<',intval($_GET['cid']),intval($_GET['aid']),DB_PREFIX,NOW_TIME,intval(GBehavior::$session['id'])),
			$string);
		return $string;
	}
	
	/**
	 * 解析变量
	 * @param string $string
	 * @return mixed
	 */
	protected function resolveVar($string) {
		if (strpos($string, '#') !== false || strpos($string, '{') !== false || strpos($string, '}') !== false) {
			$string = preg_replace('/(\#|\{)(.*)(\#|\})/U',"'.($2).'", $string);
		}
		return str_replace(array("\\'.",".\\'","[\\'","\\']"), array("'.",".'","['","']"), $string);
	}
}
?>