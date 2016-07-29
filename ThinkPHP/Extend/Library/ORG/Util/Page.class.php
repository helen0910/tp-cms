<?php
/**
 * PAGE 分页类
 * 
 *	 $PAGE = new Page($total['cid'],102);
 *	 $PAGE->setList('inner',5,10)
 *	 ->prev('<div><a href="#">上一页</a><div>','<div>上一页</div>')
 *	 ->input('跳转到','<div>{INPUT}</div>')
 *	 ->select('=={PAGE}==','<div>{SELECT}</div>')
 *	 ->list('<div><a href="#">-L{PAGE}-</a></div>','<div><a href="#">|{PAGE}|</a></div>','<div><a href="#">=R{PAGE}=</a></div>')
 *	 ->first('<div><a href="#">第一页</a></div>','<div>第一页</div>')
 *	 ->last('<div><a href="#">最后一页</a></div>')
 *	 ->next('<div><a href="#">下一页</a><div>','<div>下一页</div>')
 *	 ->other('<div>共{SUM}页，当前{PAGE}页，共{TOTAL}条，每页{LIMIT}条，当前页从{START}条-{END}条</div>')
 *	 ->show();
 *
 */
final class Page {
	
	private $total = 0;//分页总数
	private $limit;//每页分页的条数
	private $url ;//需要分页的地址
	private $page = '';//当前页数
	private $pageVar = '';//分页参数变量标识默认为page
// 	private $listNum = 2;
	private $listType = 2; //1只显示左边2左右都显示3只显示右边
	private $leftListNum = 2;//分页左边列表显示个数
	private $rightListNum = 2;//分页右边列表显示个数
	private $showSelf = true;//是否显示自身列表
	private $pageTotal;//分页总数
	private $options = array('first'=>'','prev'=>'','next'=>'','last'=>'','list'=>'','setlist'=>'','select'=>'','input'=>'','other'=>'');//样式参数，使用连贯操作
	public function __construct($total,$url = '',$limit = 20,$pageVar = 'page') {
		//数据总条数
		$this->total = $total;
// 		pageVar
		$this->pageVar = $pageVar;
// 		url
		$this->url = empty($url) ? __ACTION__."/{$this->pageVar}/{PAGE}" : $url;
// 		分页数
		$this->limit = intval($limit);
// 		总的分页数
		$this->pageTotal = ceil($this->total/$this->limit);
		// 		获取分页
		if (!isset($_GET[$this->pageVar]) || empty($_GET[$this->pageVar]) || !preg_match('/^[1-9][\d]*$/', $_GET[$this->pageVar])) {
			$this->page = 1;
		}else {
			// 			可能要进一法，也可能要舍一法，也可能要四舍五入，这里先这样做
			$this->page = intval($_GET[$this->pageVar]);
			// 			如果Page>总页数，那么设置默认为第一页
			if ($this->page > $this->pageTotal) $this->page = 1;
		}
	}
	
	/**
	 * 设置url
	 * @param string $url
	 * @param number $type 1 ,2
	 * @param string $urlSuffix 伪静态扩展名
	 * @param string $delimiter 动态分页分隔符
	 * @return Page
	 */
// 	private function _seturl($url = '') {
// // 		{PAGE}传入自定义分布标识符
// 		if (empty($url)) $url = __ACTION__."/{$this->pageMark}/{PAGE}";
// 		$this->url = $url;
// 		return $this;
// 	}
	
	/**
	 * 用于连贯操作设置
	 * @param string $method -> method Name
	 * @param array $arguments -> arguments Array
	 */
	public function __call($method,$arguments) {
		
		$allowVal = array_keys($this->options);
		if (in_array(strtolower($method),$allowVal,true)) {
			if ($method == 'first' || $method == 'prev' || $method == 'last' || $method == 'next') {
				$newMothod = '_firstPrevAndNextLast';
				//自动添加一个参数，值为其方法名
				$argusNum = count($arguments);
				$arguments[$argusNum] = $method;
			}else{
				$newMothod = '_'.$method;
			}
			
			return call_user_func_array(array($this,$newMothod), $arguments);
		}
	}
	
	public function limit() {
		return ($this->page-1)*$this->limit.','.$this->limit;
	}
	

/**
 * =============连贯操作类型开始==================
 */	
	
/**
 * first,prev,last,next四个操作
 * @param 1=>链接状态时的样式 如
 * @param 2=>非链接状态时的样式(可填)
 * 链接地址以#代替
 * 操作样式 => first('<div><a href="#">上一页</a></div>','<div><span>上一页</span></div>');
 */	
	private function _firstPrevAndNextLast() {
		$funcArgusAll = func_get_args();
// 		将数组的最后一值弹出，
		$type = array_pop($funcArgusAll);
// 		判断分页的参数类型
// 		是首页或者上一页但是page 在第一页以前
		if (($type == 'first') || ($type == 'prev' && $this->page <= 1)) {
			$page = 1;
// 			上一页page页数大于1
		}elseif ($type == 'prev' && $this->page > 1){
			$page = $this->page-1;
// 			最后一页或者是下一页page大于等于最后一页
		}elseif (($type == 'last') || ($type == 'next' && $this->page >= $this->pageTotal)){
			$page = $this->pageTotal;
		// 			下一页,page页数大于最后一页
		}elseif ($type == 'next' && $this->page < $this->pageTotal) {
			$page = $this->page+1;
		}
		
		$page_url = str_replace('{PAGE}', $page, $this->url);
		
// 		取得每个每个参数的样式
		$this->options[$type] = $funcArgusAll[0];
// 		如果第二个参数存在，1、当是第一页的时候，2、当是最后一页的时候，3，当上一页并且分页数<=1，当时最后一页并且分页数>=最后一页
		if (isset($funcArgusAll[1]) && (($this->page <= 1 && ($type == 'first' || $type == 'prev')) || ($this->page >= $this->pageTotal && ($type == 'last' || $type == 'next')))) {
			$this->options[$type] = $funcArgusAll[1];
// 		如果第二个参数存在1、当是第一页的时候page>1页，2、当是最后一页，page<最后一页，3、上一页并且page<最后一页4、下一页并且page<最后一页
		}elseif (isset($funcArgusAll[1]) && (($this->page > 1 && ($type == 'first' || $type == 'prev')) || ($this->page < $this->pageTotal && ($type == 'last' || $type == 'next')))) {
			$this->options[$type] = str_replace('#', $page_url,$this->options[$type] );
// 		如果第二个参数不存在，1、当是第一页的时候，2、当是最后一页的时候，3，当上一页并且分页数<=1，当时最后一页并且分页数>=最后一页
		}elseif (!isset($funcArgusAll[1]) && (($this->page <= 1 && ($type == 'first' || $type == 'prev')) || ($this->page >= $this->pageTotal && ($type == 'last' || $type == 'next')))){
			$this->options[$type] = str_replace('#', 'javascript:;',$this->options[$type]);
// 			$this->options[$type] = preg_replace('/^(\<[a-z]+)/', '\1 class="current" ', $this->options[$type]);
// 		如果第二个参数不存在1、当是第一页的时候page>1页，2、当是最后一页，page<最后一页，3、上一页并且page<最后一页4、下一页并且page<最后一页
		}elseif(!isset($funcArgusAll[1]) && (($this->page > 1 && ($type == 'first' || $type == 'prev')) || ($this->page < $this->pageTotal && ($type == 'last' || $type == 'next')))) {
			$this->options[$type] = str_replace('#', $page_url,$this->options[$type]);
		}
		return $this;
	}
	
	/**
	 * 设置列表栏目个数
	 * @param 1=>列表显示的格式，参数值 left(1) || inner(2) || right(3)
	 * @param 2=>左边列表的个数
	 * @param 3=>右边列表的个数(如果不填，那么则表示和左边的一样)
	 * @param 4=>是否显示自身列表
	 * setList('left'(=>1),3,5,false)
	 */
	private function _setList() {
// 		得到所有列表数
		$funcArgusAll = func_get_args();
// 		判断显示格式
		if (isset($funcArgusAll[0]) && !empty($funcArgusAll[0])) {//如果设置了列表显示的类型，
// 			扩展列表栏目设置
			$type = strtolower($funcArgusAll[0]);
			switch ($type) {
				case 'left':
					$this->listType = 1;
					break;
				case 'inner':
					$this->listType = 2;
					break;
				case 'right':
					$this->listType = 3;
					break;
				default:
					$this->listType = $type;
					break;
			}
		}
// 		如果第二个参数存在
		if (isset($funcArgusAll[1]) && !empty($funcArgusAll[1])) $this->leftListNum = $funcArgusAll[1];
		//如果第三个参数存在，
		if (isset($funcArgusAll[2]) && !empty($funcArgusAll[2])) {
			$this->rightListNum = $funcArgusAll[2];
		}else{
// 			如果不存在，则默认添加第二个列表
			$this->rightListNum = $this->leftListNum;
		}
		if (isset($funcArgusAll[3])) $this->showSelf = $funcArgusAll[3];
		return $this;
	}
	
	/**
	 * 设置列表显示格式
	 * @param 1=>左边列表格式
	 * @param 2=>自身列表格式
	 * @param 3=>右边列表格式(如果不填，那么则自动与左边相同)
	 * list(<div><a href="#">-L{PAGE}-</a></div>,<div><a href="#">|{PAGE}|</a></div>,<div><a href="#">=R{PAGE}=</a></div>)
	 */
	private function _list() {
		// 		得到所有列表数
		$funcArgusAll = func_get_args();
// 		如果右边列表不存在，那么则和左边列表相同
		if (!isset($funcArgusAll[2])) $funcArgusAll[2] = $funcArgusAll[0];
		$pageListPrev = '';
// 		判断是否显示左边=>如果值为右边则不显示左边
		if ($this->listType != 3) {
			for ($i=1;$i<=$this->leftListNum;$i++) {
				$pageListNum = $this->page-($this->leftListNum-$i+1);
				if ($pageListNum > 0) $pageListPrev .= str_replace('#', str_replace('{PAGE}', $pageListNum, $this->url), str_replace('{PAGE}', $pageListNum, $funcArgusAll[0]));//.$pageListNum.$this->urlSuffix
			}
		}
// 		判断是否显示自身
		if ($this->showSelf) {
			if (isset($funcArgusAll[1]) && !empty($funcArgusAll[1])) {
				$pageListSelf = str_replace('{PAGE}', $this->page, $funcArgusAll[1]);
			}else{
				$pageListSelf = '<span class="selfList">'.$this->page.'</span>';
			}
		}else{
			$pageListSelf = '';
		}
		$pageListNext = '';
// 		判断是否显示左边=>如果值为左边则不显示右边
		if ($this->listType != 1) {
			for ($i=1;$i<=$this->rightListNum;$i++) {
				$pageListNum = $this->page+$i;
				if ($pageListNum <= $this->pageTotal) $pageListNext .= str_replace('#', str_replace('{PAGE}', $pageListNum, $this->url), str_replace('{PAGE}', $pageListNum, $funcArgusAll[2]));//.$pageListNum.$this->urlSuffix
				
			}
		}
		if ($this->page <= 1) {
			$this->options['list'] .= $pageListSelf.$pageListNext;
		}elseif ($this->page >= $this->pageTotal){
			$this->options['list'] .= $pageListPrev.$pageListSelf;
		}else{
			$this->options['list'] .= $pageListPrev.$pageListSelf.$pageListNext;
		}
		return $this;
	}
	

/**
 * select 跳转  当数据量大时，会严重影响速度，所以===============废弃
 * @param 1=>option的跳转文字提示  => {PAGE}
 * @param 2=>外层加上一个html标签
 * 示例
 * select('=={PAGE}==','<div class="select">{SELECT}</div>')
 */	
	public function _select() {
		$funcArgusAll = func_get_args();
		$this->options['select'] .= "<select onchange='window.location=\"{$this->url}\"+this.value'>";
		for ($i=1;$i<=$this->pageTotal;$i++) {
			$selected =  ($i==$this->page) ? 'selected="selected"' : '';
			if (isset($funcArgusAll[0])) {
				$this->options['select'] .= "<option value='{$i}' {$selected}>".str_replace('{PAGE}', $i, $funcArgusAll[0])."</option>";
			}else{
				$this->options['select'] .= "<option value='{$i}' {$selected}>{$i}</option>";
			}
		}
		$this->options['select'] .='</select>';
		
// 		如果第二个参数存在(html存在)
		if (isset($funcArgusAll[1])) $this->options['select'] = str_replace('{SELECT}', $this->options['select'], $funcArgusAll[1]);
		
		return $this;
	}
	
	/**
	 * input跳转
	 * @param 1=>跳转文字提示
	 * @param 2=>外层加上一个html标签
	 * 示例
	 * input('快速跳转','<div>{INPUT}</div>')
	 */
	private function _input() {
		$funcArgusAll = func_get_args();
		
		$prompt = isset($funcArgusAll[0]) ?  $funcArgusAll[0] : '跳转';
		
		$this->options['input'] = '<input type="text" name="Val" class="valLocation" value="'.$this->page.'" onclick="this.value=\'\'" id="locationVal" /><input type="button" value="'.$prompt.'" class="locationBut" id="locationId" onclick="javascript:var _thisId = document.getElementById(\'locationVal\');window.location.href=\''.str_replace('{PAGE}', '\'+_thisId.value+\'', $this->url).'\'" />';
// 		如果设置了外层
		if (isset($funcArgusAll[1])) $this->options['input'] =  str_replace('{INPUT}', $this->options['input'], $funcArgusAll[1]);

		return $this;
	}
	
	/**
	 * 其它显示参数设置(排序任意)
	 * @param {PAGE} => 当前第几页
	 * @param {TOTAL} => 共多少条
	 * @param {LIMIT} 每页多少条
	 * @param {START} 当前页第几条
	 * @param {END} 到当前页最后条
	 * @param {SUM} 共多少页
	 * @param 
	 * 示例
	 * other('当前页数第{PAGE}页')
	 */
	private function _other() {
		$funcArgusAll = func_get_args();
		
// 		判断最后一条
		if ($this->page == $this->pageTotal) {
			$lastLimit = $this->total-(($this->page-1)*$this->limit)+($this->page-1)*$this->limit; 
			$sumLimit = $this->total;
		}else{
			$lastLimit = ($this->page-1)*$this->limit+$this->limit;
			$sumLimit = $this->page*$this->limit;
		}
		
		
		$this->options['other'] = str_replace('{PAGE}', $this->page, $funcArgusAll[0]);
		$this->options['other'] = str_replace('{TOTAL}', $this->total, $this->options['other']);
		$this->options['other'] = str_replace('{LIMIT}', $sumLimit, $this->options['other']);
		$this->options['other'] = str_replace('{START}', ($this->page-1)*$this->limit+1, $this->options['other']);
		$this->options['other'] = str_replace('{END}', $lastLimit, $this->options['other']);
		$this->options['other'] = str_replace('{SUM}', ceil($this->total/$this->limit), $this->options['other']);
		return $this;
	}
	
	/**
	 * 显示分页
	 */
	public function show() {
		
		$funcArgusAll = func_get_args();
		$arrSum = array();
		$arrSum = array_intersect($funcArgusAll,array_keys($this->options));
		//如果为空，则默认为全部样式
		if (empty($arrSum)) $arrSum = array('other','first','prev','list','next','last','input');//,'select'
		
		$pageStr = '<div id="page">';
		
		foreach ($arrSum as $value) {
// 			判断调用方法
			$this->_default($value);
			$pageStr .= $this->options[$value];
		}
		
		$pageStr .= '</div>';
		
		return $pageStr;
	}
	
/**
 * 设置初始样式
 * @param unknown_type $key
 */	
	private function _default($key) {
		
		switch ($key) {
			case 'first':
				if (empty($this->options['first'])) $this->_firstPrevAndNextLast('<a href="#" class="aFirst">首页</a>','<span class="sFirst">首页</span>','first');
				break;
			case 'prev':
				if (empty($this->options['prev'])) $this->_firstPrevAndNextLast('<a href="#" class="aPrev">上一页</a>','<span class="sPrev">上一页</span>','prev');
				break;
			case 'next':
				if (empty($this->options['next'])) $this->_firstPrevAndNextLast('<a href="#" class="aNext">下一页</a>','<span class="sNext">下一页</span>','next');
				break;
			case 'last':
				if (empty($this->options['last'])) $this->_firstPrevAndNextLast('<a href="#" class="aLast">尾页</a>','<span class="sLast">尾页</span>','last');
				break;
			case 'list':
				if (empty($this->options['list'])) $this->_list('<a href="#" class="leftList">{PAGE}</a>','<span class="selfList">{PAGE}</span>','<a href="#" class="rightList">{PAGE}</a>');
				break;
			case 'select':
				if (empty($this->options['select'])) $this->_select('{PAGE}','<span class="lo_select">{SELECT}</span>');
				break;
			case 'input':
				if (empty($this->options['input'])) $this->_input('跳转','<span class="lo_input">{INPUT}</span>');
				break;
			case 'other':
				if (empty($this->options['other'])) $this->_other('<span class="pageOther">{PAGE}/{SUM}页&nbsp;{LIMIT}/{TOTAL}条&nbsp;当前{START}-{END}条</span>');
				break;
		}
	}
}

?>