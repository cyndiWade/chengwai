define(function(require, exports){

    var is_extend_tpl = require('./tpl/is_extend.tpl');

    function initIsHardAdTips(grid) {
        new W.Tips({
            group: 'edit',
            target: ".js_isHardDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "是否接硬广",
            html: is_extend_tpl,

            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var isExtendRadio = $('input[name=isExtendRadio]:checked').val();
                        var selectAllCheckbox = $('input[name=selectAllExtendCheckbox]').attr('checked');
                        var accountId = $(".js_extendAccountId").val();
                        var self = this;
                        if (selectAllCheckbox) {
                            W.confirm("确认对全部账号进行是否接硬广的设置吗？", function (sure) {
                                if (!sure) {
                                    return false;
                                } else {
                                    var message = W.message("处理中", "loading", 1000);
                                    $.ajax({
                                        type: "POST",
                                        url: "/information/accountmanage/setextend",
                                        data: "accountId=" + accountId + "&isExtendRadio=" + isExtendRadio + "&selectAllCheckbox=" + selectAllCheckbox,
                                        dataType: "json",
                                        success: function (msg) {
                                            message.close();

                                            if (msg['status'] == false) {
                                                W.alert(msg['message']);
                                            } else {
                                                W.alert("操作成功！", "success");
                                                self.close();
                                                grid.reload();
                                            }
                                        },
                                        error: function () {
                                            W.alert("服务器有误，请联系管理员！");
                                            return false;
                                        }
                                    })
                                }
                            });
                        } else {
                            var message = W.message("处理中", "loading", 1000);
                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/setextend",
                                data: "accountId=" + accountId + "&isExtendRadio=" + isExtendRadio + "&selectAllCheckbox=" + selectAllCheckbox,
                                success: function (msg) {
                                    message.close();
                                    if (msg['status'] == false) {
                                        W.alert(msg['message']);
                                    } else {
                                        W.alert("操作成功！", "success");
                                        self.close();
                                        grid.reload();
                                    }
                                },
                                error: function () {
                                    W.alert("服务器有误，请联系管理员！");
                                    return false;
                                }
                            })
                        }

                    }

                },
                {
                    text: "取消",
                    type: "normal",
                    handler: function () {
                        this.close();
                    }
                }
            ],
            listeners: {
                ontargetchanged: function () {
                    var data_radio = this.getCurrentTarget().attr("data_radio");
                    var data_account_id = this.getCurrentTarget().attr("data_account_id");
                    $('.js_extendAccountId').val(data_account_id);
                    if (data_radio == false || data_radio == 2) {
                        $(".js_extendRadioFalse").prop('checked', true);
                        $(".js_extendRadioFalse").parent().next(".extendNotice").show();
                    } else {
                        $(".js_extendRadioTrue").prop('checked', true);
                    }
                    $("input[name='selectAllExtendCheckbox']").attr("checked", false);

                    $("input[name='isExtendRadio']").change(function(){
                        if($(this).val() == '0'){
                            $(this).parent().nextAll('.extendNotice').show();
                        }else{
                            $(this).parent().nextAll('.extendNotice').hide();
                        }
                    })

                }
            }
        });
    }
    return initIsHardAdTips;
});