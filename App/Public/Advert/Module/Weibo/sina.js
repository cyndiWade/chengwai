var Weibo = function () {
	//this.tab01-weibo = $('.tab01-weibo tr:even');
	//this.
}

//初始化对象
Weibo.prototype.init = function () {
	this.select_tags_vessel = $('.select_tags_vessel');	//选择标签容易
	
	this.delete_tags = $('.delete_tags');				//
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


//创建常见分类
Weibo.prototype.create_selete_tags = function ($type,$title,$val) {
	
	var tas_html = '';
	tas_html += '<li>';
	tas_html += '<strong>'+$type+' &gt; <b>'+$title+'</b></strong>';
	tas_html += '<em class="del delete_tags"></em>';
	tas_html += '<input type="text" class="search_tag_data" value="'+$val+'"/>';
	tas_html += '</li>';
	
	this.select_tags_vessel.append(tas_html);
	
	
}




//执行
Weibo.prototype.run = function () {
	var _fater_this = this;
	
	_fater_this.add_table_class();
	
	_fater_this.create_selete_tags('常见分类','时尚','100-400');
}


//运行
var Weibo = new Weibo();
window.onload = function () {
	Weibo.init();
	Weibo.run();	
}



