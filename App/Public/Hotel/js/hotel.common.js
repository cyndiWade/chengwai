// JavaScript Document



$(function(){
	// 登录
	
	$(".login_name").focus(function(){
		
		if($(this).val() == this.defaultValue){
			$(this).val("").css("color","#333");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val(this.defaultValue).css("color","#999");
		}
	});
	
	$(".login_btn").hover(function(){
		$(this).addClass("login_btn_hover");
	},function(){
		$(this).removeClass("login_btn_hover");
	});
	
	$(".menulist1").hover(function(){
			$(this).find(".menulist_cont1").stop(true,true).slideDown(100)
		},function(){
			$(this).find(".menulist_cont1").stop(true,true).slideUp(100)
		})
	
	$(".hotle_list tr").hover(function(){
		$(this).addClass("hover")	
	},function(){
		$(this).removeClass("hover")	
	})
	
	
});