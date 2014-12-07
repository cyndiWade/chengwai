$(function(){
	$("#guide-step .tipSwitch").live("click",function(){
		showSearchTip();
		setSearchTip();
		SetCookie("tStatus",1);
	});
})

function nextStep(next){
	$(".tipbox").css({"visibility":"hidden","display":"none"});
	$(".tipbar").hide();
	$("#step" + next).css({"visibility":"visible","display":"block"});
	$("#tipbar" + (next -1)).show();
	if(next == 2) {
		$("#searchTip").css("top","100px");
	}else if(next == 3) {
		$("#searchTip").css("top","260px");
	}else if(next == 4) {
		$("#searchTip").css("top","560px");
	}else if(next == 5) {
		$("#searchTip").css("top","430px");
	}else{
		$("#searchTip").css("top","300px");
		$("#searchTip").css("position","fixed");
	}
}

//关闭提示框
function hideTip(){
	$("#searchTipBg").hide();
	$("#searchTip").hide();
	$(".tipbar").hide();
	$(".tipbox").css({"visibility":"hidden","display":"none"});
	$("#step1").css({"visibility":"visible","display":"block"});
	SetCookie("tipVisible","no");
}

function setSearchTip(){
	var windowW = $(window).width(),
		windowH = $(window).height(),
		width = $("#searchTip").width(),
		ml = width/2;
	if($("#searchTip").length > 0 && $("#searchTipBg").length > 0){
		if($.browser.msie && $.browser.version == '6.0' && !$.support.style){
		  	var scrollT = $(window).scrollTop(),
			  	scrollL = $(window).scrollLeft();
			$("#searchTipBg").css({"width":windowW + scrollL,"height":windowH + scrollT});
		}else {
			$("#searchTipBg").css({"width":windowW,"height":windowH});
		}
		$("#searchTip").css({"margin-left":-ml});
	}
}
function noShow(){
	if(document.getElementById("notip").checked){
		SetCookie("neverShow","no",{expires:37230});
	}	
}

function showSearchTip(){
	var position = $.browser.msie && $.browser.version == '6.0' && !$.support.style ? "absolute" : "fixed";
	var searchTipBar = "<div class='tipbarwrap'><div class='tipbardiv'>";
	var searchTipBtext = "<div class='tipbarInner'><div class='arrow'></div><div class='tipBarword'></div></div>";
	var textnum = 5;
		for(i=1;i<=textnum;i++){
		  searchTipBar += "<div class='tipbar' id='tipbar"+i+"'>"+searchTipBtext+"</div>";
		}
		searchTipBar += "</div></div>";
		
	var searchTipInner='';
	var searchTipItext = "<div class='tipword'></div><span class='tipboxBtn' onclick='hideTip()'></span>";
		for(i=1;i<=textnum;i++){
		  var searchTipol = "<ol class='progress'>";
		  for(e=1;e<=(textnum+1);e++){
			  if(e==i) searchTipol+="<li class='on'></li>";
			  else searchTipol+="<li></li>";
		  }
		  searchTipol += "</ol>";
		  searchTipInner += "<div class='tipbox' id='step"+i+"'>"+searchTipItext+"<span class='tipboxNextbtn' onclick='nextStep("+(i+1)+")'></span>"+searchTipol+"</div>";
		}
		searchTipInner += "<div class='tipbox' id='step"+(textnum+1)+"'>"+searchTipItext+"<span class='tipboxNextbtn' onclick='hideTip();noShow()'></span><div class='notip'><input type='checkbox' id='notip' /><label for='notip'>不再提示</label></div></div>";
	if($("#searchTip").length == 0){
		$("#guide-step").before("<div id='searchTipBg' style='width:100%; height:100%; left:0px; top:0px; z-index:10000; background-color:#000; opacity:0.55; filter:alpha(opacity=55);position:"+ position +"'></div>");
		$("#guide-step").before("<div id='searchTip' style='left:50%; top:270px; z-index:10005; background-color:transparent; position:absolute;'>"+ searchTipInner +"</div>");
		$("#guide-step").before(searchTipBar);
		$("#step1").css({"visibility":"visible","display":"block"});
		if(GetCookie("tipVisible") == "no" || GetCookie("neverShow") == "no"){
			$("#step4 .notip").hide();
		}
	}
	if($("#searchTip").css("display") == "none"){
		$("#searchTip").css("top","270px").show();
		$("#searchTipBg").show();
		$(".tipbox").css({"visibility":"hidden","display":"none"});
		$("#step1").css({"visibility":"visible","display":"block"});
	}
	if($(".tipbarwrap").css("display") == "none"){
		$(".tipbarwrap").show();
	}
}

function GetCookie(name){
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) return decodeURIComponent(arr[2]); return null;
}

function SetCookie(name,value,options){
    var expires = '', path = '', domain = '', secure = ''; 
    if(options)
    {
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var exp;
            if (typeof options.expires == 'number') {
                exp = new Date();
                exp.setTime(exp.getTime() + options.expires*24*60*60*1000);
            }
            else{
                exp = options.expires;
            }
            expires = ';expires=' + exp.toUTCString();
        }
        path = options.path ? '; path=' + options.path : ''; 
        domain = options.domain ? ';domain=' + options.domain : ''; 
        secure = options.secure ? ';secure' : ''; 
    }
    document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
}