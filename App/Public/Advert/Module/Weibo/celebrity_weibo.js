var Weibo = function () {
	//this.tab01-weibo = $('.tab01-weibo tr:even');
	//this.now_page_url = {:U('/DMIN/')}
}

//初始化对象
Weibo.prototype.init = function () {
	this.select_ckbj_type = $('#select_ckbj_type');		//价格类型
	this.select_tags_vessel = $('.select_tags_vessel');	//选择标签容器Ul
		
	this.mrzy_tags = $('.mrzy_tags');		//名人职业
	this.mtly_tags = $('.mtly_tags');		//媒体领域
	this.jg_tags = $('.jg_tags');			//价格标签
	this.dfmr_mt_tags = $('.dfmr_mt_tags');	//地方名人/媒体：
	this.xqbq_tags = $('.xqbq_tags');		//兴趣标签
	
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
	
	this.order_vessel = $('.order_vessel');	//订单容器
	this.close_order_vessel = $('.close_order_vessel');	//关闭订单容器
	this.account_num = $('.account_num');//账号数
	this.account_money = $('.account_money');	//价格
	this.account_selected = $('.account_selected');	//已选择账号
	this.delet_account = $('.delet_account');	//删除账号
	this.confirm_order = $('.confirm_order');	//确认订单
	this.tbody = $('.tbody');	//微博账号容器
	this.orderspan = $('.orderspan');	//排序按钮
	
}


//添加相关样式
Weibo.prototype.add_table_class = function () {
	$('.tab01-weibo tr:even').addClass('even');
	
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
}

//点击创建标签
Weibo.prototype.select_tag_fn = function () {
	var _father_this = this;
	
	_father_this.mrzy_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});	
	});
	
	
	_father_this.mtly_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')
			
		});
		
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
	
	
	_father_this.xqbq_tags.click(function (){
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
	
	
	_father_this.mr_mtlb_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});
	});
	
	_father_this.phd_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});
	});
	
	_father_this.mr_fans_num_tags.click(function (){
		var _this = $(this);
		_father_this.create_selete_tags(_this.data('title'),_this.data('val'),{
			'tag_class' : _this.data('tag_class'),
			'tag_id':_this.data('tag_id'),
			'classify':_this.data('classify'),
			'field' : _this.data('field'),
			'repetition' : _this.data('repetition')	
		});
	});
	
	_father_this.zhyc_tags.click(function (){
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
Weibo.prototype.create_selete_tags = function ($title,$val,$extend) {
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
Weibo.prototype.init_tags_selected = function () {
	var _father_this = this;
	_father_this.init();
	
	//为已选标签加上首选或者清除首选
	_father_this.search_tag_data.each(function () {
		var _this = $(this);

		_father_this.mrzy_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.mrzy_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		_father_this.mtly_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.mtly_tags.removeClass("select");
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
		
		
		_father_this.xqbq_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.xqbq_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		
		_father_this.mr_mtlb_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.mr_mtlb_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		
		_father_this.phd_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.phd_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		
		_father_this.mr_fans_num_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.mr_fans_num_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		
		_father_this.zhyc_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				_father_this.zhyc_tags.removeClass("select");
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
Weibo.prototype.del_select_tag_fn = function () {
	var _father_this = this;
	_father_this.init();
	
	_father_this.delete_tags.click(function () {
		$(this).parent().remove();
		_father_this.bu_xian_init_fn(this);
	});

}


//删除标签执行的方法
Weibo.prototype.bu_xian_init_fn = function (obj) {
	var _father_this = this;
	_father_this.init();
	
	var now_class = $(obj).data('tag_class');
	$('.'+now_class).removeClass("select");
	$('.'+now_class).eq(0).addClass("select");
	_father_this.init_tags_selected();
	
}

//点击确认安按钮
Weibo.prototype.btn_click_create_tags = function () {
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
Weibo.prototype.get_selected_tags = function () {
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
Weibo.prototype.clear_tags_fn = function () {
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
Weibo.prototype.three_sidebar_type_fn = function () {
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
Weibo.prototype.get_three_sidebar_selected = function () {
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
Weibo.prototype.search_fn = function () {
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
Weibo.prototype.get_search_account = function () {
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
Weibo.prototype.lahei_and_shoucang_fn = function ($urL) {
	var _father_this = this;
	_father_this.init();
	_father_this.lahei_and_shoucang.click(function () {
		if (confirm('确认操作?') == false) return false; 
		var _this = $(this);
		var post_data = {
			'action' : _this.data('action'),
			'pt_type' :system_info.pt_type,	
			'is_celebrity' :system_info.is_celebrity,
			'or_type' : _this.data('or_type'),
			'weibo_id' : _this.data('weibo_id')
		};
		var result = System.ajax_post_setup($urL,post_data,'JSON');
		
		if (result.status == 1) {
			alert(result.msg);
			public_post_fn({});
		} else {
			alert(result.msg);
		}
	
	});
}


//更新分页记录条数，和页数
Weibo.prototype.dataNum_And_PageNum = function (data) {
	var _father_this = this;
	
	_father_this.all_data_num.text(data);
	_father_this.all_page_num.text(Math.ceil(data / system_info.page_limit));
}


//创建列表HTML
Weibo.prototype.create_now_html = function (result,$post_data) {
	var _father_this = this;
	_father_this.list_content.empty();

	for (var key in result) {
		var $data = result[key];
		var html = '';
		html += '<div class="box01-cele fl accounts_'+$data.bs_id+'" data-order_id="'+$data.bs_id+'" data-order_num="'+$data.bs_week_order_num+'"  data-fans_num="'+$data.bs_fans_num+'">';
		html += '<input type="checkbox" class="check fl now_selected" data-field="id" data-id="'+$data.bs_id+'" />';
		html += '<div class="part01-cele fl">';
		
		if ($data.bs_head_img == '') {
			html += '<img src="'+system_info.Global_Resource_Path+'images/default_head.jpg" />';
		} else {
			html += '<img src="'+$data.bs_head_img+'" class="mtimg" />';
		}
		
		html += '<div class="ctrl">';

		if ($post_data.cksc == undefined) {
			html += '<span class="lahei_and_shoucang" data-action="add" data-or_type="1" data-weibo_id="'+$data.bs_id+'">收藏</span>';
		}else if ($post_data.cksc == 1) {
			html += '<span class="lahei_and_shoucang" data-action="del" data-or_type="1" data-weibo_id="'+$data.bs_id+'">取消收藏</span>';
		}
		
		if ($post_data.ckhmd == undefined) {
			html += '<span class="lahei_and_shoucang" data-action="add" data-or_type="0" data-weibo_id="'+$data.bs_id+'">拉黑</span>';
		}else if ($post_data.ckhmd == 1) {
			html += '<span class="lahei_and_shoucang" data-action="del" data-or_type="0" data-weibo_id="'+$data.bs_id+'">取消拉黑</span>';
		}
		
		html += '</div>';
		html += '</div>';
		html += '<div class="part02-cele fl">';
		html += '<div class="grp01-cele l">';
		html += '<span class="mrdetail cur fr" data-weibo_id="'+$data.bs_id+'">查看详情</span><i class="weibo fl"></i><i class="v fl"></i><span class="femail fl account_name" data-account_name="'+$data.bs_account_name+'">'+$data.bs_account_name+'</span>';
		html += '<span class="city fl">'+$data.pg_cirymedia_explain+'</span>';
		//html += '<span class="yxl fl">影响力：1212</span>';
		html += '</div>';
		html += '<div class="grp02-cele l">';
		html += '<ul class="arr01-cele l">';
		html += '<li><span class="blue">职业：</span>'+$data.pg_occupation_explain+' <span class="blue">领域：</span><em>'+$data.pg_field_explain+'</em></li>';
		html += '<li><span class="blue">粉丝量：</span><b class="red">'+($data.bs_fans_num / 10000)+'万</b></li>';
		html += '<li><span class="blue">参考报价：</span><b class="red now_money" data-money="'+($data.bs_ck_money)+'">'+($data.bs_ck_money / 10000)+'万</b></li>';
		html += '<li><span class="blue">配合度：</span>'+$data.pg_phd_explain+'</li>';
		html += '</ul>';
		html += '<div class="arr02-cele l">';
		html += '<div class="tem01-cele fr">';
		//html += '<span class="blue">预约小贴士：</span>影视演员影视演员影视演员影视演员影视演员影视演员';
		html += '</div>';
		html += '<div class="tem02-cele fl">';
		html += $data.bs_introduction;
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
	
		_father_this.list_content.append(html);
	}
	
}


//创建详情页面
Weibo.prototype.create_details_fn = function ($url) {
	var _father_this = this;
	_father_this.init();
	
	_father_this.mrdetail.click(function(){
		var _this = $(this);
		var weibo_id = _this.data('weibo_id');
		
		var post_data = {};
		post_data.account_id = weibo_id;
		post_data.is_type = system_info.is_celebrity;
		var result = System.ajax_post_setup($url,post_data,'JSON');
		
		if (result.status == 0) {
			create_pop_html(result.data)	
		}
	})
	
	//创建HTML
	var create_pop_html = function (data) {
		//console.log(data)
		_father_this.init();
		
		_father_this.batchboxdetail.remove();
		
		var html = '';
		html += '<div class="batchboxdetail none tl">';
		html += '<div class="top-batchdetail l pr">';
		html += '<span class="close cur pa"><img src="/App/Public/Advert/images/close.gif" /></span>';
		html += '<ul class="mrnav fl">';
		html += '<li class="select">名人基本信息</li>	';
		html += '</ul>';
		html += '</div>';
		html += '<div class="mid-batchdetail l">';
		html += '<div class="box01-mrfx">';
		html += '<div class="part01-mrfx fl">';
		html += '<div class="grp01 fl">';
		//html += '<img src="App/Public/Advert/images/mr_img01.gif" />';
		if (data.bs_head_img == '') {
			html += '<img src="'+system_info.Global_Resource_Path+'images/default_head.jpg" />';
		} else {
			html += '<img src="'+data.bs_head_img+'" />';
		}
		
		html += '</div>';
		html += '<div class="grp02 fl">';
		html += '<h6><b>'+data.bs_account_name+'</b>的详细信息</h6>';
		html += '<table class="tab01-mr">';
		html += '<tr>';
		html += '<td class="t1">国籍:</td>';
		html += '<td class="t2">中国</td>';
		html += '</tr>';
		html += '<tr>';
		html += '<td class="t1">主要成就:</td>';
		html += '<td class="t2">暂无数据</td>';
		html += '</tr>';
		html += '</table>';
		html += '</div>';
		html += '</div>';
		html += '<div class="part02-mrfx fl">';
		html += '<h6><strong>履历介绍</strong></h6>';
		html += '<p>'+data.bs_introduction+'</p>';
		html += '</div>';
		html += '<div class="part02-mrfx fl">';
		html += '<h6><strong>账号信息</strong></h6>';
		html += '<div class="info"><span>周订单： <b class="red">'+data.bs_week_order_num+'</b></span><span>月订单： <b class="red">'+data.bs_month_order_nub+'</b></span></div>';
		html += '</div>';
		html += '</div>';		
		html += '</div>';
		html += '</div>';
		
		_father_this.body.append(html);
		
		_father_this.init();
		
		_father_this.batchboxdetail.popOn();

	}
	
}


//全选
Weibo.prototype.all_selected_fn = function () {
	var _father_this = this;

	_father_this.all_selected.click(function () {
		_father_this.init();
		_father_this.now_selected.prop('checked',true);
	});
}

//批量添加数据源
Weibo.prototype.add_selected_box_fn = function () {
	var _father_this = this;
	var _account_ids;	//账号IDs
	
	//点击批量添加账号时
	_father_this.add_selected_box.click(function () {
		if (confirm('确认操作？') == false) return false;
		
		_father_this.init();
		_account_ids = [];
		_father_this.now_selected.each(function () {
			var _this = $(this);
			if (_this.prop('checked') == true) {
				_account_ids.push(_this.data('id'))
			}
		});
		if (_account_ids == '') {
			alert('请选择账号！');
			return false;
		} else {
			cache_select_account();
		}
	});
	

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
				_father_this.account_selected.append('<li data-select_account_id="'+n+'" data-money="'+_money+'"><span class="del fr delet_account"></span><strong>'+_name+'</strong></li>');
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
	
	
	//确认提交订单
	_father_this.confirm_order_fn = function () {
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
					alert('提交成功！');
					window.location.href= result.data.go_to_url;	//跳转
				} else {
					alert('添加失败请稍后重新再试！');
					return false;
				}
			}
			
		});
	}();
	
	
	//关闭窗口
	var close_order_vessel_fn = function () {
		_father_this.close_order_vessel.click(function () {
			//弹窗插件
			_father_this.order_vessel.hide();
		});
	}();
}

Weibo.prototype.orderspan_fn = function () {
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
Weibo.prototype.sort_table_fn = function ($sort_type) {
	
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
Weibo.prototype.page_init_fn = function () {
	var _father_this = this;
	
	//对没有订单的情况进行隐藏
	if (_father_this.order_id.val() == '') {
		_father_this.all_selected.css({'display':'none'});
		_father_this.add_selected_box.css({'display':'none'});
		_father_this.now_selected.remove();
	}
}



//执行
Weibo.prototype.run = function () {
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
}




//运行
var Weibo = new Weibo();
window.onload = function () {
	Weibo.init();
	Weibo.run();	
}




	
	