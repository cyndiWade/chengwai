$(function(){
	//分页开始
	var ipt_ojb;
	//绑定对象
	var init_obj = function () {
		obj_this = $('.click_this');
		obj_prev = $('.click_prev');
		obj_next = $('.click_next');
		obj_nowPages = $('.now_pages');
		obj_startSelect = $('.start_select');
		obj_demoPage = $('.demo_page');
		obj_index = $('.click_index');
	}
	page_now_list(all,now_page,limit);
	//封装分页函数 总数 当前页 数量
	function page_now_list(number,start,limit)
	{
		var select_Array = [];
		//开始
		select_Array['start'] = start;
		//每页最多
		select_Array['limit'] = limit;
		if(number > limit)
		{
			//进一取整数
			var num = Math.ceil(number / select_Array['limit']);
			var str = '<a href="javascript:;" class="click_index"> 首页 </a><a href="javascript:;" now_num="'+select_Array['start']+'" class="click_prev"> < </a>';
			if( select_Array['start']<=5 )
			{
				var number = select_Array['start'] + 2 ;
				for(var i=1 ;i<=number;i++ )
				{
					if(i == select_Array['start'])
					{
						str += '<a href="javascript:;" style="color:red;" class="click_this num"> '+i+' </a>';
					}else{
						str += '<a href="javascript:;"  class="click_this num"> '+i+' </a>';
					}
				}
				str += '<em> ... </em><a href="javascript:;" class="click_this num">'+num+'</a>';
			}else{
				str += '<a href="javascript:;" class="click_this num"> 1 </a><em> ... </em>';
				for(var j=2;j>=1;j--)
				{
					str += '<a href="javascript:;" class="click_this num"> '+(select_Array['start']-j)+' </a>';
				}
				str += '<a href="javascript:;" style="color:red;" class="click_this num"> '+select_Array['start']+' </a>';
				var number = num - select_Array['start'];
				switch(number)
				{
					case 3:
						for(var m=1;m<=3;m++)
						{
							str += '<a href="javascript:;" class="click_this num"> '+(select_Array['start']+m)+' </a>';
						}
					break;
					case 2:
						for(var m=1;m<=2;m++)
						{
							str += '<a href="javascript:;" class="click_this num"> '+(select_Array['start']+m)+' </a>';
						}
					break;
					case 1:
						str += '<a href="javascript:;" class="click_this num"> '+(select_Array['start']+1)+' </a>';
					break;
					case 0:
					break;
					default:
						for(var m=1;m<=2;m++)
						{
							str += '<a href="javascript:;" class="click_this num"> '+(select_Array['start']+m)+' </a>';
						}
						str += '<em> ... </em><a href="javascript:;" class="click_this num">'+num+'</a>';
					break;
				}
			}
			str += '<a href="javascript:;" now_num="'+select_Array['start']+'" big_num="'+num+'" class="click_next num"> > </a>';
			str += '<b class="total fl"> 共'+num+'页 </b>'
			str += '<div class="count fl"><i class="fl">到第</i><input type="text" name="tiaozhuan" class="text fl now_pages"/><i class="fl">页</i>';
			str += '<span big_num="'+num+'" class="aok fl start_select">前往</span>';
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
			var num = parseInt(obj_nowPages.val());
			var _this = $(this);
			var big_num = _this.attr('big_num');
			if(num>big_num)
			{
				num = big_num;
			}
			if(isNaN(num))
			{
				num = 1;
			}
			obj_demoPage.empty();
			page_now_list(all,num,limit);
			obj_nowPages.val(num);
		});
	}

	//获得上一页
	function live_prev()
	{
		obj_prev.click(function(){
			var _this = $(this);
			var num = _this.attr('now_num') - 1;
			obj_demoPage.empty();
			if(num<=0)
			{
				num = 1;
			}
			page_now_list(all,num,limit);
		});
	}

	//获得下一页
	function live_next()
	{
		obj_next.click(function(){
			var _this = $(this);
			var now_num = _this.attr('now_num');
			var big_num = _this.attr('big_num');
			var now_page = parseInt(now_num) + 1;
			obj_demoPage.empty();
			if(now_page > big_num)
			{
				var now_page = big_num;
			}
			page_now_list(all,now_page,limit);
		});
	}

	//获得当前页
	function live_this(){
		obj_this.click(function(){
			var _this = $(this);
			var value = parseInt(_this.html());
			obj_demoPage.empty();
			page_now_list(all,value,limit);
		});
	}

	//首页
	function live_index()
	{
		obj_index.click(function(){
			obj_demoPage.empty();
			page_now_list(all,1,limit);
		});
	}
});