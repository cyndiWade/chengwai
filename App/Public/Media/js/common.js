function isReg(string) 
{
    return (/^[0-9a-z \u4e00-\u9fa5]{3,30}$/gi).test(string) !== false;
}
function checkPhone(string) 
{
    return (/^1[358]\d{9}$/).test(string) !== false;
}
function checkQQ(string) 
{
    return (/^([1-9][0-9]{4,16}$)/).test(string) !== false;
}
function checkEmail(string) 
{
    return (/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/).test(string) !== false;
}


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