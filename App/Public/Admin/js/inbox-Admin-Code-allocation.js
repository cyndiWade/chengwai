var Inbox = function () {

	var select_all_ipt = $('#select_all_ipt');		//全选
	var code_ipt = $('.code_ipt');			//所有ipt
	var menu = $('.dropdown-menu li');				//操作
	
	
	//全选
	select_all_ipt.click(function () {
		var _this = $(this);

		if (_this.prop("checked") == true) {
			code_ipt.prop("checked", true);
		} else if (_this.prop("checked") == false)  {
			code_ipt.prop("checked", false);
		}
	});
	
	
	menu.click(function () {
		var _index = $(this).index();
		
		switch (_index) {
			case 0:		//分配二维码给酒店
				var code_ipt_check = false;
				var code_check_ids = [];	//已选中的I
				code_ipt.each(function () {
					var _this = $(this);
					if (_this.prop('checked')) {
						code_ipt_check = true;
						code_check_ids.push(_this.val());
					}
				});
				
				if (code_ipt_check == false) {
					alert('请选择要分配的二维码');
					return false;
				} else {
					var url = '?s=/Admin/Hotel/show_hotel_list/&code_ids=' + code_check_ids;
					$.Dialog(url,{
						type:"iframe",
						width: 1200,
						height:600,
						title: "分配给酒店"
					});	
					
					var closeDialog = $('.closeDialog');		//关闭
					//关闭刷新页面
					closeDialog.click(function (){
						if (confirm('关闭后刷新页面！') == true) {
							window.location.reload();
						}
					}) 
				}
				break;
		}

	});
	
	
	
	

    return {
        //main function to initiate the module
        init: function () {
	/*
            // handle compose btn click
            $('.inbox .compose-btn a').live('click', function () {
                loadCompose();
            });

            // handle reply and forward button click
            $('.inbox .reply-btn').live('click', function () {
                loadReply();
            });

            // handle view message
            $('.inbox-content .view-message').live('click', function () {
                loadMessage();
            });

            // handle inbox listing
            $('.inbox-nav > li.inbox > a').click(function () {
                loadInbox('inbox');
            });

            // handle sent listing
            $('.inbox-nav > li.sent > a').click(function () {
                loadInbox('sent');
            });

            // handle draft listing
            $('.inbox-nav > li.draft > a').click(function () {
                loadInbox('draft');
            });

            // handle trash listing
            $('.inbox-nav > li.trash > a').click(function () {
                loadInbox('trash');
            });

            //handle compose/reply cc input toggle
            $('.inbox-compose .mail-to .inbox-cc').live('click', function () {
                handleCCInput();
            });

            //handle compose/reply bcc input toggle
            $('.inbox-compose .mail-to .inbox-bcc').live('click', function () {
                handleBCCInput();
            });
	*/

            //handle loading content based on URL parameter
//            if (App.getURLParameter("a") === "view") {
//                loadMessage();
//            } else if (App.getURLParameter("a") === "compose") {
//                loadCompose();
//            } else {
//               // loadInbox('inbox');
//            }

        }

    };

}();