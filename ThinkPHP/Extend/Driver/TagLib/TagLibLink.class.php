<?php
class TagLibLink extends TagLibResolve {
	
	protected $tags   =  array(
		'link'=>array('attr'=>'limit','level'=>1),
	);
	
	/**
	 * 友情链接解析
	 * limit  必填， 如果是0，则么则取出全部
	 *
	 * cache 缓存 默认 false
	 * return 返回的数据值标识 置信 data
	 * tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
	 * order 默认sort DESC,save_time DESC
	 * type 链接类型 =>1(text)文字，2(image)图片  默认文字
	 * position 链接位置 => 1 首页 ,2 内页	默认 首页
	 *
	 */
	public function _link($attr,$content) {
		// 		模块未按装则跳过
		$modules = F('modules');
		if (!isset($modules['Link']) || $modules['Link']['is_disabled']==2) return ;
		$tag = $this->parseXmlAttr($attr,'link');
		//		返回的数据变量值
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		// 		默认文字链接,
		$tag['type'] = (isset($tag['type'])&&!empty($tag['type'])) ? str_replace(array('text','image'), array(1,2), $tag['type']) : $tag['type'] = 1;
		// 		置信首页链接
		$tag['position'] = (isset($tag['position'])&&!empty($tag['type'])) ? $tag['position'] : $tag['position'] = 1;
		// 		排序
		if (!isset($tag['order']) || empty($tag['order'])) $tag['order'] = 'sort DESC,save_time DESC';
		// 		缓存
		if (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache']) == 'false') $tag['cache'] = false;//缓存
		$Link = M('Link');
		$data = $Link->cache($tag['cache'])->where("link_type={$tag['type']} AND link_pos={$tag['position']} AND l_status=1")->order($tag['order'])->limit(intval($tag['limit']))->field('web_name,web_url,web_logo')->select();
		$resolve_string = Tool::arrayPrototype($data);
		unset($Link,$data);
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = "<?php  \${$tag['return']['return']}=$resolve_string;  ?>";
		return $resolve_string .= $this->addForeach($tag['return'], $content, $tag['tag']);
	}
	
}
?>