/*产品首页和产品列表页焦点图#focus_right*/
$(function(){
	var bWidth = $("#focus_right").width(); //获取banner宽度
	var picNumber = $("#focus_right .focus_list li").length;  //获取图片张数
	var index = 1;
	var picTimer;
	$("#focus_right .focus_list").css("width",bWidth*picNumber);  //设置图片列表的宽度
	var btn = "<ul class='btn'>";  //构建按钮的DOM
	for(var i=0; i < picNumber; i++) {
		btn += "<li><a href='javascript:;' class='focus_png png menu_home"+i+"'></a></li>";
	}
	btn += "</ul>";
	$(".fmenu").append(btn);
	$(".fmenu li").mouseenter(function() {  //按钮事件
		index = $(".fmenu li").index(this);
		
		showPics(index);
	}).eq(0).addClass("active");
	$("#focus_right").hover(function() {  //鼠标划入停止，划出继续动画
		clearInterval(picTimer);
		//$(".fmenu").animate({"bottom":8,"opacity":1},180);
	},function() {
		//$(".fmenu").animate({"bottom":-10,"opacity":0},180);
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == picNumber) {index = 0;}
		},4000); 
	}).trigger("mouseleave");
	function sbanner(index){  //图片滚动函数
		$(".fmenu li").removeClass("active").eq(index).addClass("active");
		$(".fmenu li").removeClass("active").eq(index).addClass("active");
	};
	function showPics(index){  //图片滚动函数
		var rollLeft = -index*bWidth;
		var rol_shwP = "banner"+index;
		$("#focus_right .focus_list").stop(true,false).animate({"left":rollLeft},300);
		$(".fmenu li").removeClass("active").eq(index).addClass("active");
		$("#banner").attr("class",rol_shwP);
	};
});