$(function(){
	//select_input
	$('#invert_selection,#check_id_all').click(function(event){
		event.stopPropagation();
		$('input[name="id_all[]"]').each(function(){
			var checkStatus = $(this).prop('checked');
			if(checkStatus) {
				$(this).prop('checked',false);
			}else{
				$(this).prop('checked',true);
			}
		});
	});
//	validForm
	 $("#formData,.formData,.postForm").Validform({
		tiptype:function(msg,o,cssctl){
			//msg：提示信息;
			//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
			//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
			if(!o.obj.is("form")){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
				var objtip=o.obj.siblings(".Validform_checktip");
				cssctl(objtip,o.type);
				objtip.text(msg);
			}
		},
		datatype:{//传入自定义datatype类型【方式二】;
			"need_1":function(gets,obj,curform,regxp){
				var need=1,numselected=curform.find("input[name='"+obj.attr("name")+"']:checked").length;
				return  numselected >= need ? true : "请至少选择"+need+"项！";
			},
			"z":function(gets,obj,curform,regxp){
				var preg = /^[\u4E00-\uFA29]+$/;
				return preg.test(gets);
			},
			'check_ajax':function(gets,obj,curform,regxp){
				var ajaxResult = false;
				$.ajax({
					async:false,//非跨域下有效
//					crossDomain:true,
					url:WEB_URL+$(obj).attr('ajax'),
					type:'POST',
					dataType:'jsonp',
					data:{param:$(obj).val()},
					jsonp:JSONP_CALLBACK,
					success:function(data){
						ajaxResult =  (data.status != 'error' && data.status != 0) ? true : false;
					},
					error:function(){return 'fail';}
				})
				return ajaxResult;
			}
		}
	});
	//input_color 
	$(':text,:password,textarea').focus(function(){
		$(this).addClass('focus');
	});
	$(':text,:password,textarea').blur(function(){
		$(this).removeClass('focus');
	});
	//列表样式
	$('.showTable tr').mouseover(function(){
		$(this).addClass('trHover');
	});
	$('.showTable tr').mouseout(function(){
		$(this).removeClass('trHover');
	});
});
(function(window,$){
	$.CR = {
		/***前后台共用***/
		G:{
			//Ajax批量操作
			bulkAction:function(url) {
				var id = '';
				$('input[name="id_all[]"]:checked').each(function(){id += $(this).val()+',';});
				if(id) {
					id = id.substr(0,id.length-1);
					$.ajax({
						url:url,
						data:{id:id},
						type:'POST',
						dataType:'json',
						boforeSend:function(){art.dialog.tips('数据正在提交...', 10);},
						success:function(data){
                                                    _tips(data,true);
                                                },
						error:function(){art.dialog.tips('发送数据至服务器失败！');}
					});
				}else {
					_alert('请选择需要操作的数据！');
				}
			},
			//Ajax批量操作+Token
			bulkTokenAction:function(url,tokenName,obj) {
				var idKey = '';
				$('input[name="id_all[]"]:checked').each(function(){
					idKey += $(this).val()+','+$(this).attr(tokenName)+'|';
				});
				if(idKey) {
					idKey = idKey.substr(0,idKey.length-1);
					var data = {id_key:idKey};
					if(obj) data = $.extend(obj,data)
					$.ajax({
						url:url,
						data:data,
						type:'POST',
						dataType:'json',
						boforeSend:function(){art.dialog.tips('数据正在提交...', 10);},
						success:function(data){_tips(data,true);},
						error:function(){art.dialog.tips('发送数据至服务器失败！');}
					});
				}else {
					_alert('请选择需要操作的数据！');
				}
			},
			//排序
			sort:function(url) {
				url = url ? url : $.G.U('sort');
				_sortVal = '';
				$('[name^="sort"]').each(function(){
					var sortID = $(this).attr('name').replace(/sort\[(.+)\]/,'$1');
					var sortVal = $(this).val();
					_sortVal += sortID+'#'+sortVal+'|';
				})
				_sortVal = _sortVal.substr(0,_sortVal.length-1);
				$.ajax({
					url:url,
					data:{sort:_sortVal},
					type:'POST',
					dataType:'json',
					boforeSend:function(){art.dialog.tips('数据正在提交...', 10);},
					success:function(data){_tips(data,true);},
					error:function(){art.dialog.tips('发送数据至服务器失败！');}
				});
			},
			//搜索
			searchs:function (params){
				params = params ? params :'?1=1&'; 
				var _formVal = $('[name="search_form"]').serialize();
				var _formAction = $('[name="search_form"]').attr('action');
				window.location.href = _formAction+params+_formVal;
			},
			//图片展示
			showIMG:function (src) {
				if(!src) return false;
				var _indexLen = src.indexOf('|');
				if(src.indexOf('|') > 0) {var src = src.substr(0,_indexLen);}
				art.dialog({
			        title: '图片展示',
			        fixed: true,
			        id:"image_priview",
			        lock: true,
			        background:"#CCCCCC",
			        opacity:0,
			        content: '<img src="' + _ROOT_ +'/' + src + '" />',
			        time: 10
			    });
			}
		},
		/**后台操作**/
		B:{
			/**文件管理器 Start**/
			F:{
				Rename:function(){
					$('input[name="file[]"]').each(function(k,v){
						if(v.checked) {
							art.dialog.prompt('注：不包含扩展名！',function(val){
								if($.trim(val).length == 0) {
									_alert('请输入新的文件名！');
									return false;
								}
								$.post($.G.U('rename'),{filepath:v.value,new_name:val},function(data){
									_tips(data,true);
								},'json');
							});
							return false;
						}
					});
				},
				Mobile:function(){
					$('input[name="file[]"]').each(function(k,v){
						if(v.checked) {
							art.dialog.prompt('{$root}/：网站根目录，路径不包含文件名！',function(val){
								if($.trim(val).length == 0) {
									_alert('请输入新的文件路径！');
									return false;
								}
								$.post($.G.U('mobile'),{filepath:v.value,new_path:val},function(data){
									_tips(data,true);
								},'json');
							},'{$root}/');
							return false;
						}
					});
				},
				Copy:function(){
					$('input[name="file[]"]').each(function(k,v){
						if(v.checked) {
							art.dialog.prompt('{$root}/：网站根目录',function(val){
								if($.trim(val).length == 0) {
									_alert('请输入新的文件路径！');
									return false;
								}
								$.post($.G.U('copy'),{filepath:v.value,new_path:val},function(data){
									_tips(data,true);
								},'json');
							},'{$root}/');
							return false;
						}
					});
				},
				AddDir:function(filepath){
					art.dialog.prompt('请输入目录名！',function(val){
						if($.trim(val).length == 0) {
							_alert('请输入目录名！');
							return false;
						}
						$.post($.G.U('add_dir'),{filepath:filepath,dirname:val},function(data){
							_tips(data,true);
						},'json');
					});
					return false;
				},
				AddFile:function(filepath){
					art.dialog.prompt('请输入文件名！',function(val){
						if($.trim(val).length == 0) {
							_alert('请输入文件名！');
							return false;
						}
						$.post($.G.U('add_file'),{filepath:filepath,filename:val},function(data){
							_tips(data,true);
						},'json');
					});
					return false;
				},
				Delete:function(){
					var _filepath = '';
					$('input[name="file[]"]').each(function(k,v){
						if(v.checked) {
							_filepath+=v.value+'|';
						}
					});
					if(_filepath) {
						_confirm('是否确定要删除？',function(){
							_filepath = _filepath.substring(0,_filepath.length-1);
							$.post($.G.U('delete'),{all_filepath:_filepath},function(data){
								_tips(data,true);
							},'json');
						},$.noop)
					} else {
						_alert('请选择需要删除的文件！');
					}
				}
			},
			getModelFields:function(id) {
				$('#fields').val('');
				$.get($.G.U('Back/Public/get_Model_Fields'),{id:id},function(data){
					if(data) {
						var fields = '';
						$.each(data,function(k,v){
//							 fields+='<a href="###" onclick="$(\'#fields\').val($(\'#fields\').val()+\''+v+',\')">'+v+'</a>'; 
							 fields+='<a href="###" onclick="var _fieldsValue = $(\'#fields\').val();if(_fieldsValue.indexOf(\''+v+':0,\') <= -1){$(\'#fields\').val(_fieldsValue+\''+v+':0,\\n\')}">'+v+'</a>'; 
						})
						$('#select_fields').html(fields);
					}
				});
			}
		}
		/**后台操作 End**/
	}
})(window,jQuery);
