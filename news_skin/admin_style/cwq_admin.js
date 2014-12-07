$(function(){
	$('#admin_index li').click(function(){
		var liindex = $('#admin_index li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#index_list>div').eq(liindex).fadeIn(150).siblings('#index_list>div').hide();
	});
	$('#admin_order li').click(function(){
		var liindex = $('#admin_order li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#order_list>div').eq(liindex).fadeIn(150).siblings('#order_list>div').hide();
	});
	$('#admin_account li').click(function(){
		var liindex = $('#admin_account li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#account_list>div').eq(liindex).fadeIn(150).siblings('#account_list>div').hide();
	});
	$('#admin_category li').click(function(){
		var liindex = $('#admin_category li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#category_list>div').eq(liindex).fadeIn(150).siblings('#category_list>div').hide();
	});
	$('#admin_help li').click(function(){
		var liindex = $('#admin_help li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#help_list>div').eq(liindex).fadeIn(150).siblings('#help_list>div').hide();
	});
	$('#admin_sysinfo li').click(function(){
		var liindex = $('#admin_sysinfo li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#sysinfo_list>div').eq(liindex).fadeIn(150).siblings('#sysinfo_list>div').hide();
	});
	$('#admin_user li').click(function(){
		var liindex = $('#admin_user li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#user_list>div').eq(liindex).fadeIn(150).siblings('#user_list>div').hide();
	});
	$('#admin_webs li').click(function(){
		var liindex = $('#admin_webs li').index(this);
		$(this).addClass('active').siblings().removeClass('active');
		$('#webs_list>div').eq(liindex).fadeIn(150).siblings('#webs_list>div').hide();
	});
});
