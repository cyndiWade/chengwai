var ajax_post_setup = function ($url,$data) {
	$.ajaxSetup({
		async: false,//async:false 同步请求  true为异步请求
	});
	var result = false;
	//提交的地址，post传入的参数
	$.post($url,$data,function(content){
		result = content;
	},'json');
	
	return result;
}