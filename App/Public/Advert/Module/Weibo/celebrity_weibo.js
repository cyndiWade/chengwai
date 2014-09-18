$(function(){
	$('.tab01-weibo tr:even').addClass('even');
	$('#batch').click(function(){
		$('.batchbox').popOn();
	})
	$('.top-search strong').click(function(){
		$(this).siblings().removeClass('on')
		$(this).addClass('on');
	})
	$('.mrdetail').click(function(){
		$('.batchboxdetail').popOn();
	})
	$('#morecate').click(function(){
		$('.part01-cate').toggleClass('part01-cate-auot');
	})
	$('#morecity').click(function(){
		$('.part01-city').toggleClass('part01-city-auot');
	})
	$('#moreta').click(function(){
		$('.part01-xqbq').toggleClass('part01-city-auot');
	})
	
})