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
	
	this.three_sidebar_type = $('.three_sidebar_type');	//三级导航选项
	
	this.search = $('#search');				//search按钮
	this.search_account = $('.search_account');		//搜索的值
	
	this.lahei_and_shoucang = $('.lahei_and_shoucang');
	
	this.all_data_num = $('#all_data_num');	//所有数据
	this.all_page_num = $('#all_page_num');	//所有分页数
	
	this.body = $('body');
	this.list_content = $('#list_content');
	this.wxdetail = $('.wxdetail');
	this.batchboxdetail = $('.batchboxdetail');
}


//添加相关样式
Weixin.prototype.add_old_class = function () {
	$('.tab01-Weixin tr:even').addClass('even');
	
	$('#batch').click(function(){
		$('.batchbox').popOn();
	})
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
	})
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
		_father_this.ipt_jiage_start.val(ipt_val[0]);
		_father_this.ipt_jiage_over.val(ipt_val[1]);
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
		var ipt_val = _this.data('val').split("-");
		_father_this.ipt_fansNum_start.val(ipt_val[0]);
		_father_this.ipt_fansNum_over.val(ipt_val[1]);
	});
	
	_father_this.btn_fansNum_yes.click(function () {
		var _this = $(this);
		var tag_class = _this.data('tag_class');
		var obj = $('.'+tag_class).eq(1);
		var start = _father_this.ipt_fansNum_start.val() ? _father_this.ipt_fansNum_start.val() : 0;
		var over = _father_this.ipt_fansNum_over.val() ? _father_this.ipt_fansNum_over.val() : 10000000;
		var val = start + '-' + over;
		
		_father_this.create_selete_tags(val+'个',val,{
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
		
		_father_this.create_selete_tags(val+'元',val,{
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
		var _this = $(this);
		_this.addClass("select");
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
		var post_data = {
			'is_celebrity' :system_info.is_celebrity,
			'or_type' : _this.data('or_type'),
			'weixin_id' : _this.data('weixin_id')
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
Weixin.prototype.dataNum_And_PageNum = function (data) {
	var _father_this = this;
	
	_father_this.all_data_num.text(data);
	_father_this.all_page_num.text(Math.ceil(data / system_info.page_limit));
}


//创建详情页面
Weixin.prototype.create_details_fn = function ($url) {
	var _father_this = this;
	_father_this.init();
	
	_father_this.wxdetail.click(function(){
		var _this = $(this);
		var weibo_id = _this.data('weibo_id');
		var post_data = {};
		post_data.weibo_id = weibo_id;
		var result = System.ajax_post_setup($url,post_data,'JSON');
		
		if (result.status == 1) {
			create_pop_html(result)	
		}
	})
	
	//创建HTML
	var create_pop_html = function (data) {
		_father_this.init();
		
		_father_this.batchboxdetail.remove();
		
		var html = '';
	
html += '<div class="batchboxdetail none tl">';
html += '<div class="top-batchdetail l pr">';
html += '<div class="title pa"><i></i><span>全球头条新闻事件</span></div>';
html += '<span class="close cur pa"><img src="App/Public/Advert/images/close.gif" /></span>';
html += '<ul class="fl">';
html += '<li class="li_a select"><strong>账号详情</strong></li><li class="li_b"><strong>账号被占用时段</strong></li>';
html += '</ul>';
html += '</div>';
html += '<div class="mid-batchdetail l">';
html += '<div class="box01-batchdetail fl">';
html += '<table class="tab01-batchdetail">';
html += '<tr>';
html += '<td class="t1">月订单：<em>54646</em></td>';
html += '<td class="t1">周订单：<em>54646</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">万粉丝硬广转发单价：<em>54646</em></td>';
html += '<td class="t1">万粉丝软广转发单价：<em>54646</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">万粉丝硬广直发单价：<em>54646</em></td>';
html += '<td class="t1">万粉丝软广直发单价：<em>54646</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">月流单率：<em>暂无报价</em></td>';
html += '<td class="t1">月拒单率：<em>5%</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td class="t1">月合格率：<em>5%</em></td>';
html += '<td class="t1">是否接硬广：<em>是</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td colspan="2">账号ID：<em>1903188000</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td colspan="2">账号分类：<em>资讯</em><em>时尚</em><em>服装箱包</em><em>服装</em></td>';
html += '</tr>';
html += '<tr>';
html += '<td colspan="2">账号标签：<em>IT数码游戏</em><em>母婴资讯</em><em>留学教育</em><em>服装</em></td>';
html += '</tr>';
html += '</table>';
html += '<a href="#" class="btn graybtn fr">? 疑问建议</a>';
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
		
		_father_this.batchboxdetail.popOn();

	}
	
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
}



//运行
var Weixin = new Weixin();
window.onload = function () {
	Weixin.init();
	Weixin.run();	
}




	
	