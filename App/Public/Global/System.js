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


System.prototype.run = function () {
	var _fater_this = this;
	_fater_this.validateImageFn();
}



var System = new System();
window.onload = function () {
	System.init();
	System.run();	
}
