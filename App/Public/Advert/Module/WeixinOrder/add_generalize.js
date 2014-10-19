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


(function () {
	$('#phoneview').click(function(){
		$('.phonebox').popOn();
	})
	
	
})();