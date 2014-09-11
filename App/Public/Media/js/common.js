// JavaScript Document
$(function(){
	$('.topli').hover(function(){$(this).find('.downnav').show();$(this).find('.topa').addClass('on');$(this).siblings().find('.topa').removeClass('on')},function(){$(this).find('.downnav').hide();})
	$('#slides').slides({
		preload: true,
		generateNextPrev:false,
		play:5000,
		pause:2500,
		hoverPause:true
	});
})