var Weibo = function () {
	//this.tab01-weibo = $('.tab01-weibo tr:even');
	//this.now_page_url = {:U('/DMIN/')}
}

//初始化对象
Weibo.prototype.init = function () {
	this.select_tags_vessel = $('.select_tags_vessel');	//选择标签容器Ul
	
	this.cjfl_tags = $('.cjfl_tags');		//常见分类标签
	this.jg_tags = $('.jg_tags');			//价格分类标签
	this.fans_num_tags = $('.fans_num_tags');	//粉丝数量标签
	this.fans_sex_tags = $('.fans_sex_tags');	//粉丝性别标签
	
	this.search_tag_data = $('.search_tag_data');	//已选标签
	//history.pushState({a:1},'123','baidu');
	this.delete_tags = $('.delete_tags');			//删除标签
	
	this.resultbox = $('.resultbox');				//标签框体
	
	this.btn_jiage_yes = $('.btn_jiage_yes');		//价格按钮
	this.ipt_jiage_start = $('.ipt_jiage_start');	//开始价格
	this.ipt_jiage_over = $('.ipt_jiage_over');		//结束价格
	
	this.btn_fansNum_yes = $('.btn_fansNum_yes');	//粉丝按钮
	this.ipt_fansNum_start = $('.ipt_fansNum_start');//开始粉丝数
	this.ipt_fansNum_over = $('.ipt_fansNum_over');	//结束粉丝数
}


//添加相关样式
Weibo.prototype.add_table_class = function () {
	$('.tab01-weibo tr:even').addClass('even');
	$('#batch').click(function(){
		$('.batchbox').popOn();
	})
	$('.top-search strong').click(function(){
		$(this).siblings().removeClass('on')
		$(this).addClass('on');
	})
	$('#morecate').click(function(){
		$('.part01-cate').toggleClass('part01-cate-auot');
	});
}

//点击创建标签
Weibo.prototype.select_tag_fn = function () {
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
	
	_father_this.fans_sex_tags.click(function (){
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
	
	var search_tag_data = _father_this.search_tag_data;	//以选中的标签字段数据input数据

	var cjfl_tags = _father_this.cjfl_tags;	//常见分类
	var jg_tags = _father_this.jg_tags;		//价格分类
	var fans_num_tags = _father_this.fans_num_tags;	//粉丝数
	var fans_sex_tags = _father_this.fans_sex_tags;	//粉丝性别

	//为已选标签加上首选或者清除首选
	search_tag_data.each(function () {
		var _this = $(this);
		
		//处理常见分类数据	
		cjfl_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
		 		now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				cjfl_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
	
		//处理价格标签
		jg_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				jg_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
		
		//处理粉丝数量标签
		fans_num_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				fans_num_tags.removeClass("select");
				if (_this.data('val') == '') {
					_this.parent().remove();
				}
				now_this.addClass("select");
				return false;
			}
		});
	
		//处理粉丝性别数据
		fans_sex_tags.each(function () {
			var now_this = $(this);
			if (
				now_this.data('classify') == _this.data('classify') &&
				
				now_this.data('title') == _this.data('title') && 
				
				now_this.data('val') == _this.data('val')
			) {
				fans_sex_tags.removeClass("select");
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
	
	public_post_fn();
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
		var val = _father_this.ipt_jiage_start.val() + '-' + _father_this.ipt_jiage_over.val();
		
		_father_this.create_selete_tags(val+'元',val,{
			'tag_class' : obj.data('tag_class'),
			//'tag_id':obj.data('tag_id'),
			'classify':obj.data('classify'),
			'field' : obj.data('field'),
			'repetition' : obj.data('repetition')
		});
	});
	
	//价格按钮
	_father_this.btn_fansNum_yes.click(function () {
		var _this = $(this);
		var tag_class = _this.data('tag_class');
		var obj = $('.'+tag_class).eq(1);
		var val = _father_this.ipt_fansNum_start.val() + '-' + _father_this.ipt_fansNum_over.val();
		
		_father_this.create_selete_tags(val+'万',val,{
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
	var search_tag_data = _father_this.search_tag_data;
	
	var result = {};
	if (search_tag_data.size() > 0 ){
		search_tag_data.each (function () {
			var _this = $(this);
			result[_this.data('field')] = _this.data('val');
		});
	
	}
	return result;
}



//执行
Weibo.prototype.run = function () {
	var _father_this = this;
	
	_father_this.add_table_class();
	
	_father_this.select_tag_fn();	//点击创建标签
	
	_father_this.init_tags_selected();	//加首选
	
	_father_this.btn_click_create_tags();
}


//运行
var Weibo = new Weibo();
window.onload = function () {
	Weibo.init();
	Weibo.run();	
}



