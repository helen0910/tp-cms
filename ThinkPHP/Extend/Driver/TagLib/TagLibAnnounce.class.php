<?php
class TagLibAnnounce extends TagLibResolve {
	
	protected $tags   =  array(
		'announce'=>array('attr'=>'type','level'=>1),
	);
	
	/**
	*
	*
	*	 type  list ,page
	*	 return 返回的变量数据
	*	 order = 'sort DESC,save_time DESC'
	*	 cache  默认为false
	*	 tag   内部再次解析 的标签，默认为foreach  i 为每次累加数据，默认从1开始
	*	 atype 1公共 2会员 默认为1
	*	 show => all 所有 read unread  默认为all   =>只对会员有效
	*	 
	*	 field  'id,title,style,start_time,click,ann_type,end_time,save_time,link';
	*	 where start_time <= $now_time AND end_time >= $now_time AND ann_type=$atype AND a_status=1
	*/
	
	public function _announce($attr,$content) {
		$tag = $this->parseXmlAttr($attr,'announce');
		if (!isset($tag['type']) || empty($tag['type'])) $tag['type']='list' ;//类型
		if (!isset($tag['return']) || empty($tag['return'])) $tag['return'] = 'data|values';//返回的数据变量值
		if (!isset($tag['limit']) || empty($tag['limit'])) $tag['limit'] = 10;//行数
		if (!isset($tag['atype']) || empty($tag['atype'])) $tag['atype'] = 1;//公共
		if (!isset($tag['tag']) || empty($tag['tag'])) $tag['tag'] = 'foreach';//内置TP解析标签
		$tag['cache'] = (!isset($tag['cache']) || empty($tag['cache']) || strtolower($tag['cache'])=='false') ? false : true;//缓存
		$tag['order'] = 'sort DESC,save_time DESC';//排序
		$tag['field'] = 'id,title,style,start_time,click,ann_type,end_time,save_time,link';
		$now_time = NOW_TIME;
		$tag['where'] = "start_time<=$now_time AND end_time>=$now_time AND ann_type={$tag['atype']} AND a_status=1";
		$Announce = M('Announce');
		if ($tag['type'] == 'list') {
			$data = $Announce->where($tag['where'])->cache($tag['cache'])->order($tag['order'])->limit($tag['limit'])->getField($tag['field']);
		} else {
			$total = $Announce->cache($tag['cache'])->count(1);
			$Page = new Page($total,'',$tag['limit']);
			$data = $Announce->where($tag['where'])->cache($tag['cache'])->order($tag['order'])->limit($Page->limit())->getField($tag['field']);
			$page = $Page->show('first','prev','list','next','last');
		}
		unset($Announce,$Page);
		if ($data) {
			$config = require APP_PATH.'Modules/Modules/Conf/Announce/Config.php';
			foreach ($data as &$values) {
				/* a链接title */
				$values['a_title'] = $values['title'];
				/* 标题样式	 */
				if (isset($values['style'])) {
					$style = explode('|', $values['style']);
					$style_string = '';
					if (!empty($style[0])) $style_string .= "color:{$style[0]};";
					$style_string .= empty($style[1]) ? 'font-weight:normal' : "font-weight:bold;";
					$values['title'] = "<b style='$style_string'>{$values['title']}</b>";
				}
				$values['url'] = isset($values['link']) && !empty($values['link']) ? $values['link'] : __ROOT__.'/'.str_replace('{$aid}', $values['id'], $config['url_'.$config['url_type']]);
			}
		}
		$params = Tool::arrayPrototype($data);
		if ($tag['type'] == 'list') {
			$newParams = $params;
		} else {
			$newParams = 'array(\'page\'=>'.$params.',\'pageinfo\'=>\''.$page.'\')';
		}
		//处理return标识
		$tag['return'] = $this->handleReturn($tag['return']);
		$resolve_string = "<?php \${$tag['return']['return']}=$newParams;?>";
		return $resolve_string .= $this->addForeach($tag['return'],$content,$tag['tag'],$tag['type']=='page' ? true : false);
	}
	
	
}
?>