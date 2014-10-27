define(function (require) {
    var blankTpl = require('./tpl/add_blank.tpl#');
    var ko = require('knockout');
    
    require('knockout_validation');
    
    ko.validation.rules['minCharLength'] = {
        validator: function (val, minLength) {
            return ko.validation.utils.isEmptyVal(val) || W.util.getCharCount(val) >= minLength;
        },
        message: 'Sorry Chief, {0} this is not Valid'
    };
    ko.validation.rules['maxCharLength'] = {
        validator: function (val, maxLength) {
            return ko.validation.utils.isEmptyVal(val) || W.util.getCharCount(val) <= maxLength;
        },
        message: 'Sorry Chief, {0} this is not Valid'
    };
    ko.validation.registerExtenders();
    
    //三态提示框，用以处理提示语
    ko.bindingHandlers.validThreeState = {
        init: function (element, valueAccessor) {
            var value = valueAccessor(),
                $element = $(element);

            //展示默认值
            value.notice_css = ko.observable('prompt');
            value.notice_text = ko.observable(value.init_prompt);

            //获取焦点时候的设置
            $element.focus(function () {
                value.notice_css('prompt');
                value.notice_text(value.focus_prompt);
            });

            //丢失焦点时候的设置
            $element.blur(function () {
                if (value.error()) {
                    value.notice_css('error');
                    value.notice_text(value.error());
                } else {
                    value.notice_css('correct');
                    value.notice_text('');
                }
            });
        }
    };

    //用以处理notice事件
    ko.bindingHandlers.noticeImg = {
        init: function (element, valueAccessor) {
            var value = valueAccessor(),
                $element = $(element);

            //获取焦点时候的设置
            $element.focus(function () {
                self.notice_img(value());
            });
        }
    };
    
    var winOption = {
        title: '添加支付账号',
        tbar: ['close']
    };
    
    function getOptionName(id, options, defaultName){
        var name = defaultName || '数据不足';
        $.each(options, function(i, e){
            if(e.id == id){
                name = e.name;
            }
        });
        return name;
    }

    function showAddBlankWin(data) {
        var win = new W.Window({
            title: winOption.title,
            height: "auto",
            width: 921,
            tbar: winOption.tbar
        });
        
        win.setHtml(blankTpl + $('#add_dialog_manager_contact_template').html());

        win.show();
        
        var validation = {};
        
        // 真实姓名
        validation.truename = ko.observable((data && data.truename) || '');
        validation.truename = validation.truename.extend(function () {
            return {
                required: {message: '真实姓名不能为空！'},
                pattern: {
                    message: '请输入正确的真实姓名！',
                    params: '^[\u4e00-\u9fa5]{2,6}$'
                }
            };
        }());
        validation.truename.init_prompt = "请输入真实姓名！";
        validation.truename.focus_prompt = "请输入真实姓名！";
        
        // 帐号类型
        validation.cardtype = ko.observable((data && data.cardtype) || '');
        validation.cardtype_options = ko.observableArray([
            {id: 0, name: '支付宝'},
            {id: 1, name: '银行卡'}
        ]);
        validation.cardtype_name = function(){
            return getOptionName(validation.cardtype(), validation.cardtype_options());
        }();
        validation.cardtype = validation.cardtype.extend(function () {
            return {
                required: {message: '请选择帐号类型'}
            };
        }());
        
        // 帐号
        validation.account = ko.observable((data && data.account) || '');
        
        //类型转换判断
        ko.computed(function(){
            var cardtype = parseInt(validation.cardtype()) || 0;
            validation.account = validation.account.extend(function () {
                return {
                    required: {message: '帐号不能为空！'},
                    pattern: {
                        message: '请输入正确的帐号！',
                        // params: /^[\w\-\.]+@[[\w\-\.]+(\.\w+)+$/
                        params: /^(([\w\-\.]+@[[\w\-\.]+(\.\w+)+)|([a-zA-Z0-9]{16}))$/
                    }
                };
            }());
        });
        validation.account.init_prompt = "请输入正确的帐号！";
        validation.account.focus_prompt = "请输入正确的帐号！";
        
        
        validation.errors = ko.validation.group(validation);
        validation.weibo_alert_notice = ko.observable();
        
        validation.submit = function () {
            if(validation.errors().length > 0){
               W.alert(validation.errors()[0]);
                return;
            }
            validation.weibo_alert_notice("<div class='fl regi_loading'><img src='/App/Public/Media/images/myimg/onload.gif'>数据提交中，请耐心等待！</div>");
            
            var submitValues = {'id': (data && data.id) || ''};
            var formElement = win.el.find("#addBlank").serializeArray();
            $.map(formElement, function (e) {
                    if (e.name && e.value) {
                        submitValues[e.name] = e.value;
                    }
                }
            );
            var defer = $.restPost('/Media/Account/addBlank', submitValues);
            
            defer.done(function () {
                var msg = $('#account_create_success_template').html();
                W.baseMessageBox(msg, "success", {
                    width: 500,
                    title: "添加成功",
                    bbar: [
                        {
                            text: "确定",
                            handler: function () {
                                this.close();
                                location.reload();
                            }
                        }
                    ]
                });
                win.close();
                validation.weibo_alert_notice('');
            });

            defer.fail(function (error) {
                validation.weibo_alert_notice('');
                console.log(error);
                W.alert(error);
            });
        };
        
        
        validation.close = function () {
            win.close();
        };

        ko.applyBindings(validation, win.el[0]);
        

        //调整高宽度
        var body = win.el.find(".weiboyiWindow_body"),
            content = win.el.find(".cBox");
        if (content.height() > 500) {
            body.css({
                "height": "500px",
                "overflow-x": "hidden",
                position: "relative"
            });
        }
        win.setWidth(content.width() + 50);
    }

    return {
        showAddBlankWin: showAddBlankWin
    };
});