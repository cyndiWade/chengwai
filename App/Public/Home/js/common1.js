$(function(){
	
	$(".close").click(function(){
		$(this).parents(".show").hide()
		$(this).parents().siblings(".backdiv").fadeOut()
			
	})
	
	
	$(".photo1").click(function(){
		$(".backdiv").show()
		$(".showbox1").fadeIn();
		mm1()
		var h =$(".showbox1").height()
		$(".showbox1").css({"margin-top":-(h/2)});
		
	})
	
	
	$(".detail").click(function(){
		$(".backdiv").show()
		$(".showbox2").fadeIn()	
		
		var h =$(".showbox2").height()
		$(".showbox2").css({"margin-top":-(h/2)});
	})	
	
	
	$(".detail2").click(function(){
		$(".backdiv").show()
		$(".showbox3").fadeIn()	
		mm2()
		var h =$(".showbox3").height()
		$(".showbox3").css({"margin-top":-(h/2)});
	})	
	
	
	
	
	//日历选择
	/*$('#checkinday').Zebra_DatePicker({
	  direction: true,
	  pair: $('#checkoutday')
	});
	$('#checkoutday').Zebra_DatePicker({
	  direction: 1
	});
*/

	
	 
})



var mm1= function(){
	window.mySwipe = $("#s1").Swipe({auto: 3000}).data("Swipe");	
}
var mm2= function(){
	window.mySwipe = $("#s2").Swipe({auto: 3000}).data("Swipe");	
}












