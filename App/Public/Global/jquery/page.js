//分页开始
var page_limit = 10;
var ipt_ojb;
//绑定对象
var init_obj = function () {
	obj_this = $('.click_this');
	obj_prev = $('.click_prev');
	obj_next = $('.click_next');
	obj_nowPages = $('.now_pages');
	obj_startSelect = $('.start_select');
	obj_index = $('.click_index');
}
//封装分页函数 总数 当前页 数量
function page_now_list(number,start,limit)
{
	//执行之间进行清空
	$('.demo_page').empty();
	var page_now_number = number;
	//开始当前页
	var page_now_start = start;
	//每页最多
	var page_now_limit = limit;
	if( page_now_number > page_now_limit )
	{
		//进一取整数
		var num_ceil = Math.ceil( page_now_number / page_now_limit );
		//当前的页数 now_num
		//最大页数 big_num
		//总共多少数据 all_num
		var number_all = 'now_num="'+page_now_start+'" big_num="'+num_ceil+'" all_num="'+number+'"';
		var str = '<a href="javascript:;" '+number_all+' class="click_index"> 首页 </a><a href="javascript:;" '+number_all+' class="click_prev"> < </a>';
		if( page_now_start<=3)
		{
			if(page_now_number <= 30)
			{
				for(var i=1 ;i<= num_ceil ; i++ )
					{
						if(i == page_now_start)
						{
							str += '<a href="javascript:;" '+number_all+' class="click_this on"> '+i+' </a>';
						}else{
							str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+i+' </a>';
						}
					}
			}else{
				for(var i=1 ;i<= 4 ; i++ )
				{
					if(i == page_now_start)
					{
						str += '<a href="javascript:;" '+number_all+' class="click_this on"> '+i+' </a>';
					}else{
						str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+i+' </a>';
					}
				}
				if(num_ceil!=4)
				{
					if(num_ceil-4==2)
					{
						str += '<em> ... </em><a href="javascript:;" '+number_all+' class="click_this num">'+num_ceil+'</a>';
					}else if(num_ceil-4==1)
					{
						str += '<a href="javascript:;" '+number_all+' class="click_this num">'+num_ceil+'</a>';
					}else{
						str += '<em> ... </em><a href="javascript:;" '+number_all+' class="click_this num">'+num_ceil+'</a>';
					}
				}
			}
			//此处结束
		}else{
			str += '<a href="javascript:;" '+number_all+' class="click_this num"> 1 </a>';
			if(page_now_start-2-1!=1)
			{
				str +='<em> ... </em>';
			}
			for(var j=2;j>=1;j--)
			{
				str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+(page_now_start-j)+' </a>';
			}
			str += '<a href="javascript:;" '+number_all+' class="click_this on"> '+page_now_start+' </a>';
			var number_switch = num_ceil - page_now_start;
			switch(number_switch)
			{
				case 3:
					for(var m=1;m<=3;m++)
					{
						str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+(page_now_start+m)+' </a>';
					}
				break;
				case 2:
					for(var m=1;m<=2;m++)
					{
						str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+(page_now_start+m)+' </a>';
					}
				break;
				case 1:
					str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+(page_now_start+1)+' </a>';
				break;
				case 0:
				break;
				default:
					for(var m=1;m<=2;m++)
					{
						str += '<a href="javascript:;" '+number_all+' class="click_this num"> '+(page_now_start+m)+' </a>';
					}
					str += '<em> ... </em><a href="javascript:;" '+number_all+' class="click_this num">'+num_ceil+'</a>';
				break;
			}
		}
		str += '<a href="javascript:;" '+number_all+' class="click_next num"> > </a>';
		str += '<b class="total fl"> 共'+num_ceil+'页 </b>'
		str += '<div class="count fl"><i class="fl">到第</i><input type="text" name="tiaozhuan" class="text fl now_pages"/><i class="fl">页</i>';
		str += '<span '+number_all+' class="aok fl start_select">前往</span>';
		str += '</div>';
		$('.demo_page').append(str);
	}
	init_obj();
	live_this();
	live_prev();
	live_next();
	live_select();
	live_index();
}



//查找页
function live_select()
{
	obj_startSelect.click(function(){
		var this_number = parseInt(obj_nowPages.val());
		var _this = $(this);
		var big_num = _this.attr('big_num');
		var all_num = _this.attr('all_num');
		if(this_number>big_num)
		{
			this_number = big_num;
		}
		if(isNaN(this_number))
		{
			this_number = 1;
		}
		//该函数需要替换 AJAX
		page_now_list(all_num,this_number,page_limit);
		obj_nowPages.val(this_number);
	});
}

//获得上一页
function live_prev()
{
	obj_prev.click(function(){
		var _this = $(this);
		var this_number = parseInt(_this.attr('now_num')) - 1;
		var all_num = _this.attr('all_num');
		if(this_number <= 0 )
		{
			this_number = 1;
		}
		//该函数需要替换 AJAX
		page_now_list(all_num,this_number,page_limit);
	});
}

//获得下一页
function live_next()
{
	obj_next.click(function(){
		var _this = $(this);
		var this_number = _this.attr('now_num');
		var big_number = _this.attr('big_num');
		var all_num = _this.attr('all_num');
		var now_page = parseInt(this_number) + 1;
		if(now_page > big_number)
		{
			var now_page = big_number;
		}
		//该函数需要替换 AJAX
		page_now_list(all_num,now_page,page_limit);
	});
}

//获得当前页
function live_this(){
	obj_this.click(function(){
		var _this = $(this);
		var this_number = parseInt(_this.html());
		var all_num = _this.attr('all_num');
		//该函数需要替换 AJAX
		page_now_list(all_num,this_number,page_limit);
	});
}

//首页
function live_index()
{
	obj_index.click(function(){
		var _this = $(this);
		var all_num = parseInt(_this.attr('all_num'));
		//该函数需要替换 AJAX
		page_now_list(all_num,1,page_limit);
	});
}

//返回函数
function get_page_tags()
{
	var returnArray = [];
	var allnumber = obj_startSelect.attr('all_num');
	returnArray['allnumber'] = allnumber;
	var nowpage = obj_startSelect.attr('now_num');
	returnArray['nowpage'] = nowpage;
	return returnArray;
}

//第一次必须传入 总数 和 当前页数
var first_all = 18;
var first_page = 1;
//第一次刷新页面 需要传递数值
$(function(){
	page_now_list(first_all,first_page,page_limit);
});



