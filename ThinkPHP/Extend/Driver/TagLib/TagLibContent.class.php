<?php
/**
 * 内容模型解析
 * 
 *
 */
class TagLibContent extends TagLibResolve {
	
	protected $tags   =  array(
		'content'=>array('attr'=>'type','level'=>3),
		'navigate'=>array('attr'=>'type','level'=>1),
		'position'=>array('attr'=>'d','close'=>0)
	);
	
	/**
	 * 当前位置
	 * @param array $attr
	 * @return string
	 */
	public function _position($attr) {
		$tag = $this->parseXmlAttr($attr,'position');
		$tag['d'] = $tag['d'] ? $tag['d'] : '&gt;';
		$resolve_string = <<<str
		<?php 
		import('ORG.TagLib.Contents');
		\$Contents = new Contents();
		echo \$Contents->getCurrentPosition(intval(\$_GET['cid']),'{$tag['d']}'); 
		?>
str;
		return $resolve_string;
	}
	
	/**
	 * 注意，这里并未使用缓存导航，而是从库中读取，也许欠考虑
	 * 导航解析
	 *
	 * $cid 当前navigate_id   当为数字时，表示拿出指定的cid此时type无效，可同时有多个type
	 * type  all 所有  不需要$cid  默认为all
	 * 		 son 子级  需要$cid	下一级
	 * 		 sib 同级  需要$cid	
	 * 		 prt 父级  需要$cid	只是上一级
 	 *  return 	
 	 *  tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
 	 *  shide 默认true 不显示隐藏的
	 * field  默认id,pid,navigate_name,url
	 * limit  默认10
	 * order  默认sort DESC,save_time DESC
	 * cache  默认false
	 * 注意：导航解析没有where属性
	 * 
	 * 
	 * @param array $attr
	 * @param string $content
	 * @return void|unknown
	 */
	public function _navigate($attr,$content) {
		$tag = $this->parseXmlAttr($attr,'navigate');
		if ((!isset($tag['type']) || empty($tag['type'])) && $tag['cid']=='$cid') return ;//类型
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';//返回的数据变量值
		if (!isset($tag['field']) || empty($tag['field'])) $tag['field'] = 'id,pid,navigate_name,url';
		if (!isset($tag['order']) || empty($tag['order'])) $tag['order'] = 'sort DESC,save_time DESC';//排序
		if (!isset($tag['limit']) || empty($tag['limit'])) $tag['limit'] = 10;//行数
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		$tag['shide'] = (!isset($tag['shide']) || (strtolower($tag['shide'])!='false' && $tag['shide']!='0')) ? true : false;//是否显示隐藏的栏目
		$tag['cache'] = (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache'])=='false') ? false : true;//缓存
		$shide = $tag['shide'] ? ' AND is_show=1' : '';
		$Navigate = M('Navigate');
		if ($tag['type']) {
			switch ($tag['type']) {
				case 'all':
					$navigateData = $Navigate->cache($tag['cache'])->where("c_status=1 $shide")->order($tag['order'])->limit($tag['limit'])->field($tag['field'])->select();
					$Tree = new Tree();
					$Tree->init($navigateData);
					$navigateData = $Tree->get_tree_array(0);
					break;
				case 'son':
					$cid = intval($_GET['cid']);
					$navigateData = $Navigate->cache($tag['cache'])->where("pid=$cid AND c_status=1 $shide")->order($tag['order'])->limit($tag['limit'])->field($tag['field'])->select();
					break;
				case 'sib':
					$navigate = F('navigate');
					$cid = intval($_GET['cid']);
					$current = $navigate[$cid];
					$navigateData = $Navigate->cache($tag['cache'])->where("pid={$current['pid']} AND c_status=1 $shide")->order($tag['order'])->limit($tag['limit'])->field($tag['field'])->select();
					break;
				case 'prt':
					$navigate = F('navigate');
					$cid = intval($_GET['cid']);
					$current = $navigate[$cid];
					$navigateData = $Navigate->cache($tag['cache'])->where("id={$current['pid']} AND c_status=1 $shide")->order($tag['order'])->limit($tag['limit'])->field($tag['field'])->select();
					break;
			}
		} else {//指定CID
			$navigateData = $Navigate->cache($tag['cache'])->where("id IN({$tag['cid']}) AND c_status=1 $shide")->order($tag['order'])->limit($tag['limit'])->field($tag['field'])->select();
		}
		$params = Tool::arrayPrototype($navigateData);
		unset($navigateData);
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = "<?php \${$tag['return']['return']}=$params;?>";
		return $resolve_string .= $this->addForeach($tag['return'],$content,$tag['tag']);
	}
	
	/**
	 * 内容解析标签
	 * 
	 * type  list , page  
	 * 			,relevant,down,images 内容子页
	 * 			默认list
	 * 选填
	 * return 	返回的数据值标记 默认为 data
	 * tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
	 * cache    是否开启缓存  默认false
	 * 
	 * $cid 代表当前栏目id  
	 * $mid 代表当前栏目模型id   可选  当mid存在时，则优先取mid  若此栏目下要想取回的模型，则mid设置为常数即可，$mid只代表当前栏目下的模型
	 * 列表时   cid,mid 两者必须取其一  并且mid必须为常数不可为$mid或者当cid存在时可用$mid
	 * 
	 * field  为空时 id,cid,thumbnail,title,style,save_time,url,click
	 * where  条件中则不可以再写cid或mid，a_status,is_delete 系统会自动添加 
	 * 		     如果where 为空那么则自动添加条件 a_status=1 AND is_delete=1
	 * 			当 type为relevant无where条件
	 * 
	 * $aid  relevant,down,images   类型特有，如果为空或不存在，则当前aid
	 * rfield  需要查询处理的字段  relevant,down,images  类型 解析字段
	 * 
	 * order  默认sort DESC,save_time DESC
	 * limit  默认10
	 * 
	 * tbdt 无缩略图时是否显示默认   默认true   ,false
	 * tbpx 缩略图默认前缀  small_
	 * 
	 * scm 显示评论数 true,false 默认 false  废弃
	 * 
	 * $mname  用户名  废弃
	 * $aname  管理员名 废弃
	 * 
	 * 内容解析标签
	 * @param array $attr
	 * @param string $content
	 * @return void|unknown
	 */
	public function _content($attr,$content) {
		$tag = $this->parseXmlAttr($attr,'content');
		if (!isset($tag['type']) || empty($tag['type'])) $tag['type'] = 'list' ;//默认为列表类型
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';//返回的数据变量值
		if (!isset($tag['field']) || empty($tag['field'])) $tag['field'] = 'id,cid,thumbnail,title,style,save_time,url,click';
		if (!isset($tag['order']) || empty($tag['order'])) $tag['order'] = 'sort DESC,save_time DESC';//排序
		if (!isset($tag['limit'])) $tag['limit'] = 10;//行数
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		$tag['cache'] = (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache'])=='false') ? false : true;//缓存
		$tag['tbdt'] = (!isset($tag['tbdt']) || (strtolower($tag['tbdt'])!='false' && $tag['tbdt']!='0')) ? true : false;//是否显示默认缩略图
		if (!isset($tag['tbpx']) || empty($tag['tbpx'])) $tag['tbpx'] = 'small_';//显示裁剪后的缩略图
		//替换where条件
		if (isset($tag['where']) && !empty($tag['where'])) $tag['where'] = $this->replaceVar($tag['where']);
		$param_tag = Tool::arrayPrototype($tag);
		$param_tag = $this->resolveVar($param_tag);
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = <<<str
		<?php
			import('ORG.TagLib.Contents');
			\$contentClass = new Contents();
			\$contentClass->tags = $param_tag;
			\${$tag['return']['return']} = \$contentClass->_{$tag['type']}();
		?>
str;
		return $resolve_string .= $this->addForeach($tag['return'],$content,$tag['tag'],$tag['type'] == 'page' ? true : false);
	}
	
}
?>