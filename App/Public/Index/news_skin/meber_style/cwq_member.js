<<<<<<< HEAD
﻿$(function(){
	$('.tabmenu li').click(function(){
		var liindex = $('.tabmenu li').index(this);
		$(this).addClass('select').siblings().removeClass('select');
		$('#listall>div').eq(liindex).fadeIn(150).siblings('#listall>div').hide();
	});
	$('.order_menu li').click(function(){
		var liindex = $('.order_menu li').index(this);
		$(this).addClass('cur').siblings().removeClass('cur');
		$('#order_list>div').eq(liindex).fadeIn(150).siblings('#order_list>div').hide();
	});
	/*收藏 | 拉黑 begin*/
	$("#typeall").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list");
	})
	$("#blacklist").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list tabg_blacklist");
	})
	$("#collect").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list tabg_collect");
	})
	
	$("#typeall2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list");
	})
	$("#blacklist2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list tabg_blacklist");
	})
	$("#collect2").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list tabg_collect");
	})
	/*收藏 | 拉黑 begin*/
	$("#mtypeall").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix");
	})
	$("#mblacklist").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix tabg_blacklist");
	})
	$("#mcollect").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix tabg_collect");
	})
	
	$("#mtypeall2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix");
	})
	$("#mblacklist2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix tabg_blacklist");
	})
	$("#mcollect2").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix tabg_collect");
	})
	
	
	$(".tips").hover(function(){
	  $(this).css({"z-index":"10"});
	  $(this).find(".tippover").show();
	},function(){
	  $(this).find(".tippover").hide();
	  $(this).css({"z-index":"1"});
	 });
});
=======
﻿$(function(){
	$('.tabmenu li').click(function(){
		var liindex = $('.tabmenu li').index(this);
		$(this).addClass('select').siblings().removeClass('select');
		$('#listall>div').eq(liindex).fadeIn(150).siblings('#listall>div').hide();
	});
	$('.order_menu li').click(function(){
		var liindex = $('.order_menu li').index(this);
		$(this).addClass('cur').siblings().removeClass('cur');
		$('#order_list>div').eq(liindex).fadeIn(150).siblings('#order_list>div').hide();
	});
	/*收藏 | 拉黑 begin*/
	$("#typeall").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list");
	})
	$("#blacklist").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list tabg_blacklist");
	})
	$("#collect").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list").attr("class","table_list tabg_collect");
	})
	
	$("#typeall2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list");
	})
	$("#blacklist2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list tabg_blacklist");
	})
	$("#collect2").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#table_list2").attr("class","table_list tabg_collect");
	})
	/*收藏 | 拉黑 begin*/
	$("#mtypeall").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix");
	})
	$("#mblacklist").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix tabg_blacklist");
	})
	$("#mcollect").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content").attr("class","w1200 clearfix tabg_collect");
	})
	
	$("#mtypeall2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix");
	})
	$("#mblacklist2").click(function (){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix tabg_blacklist");
	})
	$("#mcollect2").click(function(){
	  $(this).siblings("dd").removeClass("selected");
	  $(this).addClass("selected");
	  $("#list_content2").attr("class","w1200 clearfix tabg_collect");
	})
	
	
	$(".tips").hover(function(){
	  $(this).css({"z-index":"10"});
	  $(this).find(".tippover").show();
	},function(){
	  $(this).find(".tippover").hide();
	  $(this).css({"z-index":"1"});
	 });
});
>>>>>>> 826fca2e8df32f0881ad64b4cf6d6ab2a458cb68
