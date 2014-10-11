var System = function () {
	
}

System.prototype.init = function () {
	this.validateImage = $('.validateImage');
}



System.prototype.validateImageFn = function () {
	var _fater_this = this;
	_fater_this.validateImage.click(function () {
		var _this = $(this);
		_this.attr('src',_this.data('image_url')+'&'+Math.random());
	});
}

/**
 * 同步模式AJAX提交
 */
System.prototype.ajax_post_setup = function ($url,$data,$type) {
	$type = $type || 'JSON';
    $.ajaxSetup({
		async: false,//async:false 同步请求  true为异步请求
	});
	var result = false;
	//提交的地址，post传入的参数
	$.post($url,$data,function(content){
		result = content;
	},$type);
	
	return result;
}



System.prototype.run = function () {
	var _fater_this = this;
	_fater_this.validateImageFn();
}

/**
 * @desc 判断数组内是否包含字符串
 * @param str
 * @param arr
 * @returns {boolean}
 */
System.prototype.in_array = function (str, arr) {
    var i = arr.length;
    while (i--) {
        if (arr[i] === str) {
            return true;
        }
    }
    return false;
}

var System = new System();
window.onload = function () {
	System.init();
	System.run();	
}
