var FormValidation = function () {

    return {
        //main function to initiate the module
        init: function () {

            // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_sample_1');
            var error1 = $('.alert-error', form1);
            var success1 = $('.alert-success', form1);
			var merchant_type = $('.merchant_type');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
					name : {
                		required: true,
                		minlength: 1,
						maxlength:255
                	},
					info : {
                		required: true,
                		minlength: 1,
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
         			
					var status = false;
					merchant_type.each(function (){
						if ($(this).prop('checked') == true) status= true;
					});
					if (status == false) {
						alert('请选择至少选择一个商家类型'); 
						return false;
					}
					
                    form.submit();		//提交表单
                }
            });



        }
		
    };

}();