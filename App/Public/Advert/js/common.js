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



function DrawImage(ImgD,FitWidth,FitHeight){

     var image=new Image();

     image.src=ImgD.src;

     if(image.width>0 && image.height>0){

         if(image.width/image.height>= FitWidth/FitHeight){

             if(image.width>FitWidth){

                 ImgD.width=FitWidth;

                 ImgD.height=(image.height*FitWidth)/image.width;

             }else{

                 ImgD.width=image.width; 

                ImgD.height=image.height;

             }

         } else{

             if(image.height>FitHeight){

                 ImgD.height=FitHeight;

                 ImgD.width=(image.width*FitHeight)/image.height;

             }else{

                 ImgD.width=image.width; 

                ImgD.height=image.height;

             } 

        }

     }

 }