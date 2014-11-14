var News = function () {
	//this.tab01-News = $('.tab01-News tr:even');
	//this.now_page_url = {:U('/DMIN/')}
}

//初始化对象
News.prototype.init = function () {
	this.select_ckbj_type = $('#select_ckbj_type');		//价格类型
	this.select_tags_vessel = $('.select_tags_vessel');	//选择标签容器Ul
		
	this.hyfl_tags = $('.hyfl_tags');		//行业分类
	this.dqsx_tags = $('.dqsx_tags');		//地区筛选
	this.yhzq_tags = $('.yhzq_tags');		//优惠专区
	this.jg_tags = $('.jg_tags');			//价格标签
	this.mh_type_tags = $('.mh_type_tags');	//门户类型
	this.sfxwy_tags = $('.sfxwy_tags');		//是否新闻源
	this.dljzk_tags = $('.dljzk_tags');		//带链接情况

	
	this.region_right = $('.region_right');//城市选择框
	this.btn_confirm_dqmr = $('.btn_confirm_dqmr');	//区域选择确认按钮

	this.btn_jiage_yes = $('.btn_jiage_yes');		//价格按钮
	this.ipt_jiage_start = $('.ipt_jiage_start');	//开始价格
	this.ipt_jiage_over = $('.ipt_jiage_over');		//结束价格
	
	this.mr_mtlb_tags = $('.mr_mtlb_tags');		//名人/媒体类别
	this.phd_tags = $('.phd_tags');			//配合度
	this.mr_fans_num_tags = $('.mr_fans_num_tags');		//粉丝数
	this.zhyc_tags = $('.zhyc_tags');		//是否支持原创
	
	this.search_tag_data = $('.search_tag_data');	//已选标签

	this.delete_tags = $('.delete_tags');			//删除标签
	
	this.resultbox = $('.resultbox');				//标签框体
	
	this.clear_tags = $('.clear_tags');	//清空选项
	
	this.three_sidebar_type = $('.three_sidebar_type');	//三级导航选项
	
	this.search = $('#search');				//search按钮
	this.search_account = $('.search_account');		//搜索的值
	
	this.lahei_and_shoucang = $('.lahei_and_shoucang');
	
	this.all_data_num = $('#all_data_num');	//所有数据
	this.all_page_num = $('#all_page_num');	//所有分页数
	
	this.body = $('body');
	this.list_content = $('#list_content');
	this.mrdetail = $('.mrdetail');
	this.batchboxdetail = $('.batchboxdetail');
	
	
	this.all_selected = $('.all_selected');	//全选
	this.now_selected = $('.now_selected');	//当前选中的值
	this.add_selected_box = $('.add_selected_box');	//添加选中的数据
	
	this.pt_type = $('#pt_type');	//平台类型ID
	this.order_id = $('#order_id');	//订单ID
	this.account_ids = $('#account_ids');	//媒体账号
	
	this.order_vessel = $('.order_vessel');	//订单容器
	this.close_order_vessel = $('.close_order_vessel');//关闭订单容器
	this.account_num = $('.account_num');//账号数
	this.account_money = $('.account_money');	//价格
	this.account_selected = $('.account_selected');	//已选择账号
	this.delet_account = $('.delet_account');	//删除账号
	this.confirm_order = $('.confirm_order');	//确认订单
	this.tbody = $('.tbody');	//微博账号容器
	this.orderspan = $('.orderspan');	//排序按钮
	
	this.export_csv = $('.export_csv');	//导出CSV按钮
	
}


//添加相关样式
News.prototype.add_table_class = function () {
	$('.tab01-News tr:even').addClass('even');
	
	$('.top-search strong').click(function(){
		$(this).siblings().removeClass('on')
		$(this).addClass('on');
	})
	$('#morecate').click(function(){
		$('.part01-cate').toggleClass('part01-cate-auot');
	})
	$('#morecity').click(function(){
		$('.part01-city').toggleClass('part01-city-auot');
	})
}

//点击创建标签
News.prototype.select_tag_fn = function () {
	var _father_this = this;
	
	_father_this.hyfl_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	_father_this.dqsx_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	_father_this.btn_confirm_dqmr.click(function () {
		var _this = $(this);
		if (_father_this.region_right.val() == '') {
			return false;
		}
		_father_this.create_selete_tags(_father_this.region_right.find("option:selected").text(),_father_this.region_right.val(),{
			'tag_class' : _father_this.region_right.data('tag_class'),
			'classify':_father_this.region_right.data('classify'),
			'field' : _father_this.region_right.data('field'),
			'repetition' : _father_this.region_right.data('repetition')
		});
	});
	
	
	_father_this.yhzq_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	
	_father_this.jg_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
		});
		var ipt_val = _this.data('val').split("-");
		_father_this.ipt_jiage_start.val(ipt_val[0]);
		_father_this.ipt_jiage_over.val(ipt_val[1]);
	});
	
	
	_father_this.mh_type_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
		});
	});
	
	
	_father_this.sfxwy_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
		});
	});
	
	
	_father_this.dljzk_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
		});
	});
	

}


//创建常见分类
News.prototype.create_selete_tags = function ($title,$val,$extend) {
	var _father_this = this;
	_father_this.init();
	
	var tags_vessel = _father_this.select_tags_vessel.find('li');
	//当前已选标签的总数
	var now_tags_count = tags_vessel.size();

	var tag_class = $extend.tag_class	//所属标签class
	var tag_id = $extend.tag_id;		//标签ID
	var classify = $extend.classify;	//类型title
	var field = $extend.field;			//字段名
	var repetition = $extend.repetition;//是否允许重复

	var tas_html = '';
	tas_html += '<li data-classify="'+classify+'" >';
	tas_html += '<strong>'+classify+' &gt; <b>'+$title+'</b></strong>';
	tas_html += '<em class="del delete_tags" data-tag_id="'+tag_id+'" data-tag_class="'+tag_class+'"></em>';
	tas_html += '<input type="hidden" class="search_tag_data" name="'+field+'" value="'+$val+'" data-tag_id="'+tag_id+'" data-classify="'+classify+'" data-field="'+field+'" data-title="'+$title+'" data-val="'+$val+'" />';
	tas_html += '</li>';
	
	var hava_tag_status = true;
	//对已经存在的元素进行替换
	tags_vessel.each(function () {
		var _this = $(this);
		if (_this.data('classify') == classify) {
			_this.replaceWith(tas_html);
			hava_tag_status = false;
			return false;
		}
	});
	
	if (hava_tag_status == true) {
		//添加到选择容器去
		_father_this.select_tags_vessel.append(tas_html);
	}
	
	//加载删除事件
	_father_this.del_select_tag_fn();

	//填充了已选标签后家首选
	_father_this.init_tags_selected();
	
}


//追加样式以及首选(重要)
News.prototype.init_tags_selected = function () {
	var _father_this = this;
	_father_this.init();
	
	//为已选标签加上首选或者清除首选
	_father_this.search_tag_data.each(function () {
		var _this = $(this);

		_father_this.hyfl_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.hyfl_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.dqsx_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.dqsx_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.yhzq_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.yhzq_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
	
		_father_this.jg_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.jg_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.mh_type_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.mh_type_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.sfxwy_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.sfxwy_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.dljzk_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.dljzk_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
	});
	
	
	if (_father_this.select_tags_vessel.children().is('li') == true) {
		_father_this.resultbox.css('display','block');	
	} else {
		_father_this.resultbox.css('display','none');
	}
	
	
	public_post_fn({});
	//_father_this.get_selected_tags();
}


//删除选中标签
News.prototype.del_select_tag_fn = function () {
	var _father_this = this;
	_father_this.init();
	
	_father_this.delete_tags.click(function () {
		$(this).parent().remove();
		_father_this.bu_xian_init_fn(this);
	});

}


//删除标签执行的方法
News.prototype.bu_xian_init_fn = function (obj) {
	var _father_this = this;
	_father_this.init();
	
	var now_class = $(obj).data('tag_class');
	$('.'+now_class).removeClass("select");
	$('.'+now_class).eq(0).addClass("select");
	_father_this.init_tags_selected();
	
}

//点击确认安按钮
News.prototype.btn_click_create_tags = function () {
	var _father_this = this;
	_father_this.init();
	
	//价格按钮
	_father_this.btn_jiage_yes.click(function () {
		var _this = $(this);
		var tag_class = _this.data('tag_class');
		var obj = $('.'+tag_class).eq(1);
		var start = _father_this.ipt_jiage_start.val() ? _father_this.ipt_jiage_start.val() : 0;
		var over = _father_this.ipt_jiage_over.val() ? _father_this.ipt_jiage_over.val() : 10000000;
		var val = start + '-' + over;
		
		var show_title;
		if (_father_this.ipt_jiage_over.val() == '' || _father_this.ipt_jiage_over.val() > 10000000) {
			show_title = start + ' - >10000'+ '元';
		} else {
			show_title = start +'-'+_father_this.ipt_jiage_over.val()+'元'
		}
		
		_father_this.create_selete_tags(show_title,val,{
			'tag_class' : obj.data('tag_class'),
			//'tag_id':obj.data('tag_id'),
			'classify':obj.data('classify'),
			'field' : obj.data('field'),
			'repetition' : obj.data('repetition')
		});
	});
		
}


/**
 * 获取选中的标签数据，返回成key=>val 类型
 */
News.prototype.get_selected_tags = function () {
	var _father_this = this;
	_father_this.init();

	var search_tag_data = _father_this.search_tag_data;	//获取选中的标签值
	var ckbj_type = _father_this.select_ckbj_type.val()
	
	var result = {};
	result.ckbj_type = ckbj_type;
	if (search_tag_data.size() > 0 ){
		search_tag_data.each (function () {
			var _this = $(this);
			result[_this.data('field')] = _this.data('val');
		});
	
	}
	
	return result;
}


/**
 * 清空选中的标签
 */
News.prototype.clear_tags_fn = function () {
	var _father_this = this;

	_father_this.clear_tags.click(function () {
		//清除已选的标签
		_father_this.select_tags_vessel.empty();
		
		//清空搜索框
		_father_this.search_account.val('');
		_father_this.search_account.data('ischeck',0);
		
		//重新请求
		_father_this.init_tags_selected();
	});
}


//点击切换三级分类
News.prototype.three_sidebar_type_fn = function () {
	var _father_this = this;
	_father_this.init();
	
	_father_this.three_sidebar_type.click(function () {
		_father_this.three_sidebar_type.removeClass("select");
		var _this = $(this);
		_this.addClass("select");
		public_post_fn({});
	});
	
} 


//获取3级导航当前选中的数据
News.prototype.get_three_sidebar_selected = function () {
	var _father_this = this;
	_father_this.init();
	
	var result = {};
	_father_this.three_sidebar_type.each(function () {
		var _this = $(this);
		if (_this.is('.select')) {
			result[_this.data('field')] = _this.data('val');
			return false;
		}
	});
	return result;
}


//点击search的按钮
News.prototype.search_fn = function () {
	var _father_this = this;
	_father_this.init();
	_father_this.search.click(function () {
		if (_father_this.search_account.val() == '') {
			_father_this.search_account.data('ischeck',0);
		} else {
			_father_this.search_account.data('ischeck',1);	//状态变成已点击
		}
		public_post_fn({});
	});
}

//获取搜索框的值
News.prototype.get_search_account = function () {
	var _father_this = this;
	_father_this.init();
	var _this = _father_this.search_account;
	if (_this.data('ischeck') == 1) {
		return {'field':_this.data('field'),'val':_this.val()};
	} else {
		return false;
	}
}

//拉黑收藏功能
News.prototype.lahei_and_shoucang_fn = function ($urL) {
	var _father_this = this;
	_father_this.init();
	_father_this.lahei_and_shoucang.click(function () {
		var _this = $(this);
		var post_data = {
			'pt_type' :system_info.pt_type,	
			'is_celeprity' :system_info.is_celeprity,
			'or_type' : _this.data('or_type'),
			'News_id' : _this.data('News_id')
		};
		var result = System.ajax_post_setup($urL,post_data,'JSON');
		
		if (result.status == 1) {
			//alert(result.msg);
			public_post_fn({});
		} else {
			//alert(result.msg);
		}
	
	});
}


//更新分页记录条数，和页数
News.prototype.dataNum_And_PageNum = function (data) {
	var _father_this = this;
	
	_father_this.all_data_num.text(data);
	_father_this.all_page_num.text(Math.ceil(data / system_info.page_limit));
}



//全选
News.prototype.all_selected_fn = function () {
	var _father_this = this;

	_father_this.all_selected.click(function () {
		_father_this.init();
		_father_this.now_selected.prop('checked',true);
	});
}


//批量添加数据源
News.prototype.add_selected_box_fn = function () {
	var _father_this = this;
	
	_father_this.add_selected_box.unbind();
	//点击批量添加账号时
	_father_this.add_selected_box.click(function () {
		_father_this.add_selected_account_to_vessel()
	}); 
}


//批量订单实际流程
News.prototype.add_selected_account_to_vessel = function () {
	var _father_this = this;
	var _account_ids;	//账号IDs
	
	_father_this.init();
	
	//缓存选择后的账号的数据数据在HTML中
	var cache_select_account = function () {
		var _vessel_ids = get_order_vessel_ids();
		//遍历已经选择的微博ID
		$.each( _account_ids, function(i, n){
			//对已经在容器的微博账号不再添加，避免重复
			if (System.in_array(n,_vessel_ids) == false) {
				var _now_account_x = $('.accounts_'+n);	//列表的行
				var _name = _now_account_x.find('.account_name').data('account_name');
				var _money = _now_account_x.find('.now_money').data('money');
				_father_this.account_selected.append('<li data-select_account_id="'+n+'" data-money="'+_money+'"><span class="del fr delet_account"></span><strong>'+_name+'</strong><strong>单价：'+_money+'</strong></li>');
			}
		});
		
		set_order_data();	
		
		delet_account_fn();
		
		//弹窗插件
		_father_this.order_vessel.show();
	}
	

	//在订单容器中，获取已经选择的数据
	var get_order_vessel_ids = function () {
		var have_ids = [];
		
		_father_this.account_selected.children().each(function (){
			var id = $(this).data('select_account_id');
			if (id != undefined) {
				have_ids.push(id);
			}
		});
		return have_ids;
	} 
	
	
	//计算当前的账号总数和价格总数
	var set_order_data = function () {
		
		var money_sum = 0;
		var account_sum = 0;
		_father_this.account_selected.children().each(function (){
			var _this = $(this);
			
			money_sum += Number(_this.data('money'));
			account_sum += 1;
		});
		
		_father_this.account_money.text(money_sum);
		_father_this.account_num.text(account_sum);
	}
	
	
	//删除一个已经选择的订单
	var delet_account_fn = function () {
		_father_this.init();
		
		_father_this.delet_account.unbind();	//清除之前绑定的事件
		_father_this.delet_account.click(function () {
			$(this).parent().remove();
			set_order_data();
		});
	}
	
	//关闭窗口
	var close_order_vessel_fn = function () {
		_father_this.close_order_vessel.unbind();
		_father_this.close_order_vessel.click(function () {
			//弹窗插件
			_father_this.order_vessel.hide();
		});
	}();

	
	//流程控制
	//alert(_account_ids);
	_account_ids = [];
	_father_this.now_selected.each(function () {
		var _this = $(this);
		if (_this.prop('checked') == true) {
			_account_ids.push(_this.data('id'))
		}
	});
	if (_account_ids == '') {
		//alert('请选择账号！');
		return false;
	} else {
		cache_select_account();
	}
	
	
	//确认提交订单
	_father_this.confirm_order_fn = function () {
		_father_this.confirm_order.unbind();
		_father_this.confirm_order.click(function () {
			var selected_account_ids = [];	//已选中的ID
			_father_this.account_selected.children().each(function (){
				var _this = $(this);
				selected_account_ids.push(_this.data('select_account_id'));
			});
			
			if (selected_account_ids == '') {
				alert('请重新选择后提交！')
				return false;
			} else {
				var post_data = {};
				post_data.account_ids = selected_account_ids.join(',');
				post_data.pt_type = _father_this.pt_type.val();
				post_data.order_id = _father_this.order_id.val();
		
				//提交操作
				var result = System.ajax_post_setup(system_info.post_order_url,post_data,'JSON');
				if (result.status == 0) {
					//alert(result.msg);
					window.location.href= result.data.go_to_url;	//跳转
				} else {
				//	alert(result.msg);
				}
			}
			
		});
	}();
	
}


//订单排序
News.prototype.orderspan_fn = function () {
	var _father_this = this; 
	
	_father_this.orderspan.click(function () {	//orderspan-select
		var _this = $(this);
		_father_this.orderspan.removeClass('orderspan-select');
		_this.addClass('orderspan-select');
		
		//排序
		var _sort_type = _this.data('sort_type');//排序类型
		_father_this.sort_table_fn(_sort_type);	//执行排序
	});
}




//排序表单
News.prototype.sort_table_fn = function ($sort_type) {
	
	var _father_this = this;
	var tr = _father_this.list_content.children();
	var tr_arr = [];
	
	//把tr放入数组中，用作排序
	tr.each (function () {
		tr_arr.push(this);
	});
	
	tr_arr.sort(function (v1,v2) {
		var obj1 = $(v1);
		var obj2 = $(v2);
		if ( Number(obj1.data($sort_type)) > Number(obj2.data($sort_type)) ) {
			return -1;
		} else if (Number(obj1.data($sort_type)) < Number(obj2.data($sort_type))) {
			return 1;
		} else {
			return 0;
		}
	});
	
	//重新插入到table中
	for (var i=0;i<tr_arr.length;i++) {
		_father_this.list_content.append(tr_arr[i]);
	}
}


//页面记载完毕后执行的方法
News.prototype.page_init_fn = function () {
	var _father_this = this;
	
	//对没有订单的情况进行隐藏
	if (_father_this.pt_type.val() =='' || _father_this.order_id.val() == '') {
		//_father_this.all_selected.css({'display':'none'});
		//_father_this.add_selected_box.css({'display':'none'});
		//_father_this.now_selected.remove();
	}
}


//根据选中的账号导出报价单
News.prototype.export_csv_fn = function () {
	var _father_this = this;
	
	_father_this.export_csv.click(function(){
		var _this = $(this);
		var base_src = _this.data('base_src');
		var selected_account_ids = [];
		var url;
		_father_this.account_selected.find('li').each(function () {
			var _li_this = $(this);
			selected_account_ids.push(_li_this.data('select_account_id'));	
		}); 
		
		if (selected_account_ids == '') {
			alert('请先选择账号！');
			return false;
		} else {
			url = base_src + '/ids/' + selected_account_ids.join(',');
			_this.attr('href',url);
			
			var export_data = {};
			export_data.account_ids = selected_account_ids.join(',');
			export_data.type = 1;
			_father_this.export_post_data(system_info.export_url,export_data);
		}
	});
}


//把导出记录保存到数据库中
News.prototype.export_post_data = function (urL,post_data) {
	if (post_data == '' || urL == '' ) return false;

	var result = System.ajax_post_setup(urL,post_data,'JSON');
	
	return result;
}


News.prototype.now_selected_fn = function () {
	
	var _father_this = this;
	_father_this.now_selected.click(function () {
	
		var _this = $(this);
		var id = _this.data('id');
		
	//	_father_this.order_vessel.show();
	});
}






//执行
News.prototype.run = function () {
	var _father_this = this;
	
	_father_this.add_table_class();
	
	_father_this.select_tag_fn();	//点击创建标签
	
	_father_this.init_tags_selected();	//加首选
	
	_father_this.btn_click_create_tags();	//创建标签
	
	_father_this.three_sidebar_type_fn();	//三级导航分类方法
	
	_father_this.clear_tags_fn();	//清空已选择标签
	
	_father_this.search_fn();
	
	_father_this.page_init_fn();	//加载页面初始化
	
	_father_this.all_selected_fn();	//全选
	
	_father_this.add_selected_box_fn();	//添加
	
	_father_this.orderspan_fn();
	
	_father_this.sort_table_fn();
	
	_father_this.export_csv_fn();
}




//运行
var News = new News();
window.onload = function () {
	News.init();
	News.run();	
}




	
	