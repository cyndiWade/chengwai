var System = function () {
	var i =1;
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


/**
 * 格式化日期，成时间戳
 * @param {Object} $date_string 2013-10-10 12:13
 * return 111111111111
 */
System.prototype.fomat_date = function ($date_string) {
	if ($date_string == '') {
		return 0;
	} else {
		return Date.parse($date_string.replace(/-/ig,'/'));
	}
	
}


System.prototype.run = function () {
	var _fater_this = this;
	_fater_this.validateImageFn();
}

var System = new System();

window.onload = function () {

	System.init();
	System.run();	
}
