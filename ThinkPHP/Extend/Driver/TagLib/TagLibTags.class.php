<?php
class TagLibTags extends TagLibResolve {
	
	protected $tags   =  array(
		'tags'=>array('attr'=>'limit','level'=>1),
	);
	
		/**
	 * Tag标签调用
	 *limit  		解析的行数  0为取出全部
	 * 公共标签
	 *	 	
	 * 		return 		返回的数据值标记
	 * 		order 		排序
	 * 		cache 		是否缓存，0(false)或1(true)
	 * 		style 0(false)或1(true) 标题是否添加样式  默认有样式
	 * 注：Tag标签无where条件
	 * 		tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
	 * 		list field 默认：id,tag_name,style，同时会自动生成url
	 * @param Attsy $attr
	 * @param string $content
	 */
	public function _tags($attr,$content) {
		/* 模块未按装则跳过 */
		$modules = F('modules');
		if (!isset($modules['Tags']) || $modules['Tags']['is_disabled']==2) return ;
		/* 解析tags */
		$tag = $this->parseXmlAttr($attr,'tags');
		/* 设置默认值 */
		if (!isset($tag['type']) || empty($tag['type'])) $tag['type'] = 'list';//展示类型
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';//返回的数据变量值
		if (!isset($tag['order']) || empty($tag['order'])) $tag['order'] = 'sort DESC';//排序
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		if (!isset($tag['field']) || empty($tag['field'])) $tag['field'] = 'id,tag_name,style';//
		$tag['cache'] = (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache'])=='false') ? false : true;//缓存
		$tag['tbdt'] = (!isset($tag['tbdt']) || (strtolower($tag['tbdt'])!='false' && $tag['tbdt']!='0')) ? true : false;//是否显示默认缩略图
		if (!isset($tag['tbpx']) || empty($tag['tbpx'])) $tag['tbpx'] = 'small_';//显示裁剪后的缩略图
		$param_tag = Tool::arrayPrototype($tag);
		$param_tag = $this->resolveVar($param_tag);
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = <<<str
		<?php
			import('ORG.TagLib.Tags');
			\$contentClass = new Tags();
			\$contentClass->tags = $param_tag;
			\${$tag['return']['return']} = \$contentClass->_{$tag['type']}();
		?>
str;
		return $resolve_string .= $this->addForeach($tag['return'],$content,$tag['tag'],($tag['type'] == 'page'||$tag['type'] == 'cntpage') ? true : false);
	}
	
	
}
?>