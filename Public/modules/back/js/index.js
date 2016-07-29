$(function(){
//	窗口大小自动化
	autoSize();
	$(window).resize(function(){autoSize();});
	
	//侧栏点击效果
	$('.mainLeft dl dd,#u_Cache').click(function(){
//		self Style
		$('.mainLeft dl dd').removeClass('current');
		$(this).addClass('current');
		_src = $(this).attr('src');
		var _this = false;
		//判断是否存在于此li是否存在
		var _width = 0;
		$('div.fastNavi p').each(function(){
			var liSrc = $(this).attr('src');
			if(liSrc == _src) {
				$(this).addClass('current').siblings('p').removeClass('current');
				//$(this).addClass('setThis').css('color','#fff').siblings('li').removeClass('setThis').css({fontWeight:'normal',color:'#333333'});
				//赋值到iframe
				$('.iframeContent iframe[src="'+_src+'"]').show().siblings('iframe').hide();
				_this = true;
				return false;
			}else{
				_width += $(this).width() + 17;//16=>padding:0px 8px;border-left:1px; 
			}
		});
		if(!_this) {
			//取得标题
			var _title = $(this).text();
			//移除所有的当前状态
			$('div.fastNavi p').removeClass('current');
			//组合li
			var liStr = '<p src='+_src+' class="current">'+_title+'<a href="###">╳</a></p>';
			//将新li添加到末尾
			$('div.fastNavi').append(liStr);
			//赋值到iframe
			//$('#showFrame').attr('src',_src);
			$('.iframeContent iframe').hide();
			$('.iframeContent').append('<iframe src="'+_src+'" class="mainIframe" height="100%" name="mainIframe" frameborder="0" width="100%"></iframe>');
			//得到新增加的最后一个li的宽度
			_width = _width + $('div.fastNavi p:last').width() + 17;//25=>20 最后一个li的padding也是25，但由于第一个li(后台首页的padding 20) 所有这里必须要减5 才能和占用的宽度相同 
			//取得div的长度
			var divWidth = $('div.fastNavi').width();
			//如果长度超过li那么则移除最后一个li
			if(_width > divWidth) $('div.fastNavi p:eq(1)').remove();
		}
	});
	//p点击事件
	$('div.fastNavi').on('click','p',function(){
		$(this).addClass('current').siblings('p').removeClass('current');
		_src = $(this).attr('src');
		$('.iframeContent iframe[src="'+_src+'"]').show().siblings('iframe').hide();
	})
	
	//点击关闭按钮关闭标签页
	$('div.fastNavi').on('click','p a',function(){
		//如果它有下一个元素，那么
		var _parent = $(this).parent();
		var _parentSrc = $(_parent).attr('src');
		$('.iframeContent iframe[src="'+_parentSrc+'"]').remove();
		if(_parent.next().attr('src')) {
			_parent.next().addClass('current').siblings('p').removeClass('current');
			$('.iframeContent iframe[src="'+_parent.next().attr('src')+'"]').show().siblings('iframe').hide();
		}else {
			_parent.prev().addClass('current').siblings('li').removeClass('current');
			$('.iframeContent iframe[src="'+_parent.prev().attr('src')+'"]').show().siblings('iframe').hide();
		}
		_parent.remove();
	})
	//双击关闭标签页
	$('div.fastNavi').on('dblclick','p',function(){
		//判断是不是后台首页，如果是则退出
		if($(this).attr('id') == 'index') return false;
		//如果它有下一个元素，那么
		if($(this).next().attr('src')) {
			$(this).next().addClass('current').siblings('li').removeClass('current');
			$('.iframeContent iframe[src="'+$(this).next().attr('src')+'"]').show().siblings('iframe').hide();
		}else {
			$(this).prev().addClass('current').siblings('li').removeClass('current');
			$('.iframeContent iframe[src="'+$(this).prev().attr('src')+'"]').show().siblings('iframe').hide();
		}
		$('.iframeContent iframe[src="'+$(this).attr('src')+'"]').remove();
		$(this).remove();
	});
	
	//主导航点击样式
	$('.naviBottom a').click(function(){
		var _index = $(this).index();
		$('.mainLeft dl:eq('+_index+')').show().siblings('dl').hide();
		$(this).addClass('current').siblings().removeClass('current');
	});
});
function autoSize() {
	_windowHeight = $(window).height();
	_headerHeight = $('.header').height();
	_footerHeight = $('.footer').height();
	//12 .footer margin:5px border:1px;
	_globalHeight = _windowHeight - (_headerHeight + _footerHeight + 12);
	$('.main,.mainLeft').height(_globalHeight);
	
	//10 padding:5px;
//	$('.mainRight').height(_globalHeight - 10);
	_fastNaviHeight = $('.fastNavi').height();
	//40 iframeContent padding:10px margin:5px border:1px  .mainRight padding:5px;
	$('.iframeContent,.mainIframe').height(_globalHeight - ( _fastNaviHeight + 40 ));
}
function fullScreen() {
	$('.header').hide();
	$('.mainLeft').hide();
	$('.fullScreen').hide();
	$('.cancelFullScreen').show();
	_headerHeight = $('.header').height();
	$('.main').height($('.main').height() + _headerHeight);
	$('.iframeContent,.mainIframe').height($('.mainIframe').height() + _headerHeight);
}
function cancelFullScreen() {
	$('.header').show();
	$('.mainLeft').show();
	$('.fullScreen').show();
	$('.cancelFullScreen').hide();
	_headerHeight = $('.header').height();
	$('.main').height($('.main').height() - _headerHeight);
	$('.iframeContent,sa.mainIframe').height($('.mainIframe').height() - _headerHeight);
}
