var Calendar = function () {

    return {
        //main function to initiate the module
        init: function () {
            Calendar.initCalendar();
        },

        initCalendar: function () {

            if (!jQuery().fullCalendar) {
                return;
            }

			var calendar = $('#calendar');
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var h = {};
			
			/* 导航样式容错 */
            if (App.isRTL()) {
                 if (calendar.parents(".portlet").width() <= 720) {
                    calendar.addClass("mobile");
                    h = {
                        right: 'title, prev, next',
                        center: '',
                        right: 'prev,next,today,agendaDay,agendaWeek,month'
                    };
                } else {
                    calendar.removeClass("mobile");
                    h = {
                        right: 'title',
                        center: '',
                        left: 'prev,next,today,agendaDay,agendaWeek,month'
                    };
                }                
            } else {
                 if (calendar.parents(".portlet").width() <= 720) {
                    calendar.addClass("mobile");
                    h = {
                        left: 'title, prev, next',
                        center: '',
                        right: 'prev,next,today,agendaDay,agendaWeek,month'
                    };
                } else {
                    calendar.removeClass("mobile");
                    h = {
                        left: 'title',
                        center: '',
                        right: 'prev,next,today,agendaDay,agendaWeek,month'
                    };
                }
            }


			//日历控件
			var wade_datetimepicker = function () {
				var options = {
				    format: 'yyyy-mm-dd',
					language:  'zh-CN',
			        weekStart: 1,
			        todayBtn:  1,
					autoclose: 1,
					todayHighlight: 1,
					startView: 2,
					forceParse: 0,
			        showMeridian:1,
					 todayBtn:1,
					forceParse: 0,
					minView: 2,
				};
				$('.wade_datetimepicker').datetimepicker(options);
			}();


			/* wade -- 自定义新日程到日历中 */
			var add_schedule = function (title) {
				
					/* 数据采集与验证 */
					var arr_input = $('.schedule_container').find('input');
					var check_result = true;
					var post_data = {};
					
					arr_input.each(function () {		//收集提交的值
						var _this = $(this);
						if (_this.val() == '') {
							alert('不得为空！');
							_this.focus();
							check_result = false;
							return false;		//退出循环
						} else {
							post_data[_this.attr('name')] = _this.val();			//放入数组中
						}
					});	
					
					if (check_result == false)  return false;
				//	console.log(post_data);
	
					//日期验证
					if (fomat_date(post_data.start_time) > fomat_date(post_data.over_time) ) {
						alert('开始日期不得大于结束日期');
						check_result = false;
					}
					//现付价格验证
					if (post_data.spot_payment.match(/^[0-9][0-9]*/gim) == undefined) {
						alert('价格只能是数字');
						check_result = false;
					}
					//到付
					if (post_data.prepay.match(/^[0-9][0-9]*$/gim) == undefined)  {
						alert('价格只能是数字');
						check_result = false;
					}
					if (post_data.room_num.match(/^[1-9][0-9]*$/gim) == undefined)  {
						alert('房间数量只能是数字');
						check_result = false;
					}
					
					if (check_result == false)  return false;
	
					/* 添加数据 */				
					var result = ajax_post_setup('?s=/Admin/RoomSchedule/Ajax_room_schedule_edit',post_data);
					if (result == false) {
						alert('服务器连接超时');
						return false;
					} else if (result.status == 0) {
						alert(result.msg);
					} else {
						alert(result.msg);
						return false;
					}

					//console.log(result);
					/* 日程写入到日历中
                    var copiedEventObject =  {};
					copiedEventObject.id = result.data.id;
					copiedEventObject.title = post_data.spot_payment;
					copiedEventObject.start = post_data.start_time;
					copiedEventObject.end = post_data.over_time;
					copiedEventObject.backgroundColor = App.getLayoutColorCode('red');
                    copiedEventObject.allDay = true;
                    //添加到日历中
     				 */
			};
			
			
			/* 点击添加新的日程 */
            $('#event_add').unbind('click').click(function () {
			
				add_schedule();
            });
	
	
           // calendar.fullCalendar('destroy'); // destroy the calendar
			var calendar_init = { //re-initialize the calendar
           	//	 contentHeight: 1024,	
        	    monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
	       	 	monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
	         	dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],  
	         	dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],  
	        	today: ["今天"],  
	       		buttonText: {  
	        		today: '今天',  
	     			month: '月',  
	  			    week: '周',  
	   				day: '日'
				},
				defaultView:'month',		//默认显示的视图
				titleFormat:{
				    month: 'yyyy-MM',                            
				    week: "{yyyy}-MM", // Sep 7 - 13 2009
				    day: 'yyyy-MM-dd dddd'               
				},
				header: h,
				firstDay:1,	//Sunday=0, Monday=1, Tuesday=2, etc. 设置每个星期第一天是
				axisFormat:'HH(:mm)',	//week的左边事件		axisFormat:'h(:mm)tt',
				timeFormat :'HH:mm{ - HH:mm}',		//日程时间格式	timeFormat :''h:mm{ - h:mm}',
                slotMinutes: 5,
				
				/* 加载以后日程数据 */
				events : function (start, end, callback) {
					var hotel_room_id = $('input[name=hotel_room_id]').val();
					var result = ajax_post_setup('?s=/Admin/RoomSchedule/AJAX_Get_Schedule',{'hotel_room_id':hotel_room_id});
					var event_data = [];			
					if (result.status == 0) {
						//现付价格
						for (var obj in result.data) {

							var a = result.data[obj].is_cut_off == 1 ? App.getLayoutColorCode('red') : App.getLayoutColorCode('green')
							//var a = App.getLayoutColorCode('green');
							var b = result.data[obj].is_cut_off == 1 ? App.getLayoutColorCode('red') : App.getLayoutColorCode('red');
							var c = result.data[obj].is_cut_off == 1 ? App.getLayoutColorCode('red') : App.getLayoutColorCode('blue');
							event_data.push({
								id : result.data[obj].id,
			                	title :'数量:' + result.data[obj].room_num,
			                	start: result.data[obj].day,
								end : result.data[obj].day,		
								className:'hand',
								backgroundColor:a,
								allDay: true
			                });
							event_data.push({
								id : result.data[obj].id,
			                	title :'预付:' + result.data[obj].spot_payment,
			                	start: result.data[obj].day,
								end : result.data[obj].day,		
								className:'hand',
								backgroundColor:b,
								allDay: true
			                });
							event_data.push({
								id : result.data[obj].id,
			                	title :'现付:' + result.data[obj].prepay,
			                	start: result.data[obj].day,
								end : result.data[obj].day,
								className:'hand',
								backgroundColor:c,
								allDay: true
			                });
						}					
						//日程写入到日历中
	                	callback(event_data);	
					}	 
				},


				/* 删除日程 */
				eventClick:function( event, jsEvent, view ) { 
				
					if (confirm('确定要删除吗？') == true) {
						var id = event.id;
						var result = ajax_post_setup('?s=/Admin/RoomSchedule/AJAX_DEL_Schedule',{'id':id});
						if (result == false) {
							alert('服务器连接超时');
							return false;
						} else if (result.status == 0) {
							alert(result.msg);
							calendar.fullCalendar('removeEvents', [id]);
						} else {
							alert(result.msg);
							return false;
						}
					};		
				},	
            };
            
			calendar.fullCalendar(calendar_init);

        }

    };

}();
