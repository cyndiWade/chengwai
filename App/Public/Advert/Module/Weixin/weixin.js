var Weixin = function () {
	//this.tab01-Weixin = $('.tab01-Weixin tr:even');
	//this.now_page_url = {:U('/DMIN/')}
}

//初始化对象
Weixin.prototype.init = function () {
	this.select_zfjg_type = $('#select_zfjg_type');		//价格类型
	this.select_tags_vessel = $('.select_tags_vessel');	//选择标签容器Ul
		
	this.cjfl_tags = $('.cjfl_tags');		//常见分类
	this.jg_tags = $('.jg_tags');			//价格标签
	this.fans_num_tags = $('.fans_num_tags');	//粉丝数据量
	this.dfmr_mt_tags = $('.dfmr_mt_tags');	//地方名人/媒体：
	
	this.region_right = $('.region_right');//城市选择框
	this.btn_confirm_dqmr = $('.btn_confirm_dqmr');	//区域选择确认按钮
	
	this.sprz_rq = $('#sprz_rq');	//视频认证容器
	this.sprz_ra_size = this.sprz_rq.children('span').size();
	for (var i=1;i<=this.sprz_ra_size;i++) {
		this['sprz_tags_'+i] = $('.sprz_tags_'+i);	//粉丝量认证
	}

	this.zhsfrz_tags = $('.zhsfrz_tags');	//账号是否认证：
	this.szxb_tags = $('.szxb_tags');		//受众性别
	this.fsrzsj_tags = $('.fsrzsj_tags');	//粉丝量认证时间
	this.zpjyds_tags = $('.zpjyds_tags');		//周平均阅读数
	
	
	this.btn_jiage_yes = $('.btn_jiage_yes');		//价格按钮
	this.ipt_jiage_start = $('.ipt_jiage_start');	//开始价格
	this.ipt_jiage_over = $('.ipt_jiage_over');		//结束价格
	
	this.btn_fansNum_yes = $('.btn_fansNum_yes');	//粉丝按钮
	this.ipt_fansNum_start = $('.ipt_fansNum_start');//开始粉丝数
	this.ipt_fansNum_over = $('.ipt_fansNum_over');	//结束粉丝数
	
	this.search_tag_data = $('.search_tag_data');	//已选标签

	this.delete_tags = $('.delete_tags');			//删除标签
	
	this.resultbox = $('.resultbox');				//标签框体
	
	this.clear_tags = $('.clear_tags');	//清空选项
	
	this.three_sidebar_type = $('.three_sidebar_type');	//
	
	this.search = $('#search');				//search按钮
	this.search_account = $('.search_account');		//搜索的值
	
	this.lahei_and_shoucang = $('.lahei_and_shoucang');
	
	this.all_data_num = $('#all_data_num');	//所有数据
	this.all_page_num = $('#all_page_num');	//所有分页数
	
	this.body = $('body');
	this.list_content = $('#list_content');
	this.wxdetail = $('.wxdetail');
	this.batchboxdetail = $('.batchboxdetail');
	
	this.all_selected = $('.all_selected');	//全选
	this.now_selected = $('.now_selected');	//当前选中的值
	this.add_selected_box = $('.add_selected_box');	//添加选中的数据
	
	this.pt_type = $('#pt_type');	//平台类型ID
	this.order_id = $('#order_id');	//订单ID
	
	this.order_vessel = $('.order_vessel');	//订单容器
	this.close_order_vessel = $('.close_order_vessel');	//关闭订单容器
	this.account_num = $('.account_num');//账号数
	this.account_money = $('.account_money');	//价格
	this.account_selected = $('.account_selected');	//已选择账号
	this.delet_account = $('.delet_account');	//删除账号
	this.confirm_order = $('.confirm_order');	//确认订单
	this.tbody = $('.tbody');	//微博账号容器
	this.orderspan = $('.orderspan');	//排序按钮
	
	this.tooltip_tonus = $('.tooltip_tonus');	//放大图
	
	this.qr_code = $('.qr_code');	//二维码详情
	
	this.export_csv = $('.export_csv');	//导出CSV按钮
	
	this.account_all_html = $('.account_all_html');
	
	this.add_one_selected_box = $('.add_one_selected_box');//新版添加购物城
	
	this.mid_batch = $('.mid-batch');
}


//添加相关样式
Weixin.prototype.add_old_class = function () {
	$('.tab01-Weixin tr:even').addClass('even');
	
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
	$('#moreta').click(function(){
		$('.part01-xqbq').toggleClass('part01-city-auot');
	})
	$('.top-batchdetail li').click(function(){
		var i=$(this).index();
		$(this).siblings().removeClass('select');
		$(this).addClass('select');
		$('.box01-batchdetail').hide();
		$('.box01-batchdetail').eq(i).show();
	});
}


//点击创建标签
Weixin.prototype.select_tag_fn = function () {
	var _father_this = this;
	
	_father_this.cjfl_tags.click(function (){
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
		//_father_this.ipt_jiage_start.val(ipt_val[0]);
		//_father_this.ipt_jiage_over.val(ipt_val[1]);
	});
	
	_father_this.dfmr_mt_tags.click(function (){
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
		
	_father_this.fans_num_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
		});
		//var ipt_val = _this.data('val').split("-");
		//_father_this.ipt_fansNum_start.val(ipt_val[0]);
		//_father_this.ipt_fansNum_over.val(ipt_val[1]);
	});
	
	_father_this.btn_fansNum_yes.click(function () {
		var _this = $(this);
		var tag_class = _this.data('tag_class');
		var obj = $('.'+tag_class).eq(1);
		var start = _father_this.ipt_fansNum_start.val() ? _father_this.ipt_fansNum_start.val() : 0;
		var over = _father_this.ipt_fansNum_over.val() ? _father_this.ipt_fansNum_over.val() : 10000000;
		var val = start * 10000 + '-' + over * 10000;
		
		var show_title;
		if (_father_this.ipt_fansNum_over.val() == '' || _father_this.ipt_fansNum_over.val() > 10000000) {
			show_title = start + ' - >10000'+ '万';
		} else {
			show_title = start +'-'+_father_this.ipt_fansNum_over.val()+'万'
		}
		
		_father_this.create_selete_tags(show_title,val,{
			'tag_class' : obj.data('tag_class'),
			//'tag_id':obj.data('tag_id'),
			'classify':obj.data('classify'),
			'field' : obj.data('field'),
			'repetition' : obj.data('repetition')
		});
	});
	
	for (var i=1;i<=this.sprz_ra_size;i++) {
		_father_this['sprz_tags_'+i].click(function () {
			var _this = $(this);
			if(_this.prop('checked') == true) {
				_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
					//'tag_class' : _this.data('tag_class'),
					'tag_id':_this.data('tag_id'),
					'classify':_this.data('classify'),
					'field' : _this.data('field'),
					'repetition' : _this.data('repetition')
				});
			} else {
				_father_this.search_tag_data.each(function () {
					_select_obj  = $(this);
					if (_select_obj.data('tag_id') == _this.data('tag_id')) {
						_select_obj.parent().remove();
						_father_this.init_tags_selected();
						return false;
					}
				});
			}	
		});
	}
	
	_father_this.zhsfrz_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	_father_this.szxb_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	_father_this.fsrzsj_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	
	_father_this.zpjyds_tags.click(function (){
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
Weixin.prototype.create_selete_tags = function ($title,$val,$extend) {
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
Weixin.prototype.init_tags_selected = function () {
	var _father_this = this;
	_father_this.init();
	
	//为已选标签加上首选或者清除首选
	_father_this.search_tag_data.each(function () {
		var _this = $(this);

		_father_this.cjfl_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.cjfl_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
			
		//处理价格标签
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
		
		
		_father_this.dfmr_mt_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.dfmr_mt_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		//处理粉丝数量标签
		_father_this.fans_num_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.fans_num_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.zhsfrz_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.zhsfrz_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.szxb_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.szxb_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.fsrzsj_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.fsrzsj_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.zpjyds_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.zpjyds_tags.removeClass("select");
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
Weixin.prototype.del_select_tag_fn = function () {
	var _father_this = this;
	_father_this.init();
	
	_father_this.delete_tags.unbind();
	_father_this.delete_tags.click(function () {
		$(this).parent().remove();
		_father_this.bu_xian_init_fn(this);
	});

}


//删除标签执行的方法
Weixin.prototype.bu_xian_init_fn = function (obj) {
	var _father_this = this;
	_father_this.init();
	
	var now_class = $(obj).data('tag_class');
	$('.'+now_class).removeClass("select");
	$('.'+now_class).eq(0).addClass("select");
	
	//认证数据处理
	for (var i=1;i<=this.sprz_ra_size;i++) {
		if (_father_this['sprz_tags_'+i].data('tag_id') == $(obj).data('tag_id')) {
			_father_this['sprz_tags_'+i].prop("checked", false);
		}
	}
	
	_father_this.init_tags_selected();
	
}

//点击确认安按钮
Weixin.prototype.btn_click_create_tags = function () {
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
		
		//>10000 
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
Weixin.prototype.get_selected_tags = function () {
	var _father_this = this;
	_father_this.init();

	var search_tag_data = _father_this.search_tag_data;	//获取选中的标签值
	var zfjg_type = _father_this.select_zfjg_type.val()
	
	var result = {};
	result.zfjg_type = zfjg_type;
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
Weixin.prototype.clear_tags_fn = function () {
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
Weixin.prototype.three_sidebar_type_fn = function () {
	var _father_this = this;
	_father_this.init();
	
	_father_this.three_sidebar_type.click(function () {
		_father_this.three_sidebar_type.removeClass("select");
		_father_this.three_sidebar_type.removeClass("selected");
		var _this = $(this);
		_this.addClass("select");
		_this.addClass("selected");
		public_post_fn({});
	});
	
} 


//获取3级导航当前选中的数据
Weixin.prototype.get_three_sidebar_selected = function () {
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
Weixin.prototype.search_fn = function () {
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
Weixin.prototype.get_search_account = function () {
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
Weixin.prototype.lahei_and_shoucang_fn = function ($urL) {
	var _father_this = this;
	_father_this.init();
	_father_this.lahei_and_shoucang.click(function () {
		
		var _this = $(this);
		
		if (_this.data('or_type') == 0) {
			if (confirm('确认操作?') == false) return false; 
		}
		
		var post_data = {
			'action' : _this.data('action'),
			'is_celebrity' :system_info.is_celebrity,
			'or_type' : _this.data('or_type'),
			'weixin_id' : _this.data('weixin_id')
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
Weixin.prototype.dataNum_And_PageNum = function (data) {
	var _father_this = this;
	
	_father_this.all_data_num.text(data);
	_father_this.all_page_num.text(Math.ceil(data / system_info.page_limit));
}


//创建详情页面
Weixin.prototype.create_details_fn = function ($url) {
	var _father_this = this;
	_father_this.init();
	
	_father_this.wxdetail.unbind();
	_father_this.wxdetail.click(function(){
		var _this = $(this);
		var weixin_id = _this.data('weixin_id');
		var post_data = {};
		post_data.account_id = weixin_id;
		post_data.is_type = system_info.is_celebrity;	//名人类型

		var result = System.ajax_post_setup($url,post_data,'JSON');
		
		if (result.status == 0) {
			create_pop_html(result.data)	
		}
	})
	
	//创建HTML
	var create_pop_html = function (data) {
		_father_this.init();
		
		_father_this.batchboxdetail.remove();
		
		var html = '';
	
html += '<div class="batchboxdetail none tl">';
html += '<div class="top-batchdetail l pr">';
html += '<div class="title pa">';
//html += '<i></i><span>全球头条新闻事件</span>';
html += '</div>';
html += '<span class="close cur pa"><img src="/App/Public/Advert/images/close.gif" /></span>';
html += '<ul class="fl">';
html += '<li class="li_a select"><strong>账号详情</strong></li>';
//html += '<li class="li_b"><strong>账号被占用时段</strong></li>';
html += '</ul>';
html += '</div>';
html += '<div class="mid-batchdetail l">';
html += '<div class="box01-batchdetail fl">';
html += '<table class="tab01-batchdetail">';
html += '<tr>';
html += '<td class="t1">账号ID：<em>'+data.bs_id+'</em></td>';
html += '<td class="t1">账号名：<em>'+data.bs_account_name+'</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">账号：<em>'+data.bs_weixinhao+'</em></td>';
if (data.bs_qr_code == '') {
	html += '<td class="t1">二维码：<em class="tooltip_tonus" data-src="/App/Public/Advert/images/wx_img02.gif" data-width="150px" data-height="150px" >查看</em></td>';
} else {
	html += '<td class="t1">二维码：<em class="tooltip_tonus" data-src="'+data.bs_qr_code+'" data-width="300px" data-height="300px" >查看</em></td>';
}
html += '</tr>';
html += '<tr>';
html += '<td class="t1">月订单：<em>'+data.bs_month_order_nub+'</em></td>';
html += '<td class="t1">周订单：<em>'+data.bs_week_order_num+'</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">单图文报价：<em>'+data.bs_dtb_money+'</em></td>';
html += '<td class="t1">多图文第一条报价：<em>'+data.bs_dtwdyt_money+'</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">多图文第二条报价：<em>'+data.bs_dtwdet_money+'</em></td>';
html += '<td class="t1">多图文第三条及N条报价：<em>'+data.bs_dtwqtwz_money+'</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">常见分类：<em>'+data.pg_cjfl_explain+'</em></td>';
html += '<td class="t1">地区：<em>'+data.pg_area_name+'</em></td>';
html += '</tr>';
html += '<td class="t1">粉丝量：<em>'+data.sy_fans_number+'</em></td>';
var account_on_explain = '';
if (data.sy_account_on == 0) {
	account_on_explain = '未认证'; 
} else if(data.sy_account_on == 1) {
	account_on_explain = '已认证'; 
} else {
	account_on_explain = '暂无数据'
}
html += '<td class="t1">账号是否认证：<em>'+account_on_explain+'</em></td>';
html += '</tr>';
html += '<td class="t1">受众性别：<em>男：>'+data.sy_audience_man+'%,女：>'+data.sy_audience_women+'%</em></td>';
html += '<td class="t1">周平均阅读数：<em>'+data.sy_read_number+'</em></td>';
html += '</tr>';
//html += '<tr>';
//html += '<td colspan="2">账号ID：<em>'+data.bs_id+'</em></td>';
//html += '</tr>';
//html += '<tr>';
//html += '<td colspan="2">账号分类：<em>资讯</em><em>时尚</em><em>服装箱包</em><em>服装</em></td>';
//html += '</tr>';
//html += '<tr>';
//html += '<td colspan="2">账号标签：<em>IT数码游戏</em><em>母婴资讯</em><em>留学教育</em><em>服装</em></td>';
//html += '</tr>';
html += '</table>';
//html += '<a href="#" class="btn graybtn fr">? 疑问建议</a>';
html += '</div>';
html += '<div class="box01-batchdetail none fl">';
html += '<div class="part01-data fl">';
html += '<strong>选择日期：</strong><span class="on">09月10日</span><span>09月11日</span><span>09月12日</span><span>09月13日</span>';
html += '</div>';
html += '<div class="datetip fl">明天（2014年09月12日） 账号的不可用时间段: </div>';
html += '<table class="tab01-date">';
html += '<tr>';
html += '<td class="t1">早晨：</td>';
html += '<td class="t2"></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">上午：</td>';
html += '<td class="t2"></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">中午：</td>';
html += '<td class="t2"> 12:30 -12:45</td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">下午：</td>';
html += '<td class="t2"> 17:30 -17:45</td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">晚上： </td>';
html += '<td class="t2"></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">凌晨： </td>';
html += '<td class="t2"></td>';
html += '</tr>';
html += '</table>';
html += '<p class="datetip gray fl">除以上时段外，其他时间均可用！</p>';
html += '<a href="#" class="btn graybtn fr">? 疑问建议</a>';
html += '</div>';
html += '</div>';
html += '</div>';
		
		_father_this.body.append(html);
		
		_father_this.init();
		
		_father_this.add_old_class();
		
		_father_this.tooltip_tonus_fn();	//加载二维码
		
		_father_this.batchboxdetail.popOn();

		
	}
	
}


//全选
Weixin.prototype.all_selected_fn = function () {
	var _father_this = this;

	_father_this.all_selected.click(function () {
		_father_this.init();
		var _this = $(this);
		var is_check = _this.data('is_check');
		
		if (is_check == 1) {
			_father_this.now_selected.prop('checked',true);
			_this.data('is_check',0);
		} else {
			_father_this.now_selected.prop('checked',false);
			_this.data('is_check',1);
		} 
	});
}


//批量添加数据源
Weixin.prototype.add_selected_box_fn = function () {
	var _father_this = this;
	
	_father_this.add_selected_box.click(function () {
		var selected_account = _father_this.get_list_selected_account();
		$.each(selected_account,function (i,n){
			_father_this.add_account_to_cart(n,true);
		});
	}); 
}
//添加单个到购物车流程控制
Weixin.prototype.add_one_selected_box_fn = function () {
	var _father_this = this;
	_father_this.add_one_selected_box.click(function () {
		var _this = $(this);
		_father_this.add_account_to_cart(_this.data('id'),_this.prop('checked'));
	});
	
}
//添加一个账号到购物车中
Weixin.prototype.add_account_to_cart = function (account_id,status) {
	var _father_this = this;
	_father_this.init();
	if (status == true) {
		//对已经在容器的微博账号不再添加，避免重复
		if (System.in_array(account_id,_father_this.get_order_vessel_ids()) == false) {
			var _now_account_x = $('.accounts_'+account_id);	//列表的行
			var _name = _now_account_x.find('.account_name').data('account_name');
			
			var _money1 = _now_account_x.find('.now_money').eq(0).data('money');
			var _money2 = _now_account_x.find('.now_money').eq(1).data('money');
			var _money3 = _now_account_x.find('.now_money').eq(2).data('money');
			var _money4 = _now_account_x.find('.now_money').eq(3).data('money');
			
			var html = '<li data-select_account_id="'+account_id+'" data-money1="'+_money1+'"  data-money2="'+_money2+'" data-money3="'+_money3+'" data-money4="'+_money4+'">';
			
			html += '<span class="del fr delet_account"></span><strong class="wxdetail" data-weixin_id="'+account_id+'">'+_name+'</strong>';
			
//			html += ' | 单图文('+_money1+')';
//			html += ' | 第一条('+_money2+')';
//			html += ' | 第二条('+_money3+')';
//			html += ' | 第3-N条('+_money4+')';
		
			html += '<p class="fr"> 单图 ('+_money1+')';
			html += ' | 多图文 ( '+_money2+' | '+_money3+' | '+_money4+' ) </p> ';
		
			html += '</li>';
			
			_father_this.account_selected.append(html);
		}
	} else {
		_father_this.account_selected.children('li').each(function () {
			var _li_this = $(this);
			 if(_li_this.data('select_account_id') == account_id) {
				 _li_this.remove();
			 }
		});
	}
	
	_father_this.set_order_data();
	
	_father_this.delet_account_fn();
	
	_father_this.close_order_vessel_fn();
	
	_father_this.confirm_order_fn();
	
	_father_this.create_details_fn(system_info.getAccountInfo);
	
	//_father_this.order_vessel.show();
	_father_this.mid_batch.show();
}
//获取选中的账号
Weixin.prototype.get_list_selected_account = function () {
	var _father_this = this;
	_account_ids = [];
	_father_this.now_selected.each(function () {
		var _this = $(this);
		if (_this.prop('checked') == true) {
			_account_ids.push(_this.data('id'))
		}
	});	
	return _account_ids;
}
//在订单容器中，获取已经选择的订单
Weixin.prototype.get_order_vessel_ids = function () {
	var _father_this = this;
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
Weixin.prototype.set_order_data = function () {
	var _father_this = this;	
	var money_sum_1 = 0;
	var money_sum_2 = 0;
	var money_sum_3 = 0;
	var money_sum_4 = 0;
	var account_sum = 0;
	_father_this.account_selected.children().each(function (){
		var _this = $(this);
		
		money_sum_1 += Number(_this.data('money1'));
		money_sum_2 += Number(_this.data('money2'));
		money_sum_3 += Number(_this.data('money3'));
		money_sum_4 += Number(_this.data('money4'));
		account_sum += 1;
	});
	
	var html = '<p>已选择<b class="account_num">'+account_sum+'</b>个账号</p>';
	html += '<ul class="cart_ul">';
	html += '<li>单图文：<b class="account_money">'+money_sum_1.toFixed(2)+'</b>元</li>';
	html += '<li>多图文第一条：<b class="account_money">'+money_sum_2.toFixed(2)+'</b>元</li>';
	html += '<li>多图文第二条：<b class="account_money">'+money_sum_3.toFixed(2)+'</b>元</li>';
	html += '<li>多图文第三-N：<b class="account_money">'+money_sum_4.toFixed(2)+'</b>元</li>';
	html += '</ul>';
	
	//var html = '已选择<b class="account_num">'+account_sum+'</b>个账号，计费： 单图文：<b class="account_money">'+money_sum_1+'</b>元， 多图文第一条：<b class="account_money">'+money_sum_2+'</b>元 ，多图文第二条：<b class="account_money">'+money_sum_3+'</b>元 ，多图文第三-N：<b class="account_money">'+money_sum_4+'</b>元';

	_father_this.account_all_html.html(html);

}
//删除一个已经选择的订单
Weixin.prototype.delet_account_fn = function () {
	var _father_this = this;
	_father_this.init();
	_father_this.delet_account.unbind();	//清除之前绑定的事件
	_father_this.delet_account.click(function () {
		var _this_parent = $(this).parent();
		
		_father_this.cancel_select_ipt_fn(_this_parent.data('select_account_id'));
		
		_this_parent.remove();
		
		_father_this.set_order_data();
	});
}
//关闭窗口
Weixin.prototype.close_order_vessel_fn = function () {
	var _father_this = this;
	_father_this.close_order_vessel.unbind();
	_father_this.close_order_vessel.click(function () {
		//弹窗插件
		//_father_this.order_vessel.hide();
		_father_this.mid_batch.hide();
	});
};
//确认提交订单
Weixin.prototype.confirm_order_fn = function () {	
	var _father_this = this;
	
	_father_this.confirm_order.unbind();
	_father_this.confirm_order.click(function () {
		var selected_account_ids = [];	//已选中的ID
		
		selected_account_ids = _father_this.get_order_vessel_ids();
		
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
}
//添加连接外带的账号到购物车中
Weixin.prototype.add_haved_to_cart = function () {
	var _father_this = this;
	_father_this.now_selected.prop('checked',true);
	var account_ids = _father_this.get_list_selected_account();
	$.each(account_ids,function (i,n) {
		_father_this.add_account_to_cart(n,true);
	});
}
//取消当前选中的复选框
Weixin.prototype.cancel_select_ipt_fn = function (account_id) {
	var _father_this = this;
	_father_this.init();
	_father_this.now_selected.each(function () {
		var _this = $(this);
		if (_this.data('id') == account_id) {
			_this.prop('checked',false);
			return false;
		}
	});
}


/*
//批量订单实际流程
Weixin.prototype.add_selected_account_to_vessel = function () {
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
				
				var _money1 = _now_account_x.find('.now_money').eq(0).data('money');
				var _money2 = _now_account_x.find('.now_money').eq(1).data('money');
				var _money3 = _now_account_x.find('.now_money').eq(2).data('money');
				var _money4 = _now_account_x.find('.now_money').eq(3).data('money');
				
				var html = '<li data-select_account_id="'+n+'" data-money1="'+_money1+'"  data-money2="'+_money2+'" data-money3="'+_money3+'" data-money4="'+_money4+'">';
				
				html += '<span class="del fr delet_account"></span><strong>'+_name+'</strong>';
				
				html += ' | 单图文('+_money1+')';
				html += ' | 第一条('+_money2+')';
				html += ' | 第二条('+_money3+')';
				html += ' | 第3-N条('+_money4+')';
				
				html += '</li>';
				
				_father_this.account_selected.append(html);
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
		
		var money_sum_1 = 0;
		var money_sum_2 = 0;
		var money_sum_3 = 0;
		var money_sum_4 = 0;
		var account_sum = 0;
		_father_this.account_selected.children().each(function (){
			var _this = $(this);
			
			money_sum_1 += Number(_this.data('money1'));
			money_sum_2 += Number(_this.data('money2'));
			money_sum_3 += Number(_this.data('money3'));
			money_sum_4 += Number(_this.data('money4'));
			account_sum += 1;
		});
		//account_all_html
		//_father_this.account_money.text(money_sum_4);
		//_father_this.account_num.text(account_sum);
		_father_this.account_all_html.html('已选择<b class="account_num">'+account_sum+'</b>个账号，计费： 单图文：<b class="account_money">'+money_sum_1+'</b>元， 多图文第一条：<b class="account_money">'+money_sum_2+'</b>元 ，多图文第二条：<b class="account_money">'+money_sum_3+'</b>元 ，多图文第三-N：<b class="account_money">'+money_sum_4+'</b>元');

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
*/

//订单排序
Weixin.prototype.orderspan_fn = function () {
	var _father_this = this; 
	
	_father_this.orderspan.click(function () {	//orderspan-select
		var _this = $(this);
		_father_this.orderspan.removeClass('curr');
		_this.addClass('curr');
		
		//排序
		var _sort_type = _this.data('sort_type');//排序类型
		_father_this.sort_table_fn(_sort_type);	//执行排序
	});
}

//排序表单
Weixin.prototype.sort_table_fn = function ($sort_type) {
	
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


////放大图片
//Weixin.prototype.tooltip_tonus_fn = function () {
//	var _father_this = this; 
//	_father_this.tooltip_tonus.tooltip({
//		delay: 0,
//		showURL: false,
//		bodyHandler: function() {
//			var _this = $(this);
//			var html = '<img src="'+_this.attr('src')+'" />';
//			return html;
//		}
//	});
//}


//放大图片
Weixin.prototype.tooltip_tonus_fn = function () {
	var _father_this = this; 
	_father_this.tooltip_tonus.tooltip({
		delay: 0,
		showURL: false,
		bodyHandler: function() {
			var _this = $(this);
			var src = _this.data('src');
			var width = _this.data('width'); 
			var height = _this.data('height');
			var html = '<img src="'+src+'" style="width:'+width+';height:'+height+'"/>';
			return html;
		}
	});
}


//页面记载完毕后执行的方法
Weixin.prototype.page_init_fn = function () {
	var _father_this = this;
	
	//对没有订单的情况进行隐藏
	if (_father_this.order_id.val() == '') {
		//_father_this.all_selected.css({'display':'none'});
		//_father_this.add_selected_box.css({'display':'none'});
		//_father_this.now_selected.remove();
	}
}

//根据选中的账号导出报价单
Weixin.prototype.export_csv_fn = function () {
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
			export_data.type = 3;
			_father_this.export_post_data(system_info.export_url,export_data);
		}
	});
}


//把导出记录保存到数据库中
Weixin.prototype.export_post_data = function (urL,post_data) {
	if (post_data == '' || urL == '' ) return false;

	var result = System.ajax_post_setup(urL,post_data,'JSON');
	
	return result;
}

//执行
Weixin.prototype.run = function () {
	var _father_this = this;
	
	_father_this.add_old_class();
	
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
var Weixin = new Weixin();
window.onload = function () {
	Weixin.init();
	Weixin.run();	
}




	
	