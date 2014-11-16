var init_check_form = function () {
	$(".generalize_form").Validform({
		btnSubmit:".btn_sub",
		btnReset:"#btn_reset",			//重置
		tiptype:1, 						//提示窗口
		showAllError:false,				//提示所有错误
		postonce:false,					//true：数据成功提交后，表单将不能再继续提交。false：关闭
		ajaxPost:false,					//是否使用ajax提交数据，true时，提交的地址就是action地址
		tipSweep:false ,				//默认为false，在各种tiptype下， 为true时提示信息将只会在表单提交时触发显示，各表单元素blur时不会触发信息提示；

		
		//表单验证之前，提交时执行的函数
		beforeCheck:function(curform){	
			//var TextArea1 = $('#TextArea1');
			//alert(TextArea1.val().length)
			//在表单提交执行验证之前执行的函数，curform参数是当前表单对象。
				//这里明确return false的话将不会继续执行验证操作;	
		},
			
		//表单验证成功，提交时执行的函数
		beforeSubmit:function(curform){
			//在验证成功后，表单提交前执行的函数，curform参数是当前表单对象。
			//这里明确return false的话表单将不会提交;
			//return false;
		},
		
		//在使用ajax提交表单数据时，数据提交后的回调函数。返回数据data是Json对象：
		callback:function(data){	////如果不是ajax方式提交表单，传入callback，这时data参数是当前表单对象，回调函数会在表单验证全部通过后执行，然后判断是否提交表单，如果callback里明确return false，则表单不会提交，如果return true或没有return，则会提交表单。		
			if (data.status == 0) {
				setTimeout(function(){
					$.Hidemsg(); //公用方法关闭信息提示框;显示方法是$.Showmsg("message goes here.");
				}, 2000);
			}
			
			//返回数据data是json格式，{"info":"demo info","status":"y"}
			//info: 输出提示信息;
			//status: 返回提交数据的状态,是否提交成功。如可以用"y"表示提交成功，"n"表示提交失败，在ajax_post.php文件返回数据里自定字符，主要用在callback函数里根据该值执行相应的回调操作;
			//你也可以在ajax_post.php文件返回更多信息在这里获取，进行相应操作；
			//ajax遇到服务端错误时也会执行回调，这时的data是{ status:**, statusText:**, readyState:**, responseText:** }；
			
			//这里执行回调操作;
			//注意：如果不是ajax方式提交表单，传入callback，这时data参数是当前表单对象，回调函数会在表单验证全部通过后执行，然后判断是否提交表单，如果callback里明确return false，则表单不会提交，如果return true或没有return，则会提交表单。
		}
		
	});

};

init_check_form();

(function () {	
	
	var timeSubject = $('.timeSubject');
	
	timeSubject.val(function () {
		//获取当前时间
		var date = new Date();		
		var now_date = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()+' '+(date.getHours() + 3 )+':'+date.getMinutes()+':'+date.getSeconds()
		
		return now_date;
	});
	
	//验证日期
	var check_date = function ($ojb) {
		var _this = $($ojb);
		
		//获取当前时间
		var date = new Date();		
		var now_date = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()
		//格式化当前时间
		var now_date_format = System.fomat_date(now_date);	 

		//获取选择的时间
		var select_date = System.fomat_date(_this.val());
					
		//24小时*60分钟*60秒*1000毫秒
		var sixteen_minutes = 16 * 60 * 1000;
		var fourteen_day = 14 * 24 * 60 * 60 * 1000;
		
		if (select_date - now_date_format <  sixteen_minutes
		|| 	
		   select_date - now_date_format > fourteen_day
		) {
			_this.val('');
			alert('请选择当前时间60分钟后，14天之内的时间');
		}	
	}
	
	//显示时间的选择
	timeSubject.ymdateplugin({
		//attr 属性 ，更多格式参加书本
		altField:'#otherField',			//同步元素日期到其他元素上
		dateFormat:'yy-mm-dd',		//日期格式设置
		minDate: new Date(),		//最小选择日期为今天
		changeYear:true,				//显示年份
		changeMonth:true,				//显示月份
		showMonthAfterYear:true,	//互换位置
		showTimePanel: true,
		
		onClose : function () {			//关闭窗口执行函数
			var _this = $(this);
			if (_this.data('check_type') == 'start') {
				check_date(this); 
			}	
		}
	});
})();


(function () {
	var dxfkjg = $('.dxfkjg');
	var iphone = $('.iphone');
	dxfkjg.click(function () {
		//var _this = $(this);
		is_show_iphone();
	});
	
	var is_show_iphone = function () {

		if (dxfkjg.eq(0).prop('checked') == true) {
			var html = '';
			html += '<td class="t1" ><span><strong>手机号：</strong></span></td>';
			html += '<td class="t2"><input type="text" class="text" name="dx_phone"  ignore="ignore" datatype="m" errormsg="请正确选择填写"  nullmsg="请填写手机号码" /><p class="writetip">请准确填写您的手机号码，以便及时短信告知您确认结果</p>';
			html += '</td>';
			iphone.append(html);
		} else {
			iphone.empty();
		}
		init_check_form();
	};
	is_show_iphone();
})();




var WeixinOrderAddGeneralize = function () {
	//this.tab01-Weixin = $('.tab01-Weixin tr:even');
	//this.now_page_url = {:U('/DMIN/')}
	
	this.obj_date = new Date();	
	this.now_date = this.obj_date.getFullYear()+'-'+(this.obj_date.getMonth()+1)+'-'+this.obj_date.getDate();
}


//初始化对象
WeixinOrderAddGeneralize.prototype.init = function () {
	this.tooltip_tonus = $('.tooltip_tonus');
	
	this.ggw_type = $('.ggw_type');	//广告位类型	
	this.phoneview_Id = $('#phoneview');	//预览事件按钮
	this.phonebox = $('.phonebox');	//容器
	
	this.top_view_li = $('.top-view li');
	this.part01_view = $('.part01-view');
	this.part01_detail = $('.part01-detail');
	
	//form
	this.title = $('input[name=title]');	//系统标题
	this.zhaiyao = $('textarea[name=zhaiyao]');	//系统摘要
	this.zw_info = $('textarea[name=zw_info]');	//正文
	this.ly_url = $('input[name=ly_url]');	//来源网址
	
	//pop
	this.pop_title = $('.pop_title');
	this.pop_time = $('.pop_time');
	this.pop_zhaiyao = $('.pop_zhaiyao');
	this.pop_dtw = $('.pop_dtw');
	this.pop_photoview = $('.pop_photoview');
	this.pop_ly_url = $('.pop_ly_url');
	
}


//获取当前选中的数据
WeixinOrderAddGeneralize.prototype.get_GgwType_Val_Fn = function () {
	var _father_this = this;
	
	var value = 0;
	_father_this.ggw_type.each(function () {
		var _this = $(this);
		if (_this.prop('checked') == true) {
			value = _this.val();
			return false;
		}
	});
	
	return value;
}


//预览事件触发器
WeixinOrderAddGeneralize.prototype.phoneview_Fn = function () {
	var _father_this = this;
	
	//预览
	_father_this.phoneview_Id.click(function(){
		
		_father_this.show_phoneview_fn(_father_this.get_GgwType_Val_Fn());
		
		_father_this.phonebox.popOn();
		
	});
	
	
	_father_this.top_view_li.click(function(){
		var _this = $(this);
		var i=_this.index();
		_this.addClass('select');
		_this.siblings().removeClass('select');
		_father_this.part01_view.hide();
		_father_this.part01_view.eq(i).show();
		_father_this.part01_detail.hide();
	})
	
	
	_father_this.part01_view.find('a').click(function(){
		_father_this.part01_view.hide();
		_father_this.part01_detail.show();
	})
	
}

WeixinOrderAddGeneralize.prototype.show_phoneview_fn = function ($type) {
	var _father_this = this;
	_father_this.init();
		
	//form_info
	var default_info = '博主自己更新的内容';
	var title_info = _father_this.title.val();	//标题信息
	var zhaiyao_info = _father_this.zhaiyao.val();	//摘要信息
	
	//defautl_info
	_father_this.pop_title.text(title_info);
	_father_this.pop_time.text(_father_this.now_date);
	_father_this.pop_zhaiyao.text(zhaiyao_info);
	
	
	if (_father_this.ly_url.val() != '') {
		_father_this.pop_ly_url.find('a').attr('href',_father_this.ly_url.val())
		_father_this.pop_ly_url.show(); 
	} else {
		_father_this.pop_ly_url.hide(); 
	}
	//pop_ly_url
	
	//public——css
	_father_this.top_view_li.removeClass('select');
	_father_this.part01_view.hide();
	_father_this.part01_detail.hide();
	_father_this.top_view_li.hide();
	
	//var stemTxt = CKEDITOR.instances.TextArea1.document.getBody().getText(); //取得纯文本 
	
	var stemHtml = CKEDITOR.instances.TextArea1.getData();	//获取HTML数据
	
	_father_this.pop_photoview.html(stemHtml);
	
	
	//单图文
	if ($type == 1) {
		
		_father_this.top_view_li.eq(0).addClass('select');
		
		_father_this.top_view_li.eq(0).show();
		
		_father_this.part01_view.eq(0).show();
		
	//多图文第一条	
	} else if ($type == 2) {
		
		_father_this.top_view_li.eq(1).addClass('select');
		
		_father_this.top_view_li.eq(1).show();
		
		_father_this.part01_view.eq(1).show();
		
		_father_this.pop_dtw.text(default_info);
		
	//多图文第二条	
	} else if ($type == 3) {
		
		_father_this.top_view_li.eq(1).addClass('select');
		
		_father_this.top_view_li.eq(1).show();
		
		_father_this.part01_view.eq(1).show();
		
		_father_this.pop_title.text(default_info);
		
		_father_this.pop_dtw.text(default_info);
		
		_father_this.pop_dtw.eq(0).text(title_info);
		
	//多图文第三-N条		
	} else if ($type == 4) {
		
		_father_this.top_view_li.eq(1).addClass('select');

		_father_this.top_view_li.eq(1).show();
		
		_father_this.part01_view.eq(1).show();
			
		_father_this.pop_title.text(default_info);
		
		_father_this.pop_dtw.text(default_info);
		
		
		for (var i=1;i<=_father_this.pop_dtw.size();i++) {
			_father_this.pop_dtw.eq(i).text(title_info);
		}
		
	}

	
}


//放大图片
WeixinOrderAddGeneralize.prototype.tooltip_tonus_fn = function () {
	var _father_this = this; 
	_father_this.tooltip_tonus.tooltip({
		delay: 0,
		showURL: false,
		bodyHandler: function() {
			var _this = $(this);
			var src = _this.data('src');
			var width = _this.data('width');
			var height = _this.data('height');
			var html = '<img src="'+src+'" width="'+width+'" height="'+height+'" />';
			return html;
		}
	});
}



//执行
WeixinOrderAddGeneralize.prototype.run = function () {
	var _father_this = this;
	
	_father_this.init();
	
	_father_this.phoneview_Fn();
	
	_father_this.tooltip_tonus_fn();
	
	//_father_this.phonebox.popOn();
}


//运行
var WeixinOrderAddGeneralize = new WeixinOrderAddGeneralize();
window.onload = function () {
	WeixinOrderAddGeneralize.init();
	WeixinOrderAddGeneralize.run();	
}

//$('#phoneview').click(function(){
//	$('.phonebox').popOn();
//})
//$('.top-view li').click(function(){
//	var i=$(this).index();
//	$(this).addClass('select');
//	$(this).siblings().removeClass('select');
//	$('.part01-view').hide();
//	$('.part01-view').eq(i).show();
//	$('.part01-detail').hide();
//})
//$('.part01-view a').click(function(){
//	$('.part01-view').hide();
//	$('.part01-detail').show();
//})


//(function () {
//	$('#phoneview').click(function(){
//		$('.phonebox').popOn();
//	})
//})();