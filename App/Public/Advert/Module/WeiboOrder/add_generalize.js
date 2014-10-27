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
	$(".timeSubject").ymdateplugin({
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

//短信控制
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


//转换类型切换HTML
(function () {
	var fslx_type = $('.fslx_type');	//转发类型
	var tr_zhifa = $('.tr_zhifa');	//直发
	var tr_zhuanfa = $('.tr_zhuanfa');	//转发
	
	
	fslx_type.change(function () {
		var _this = $(this);
		change_html(_this.val());
	});

		
	var change_html = function ($type) {
		if ($type == 1) {	//直发
			tr_zhuanfa.empty();
			tr_zhifa.empty();
			tr_zhuanfa.eq(0).append(add_html('zhuanfa1'));
			tr_zhuanfa.eq(1).append(add_html('zhuanfa2'));
			tr_zhuanfa.eq(2).append(add_html('zhuanfa3'));
			
		} else if ($type == 2) {	//转发
			tr_zhifa.empty();
			tr_zhuanfa.empty();
			tr_zhifa.eq(0).append(add_html('zhifa1'));
			tr_zhifa.eq(1).append(add_html('zhifa2'));
			tr_zhifa.eq(2).append(add_html('zhifa3'));
		} 
		init_check_form();
	}
	
	var add_html = function ($key) {
		var html_obj = {};
		
		//直发HTML
		html_obj.zhifa1 = '<td class="t1"><span><i>*</i><strong>转发链接：</strong></span></td>';
		html_obj.zhifa1 += '<td class="t2">';
		html_obj.zhifa1 += '<input type="text" name="zf_url" class="text zf_url" datatype="url" errormsg="请正确填写转发链接"  nullmsg="请填写转发链接"/>';
		html_obj.zhifa1+= '<div class="writetip"><span class="gray">例：http://weibo.com/1675718025/xhUXoBAJa </span>';
		html_obj.zhifa1 += '<!-- <a class="why blue" href="#">什么是转发链接? </a> -->';
		html_obj.zhifa1 += '</div>';
		html_obj.zhifa1 += '</td>';
			
		html_obj.zhifa2 = '<td class="t1"><span><i>*</i><strong>转发语类型：</strong></span></td>';
		html_obj.zhifa2	+=	'<td class="t2">';
		html_obj.zhifa2	+=	'<span class="soft fl"><input type="radio" name="zfy_type" class="radio zfy_type" value="1" datatype="*" errormsg="请正确选择"  nullmsg="请选择转发语类型" checked="checked"/><strong>指定转发语</strong></span>';
		html_obj.zhifa2	+=	'<span class="soft fl"><input type="radio" name="zfy_type" class="radio zfy_type" value="2" datatype="*" errormsg="请正确选择"  nullmsg="请选择转发语类型"/><strong>博主自拟转发语</strong></span>';
		html_obj.zhifa2	+=	'<span class="soft fl"><input type="radio" name="zfy_type" class="radio zfy_type" value="3" datatype="*" errormsg="请正确选择"  nullmsg="请选择转发语类型"/><strong>无转发语</strong></span>';
		html_obj.zhifa2	+=	'</td>'
			
		html_obj.zhifa3 = '<td class="t1"><span><strong>转发语：</strong></span></td>';
		html_obj.zhifa3 += '<td class="t2"><div class="forward"><textarea name="zw_info" class="textarea zw_info" ignore="ignore" datatype="*0-500" errormsg="请正确填写范围是10-500个字符" ></textarea><p>此内容会直接发出，请不要填写与转发语无关的信息。请不要超过140字，您还可以输入140个字</p></div>';
		html_obj.zhifa3 += '</td>'
		
			
		//转发HTML
		html_obj.zhuanfa1 = '<td class="t1"><span><i>*</i><strong>直发内容类型：</strong></span></td>';
		html_obj.zhuanfa1 += '<td class="t2"> <input type="radio" name="zfnr_type" class="zfnr_type" id="content_type_1" value="1" datatype="*" errormsg="请正确选择"  nullmsg="请选择直发内容类型" /> 指定直发内容';
		html_obj.zhuanfa1 += '<input type="radio" name="zfnr_type" class="zfnr_type" id="content_type_2" value="2" datatype="*" errormsg="请正确选择"  nullmsg="请选择直发内容类型" checked="checked"/> 博主自拟直发内容';
		html_obj.zhuanfa1 += '</td>';
			
		html_obj.zhuanfa2 = '<td class="t1"><span><strong>直发语：</strong></span></td>';
		html_obj.zhuanfa2 += '<td class="t2"><div class="forward"><textarea name="zfnr_info" class="textarea zfnr_info" ignore="ignore" datatype="*0-500" errormsg="请正确填写范围是10-500个字符" ></textarea><p>此内容会直接发出，请不要填写与转发语无关的信息。请不要超过140字，您还可以输入140个字</p></div>';
		html_obj.zhuanfa2 += '</td>'			
		
		html_obj.zhuanfa3 = '<td class="t1"><span><strong>直发配图：</strong></span></td>';
		html_obj.zhuanfa3 += '<td class="t2"> <input type="file" name="contentTypeRetweet" > ';
		html_obj.zhuanfa3 += '</td>';
			
		return html_obj[$key];
		
	}
	
	change_html(fslx_type.val());
	
})()

















