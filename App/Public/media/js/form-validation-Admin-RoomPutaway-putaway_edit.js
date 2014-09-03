var FormValidation = function () {

    return {
        //main function to initiate the module
        init: function () {

            // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_sample_1');
            var error1 = $('.alert-error', form1);
            var success1 = $('.alert-success', form1);

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                	start_time : {
                		required: true,
                		dateISO:true
                	},
					over_time : {
                		required: true,
                		dateISO:true
                	},
					remark : {
						maxlength:255
                	}
	

                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    success1.hide();
                    error1.show();
                    App.scrollTo(error1, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change dony by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid').addClass('help-inline ok') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                    
                },

                submitHandler: function (form) {
                    success1.show();
                    error1.hide();
          //          var result = ajax_post_setup('?s=/Admin/CarsSchedule/AJAX_Get_Schedule',{'cars_id':cars_id});
			//		var event_data = [];
				//	if (result.status == 0) {
                    form.submit();		//提交表单
                }
            });

     
			
			
			//搜索用户
			(function ($) {
				var btn_search = $('#btn_search');					//搜索账号
				var select_member  = $('#select_member');		//用户选择框
				var ipt_content = $('#ipt_content');					//搜索框内容
				
				//查找用户数据
				btn_search.click(function () {
					var val = ipt_content.val();
					if (val == '') {
						alert('查询内容不得为空！');
						return false;
					}
					
					$.post('?s=/Admin/Rank/ajax_search_account',{
						account:val	
					},function(result){
						select_member.empty();	//清空
						select_member.append('<option value="">--请选择用户--</option>');
						if (result.status == 0) {	
							alert('找到'+result.data.length+'条相似数据');
							for(var key in result.data){
								select_member.append("<option value="+result.data[key].id+">"+result.data[key].account+"--"+result.data[key].nickname+"</option>");
							}
						} else {
							alert('没有查找到指定数据！');
						}						
					},'json');	
					
				});
			})(jQuery);

        }
		
    };

}();