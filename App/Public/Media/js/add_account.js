define(function (require) {
    var sinaTpl = require('./tpl/add_sina.tpl'),
        tencentTpl = require('./tpl/add_tengxun.tpl'),
        weixinTpl = require('./tpl/add_weixin.tpl'),
        xinwenTpl = require('./tpl/add_xinwen.tpl'),
        // weitaoTpl = require('./tpl/add_weitao.tpl'),
        // pengyouquanTpl = require('./tpl/add_pengyouquan.tpl'),
        // QZoneUncertificatedTpl = require('./tpl/add_qzone_uncertificated.tpl'),
        // QzoneTpl = require('./tpl/add_qzone.tpl'),
        // sohuTpl = require('./tpl/add_sohu.tpl'),
        othersTpl = require('./tpl/add_other.tpl');

    var ko = require('knockout'),
        AccountModel = require('./account_model.js'),
        ValidationModel = require('./account_validation_model.js');

    require('knockout_validation');
    require('knockout_placeholder');
    require('knockout_plupload');

    require('./weixin_info_desc.js');

    var selectPlatformTpl = require('./tpl/add_account_select_platform.tpl');

    var platformDatas, selectWin;

    var winOption = {
        title: '选择账号类型',
        tbar: ['close']
    };
    /**
     * 选择平台
     */
    function showAccountWin(cfg) {
        $.extend(winOption, cfg);

        if(selectWin){
            selectWin.show();
        }else{
            var win = new W.Window({
                title: winOption.title,
                width: 580,
                // height: 430,
                modal: true,
                tbar: winOption.tbar
            });
            var rest = $.restGet('/Media/SocialAccount/chooseweibotype');

            rest.done(function (msg, data) {
                win.setHtml(doT.template(selectPlatformTpl)(data));
                platformDatas = data;
            });

            rest.fail(function (msg) {
                W.alert(msg || '获取账号类型失败', 'error');
            });
            
            win.setLoader(rest);
            win.show();

            win.el.on('click', 'a', function () {
                var $a = $(this),
                    id = $a.attr('data-pid'),
                    name = $a.attr('data-name');

                var selectedPlatform;
                $.each(platformDatas, function(index, platform){
                    if(platform.pid == id){
                        selectedPlatform = platform;
                    }
                });

                showAddAccountWin(selectedPlatform);
                win.close();
            });

            selectWin = win;
        }
    }

    function showAddAccountWin(platformData) {

        var win = new W.Window({
            title: W.util.formatStr('添加 {0} 账号', platformData.platformName),
            height: "auto",
            width: 921,
            tbar: winOption.tbar
        });

        var tpl = {
            // '1': sinaTpl,
            // '2': tencentTpl,
            // '3': sohuTpl,
            // '5': QzoneTpl,
            // '9': weixinTpl,
            // '17': weitaoTpl,
            // '23': pengyouquanTpl,
            // '19': QZoneUncertificatedTpl
            '1': sinaTpl,
            '2': tencentTpl,
            '3': weixinTpl,
            '4': xinwenTpl,

        } [platformData.pid] || othersTpl;

        win.setHtml(tpl + $('#add_dialog_manager_contact_template').html());

        win.show();
        
        var validation = ValidationModel(new AccountModel({'weibo_type': platformData.pid}));

        validation.single_graphic_price = validation.single_graphic_price.extend(function () {
            var weibo_type = validation.weibo_type();
            //微信 old pid = 9
            if (weibo_type == 3) {
                return {
                    required: {message: '单图文价格不能为空！'},
                    min: {params: 0, message: '价格必须大于等于1'}
                }
            }
            return {};
        }());

        validation.single_graphic_price.init_prompt = "请输入正确的价格！";
        validation.single_graphic_price.focus_prompt = "请输入正确的价格！";

        validation.platform_account_name_img = ko.observable(platformData.platformAccountNameImg);
        validation.platform_id_img = ko.observable(platformData.platformIdImg);
        validation.platform_url_img = ko.observable(platformData.platformUrlImg);
        validation.notice_img = ko.observable();

        validation.errors = ko.validation.group(validation);
        validation.weibo_alert_notice = ko.observable();

        if (platformData.pid == 23) {
            //朋友圈用于职业类型的错误提示信息
            validation.profession_error_display = ko.computed(function () {
                    return validation.profession_type.error() || validation.profession_other_info.error();
                }
            );
        }
        if (platformData.pid == 23) {
            validation.edu_error_display = ko.computed(function () {
                    return validation.edu_degree.error();
                }
            );
        }
        //微信多图文第一条和多图文第二条会随着变动 old pid = 9
        if(platformData.pid == 3){
            validation.single_graphic_price.subscribe(
                function (singleGraphicPrice) {
                    validation.multi_graphic_top_price(singleGraphicPrice * 0.7);
                    validation.multi_graphic_second_price(singleGraphicPrice * 0.4);
                    validation.multi_graphic_other_price(singleGraphicPrice * 0.25);
                    validation.content_price(singleGraphicPrice * 0.25);
                }
            );
        }

        validation.submit = function () {
            console.log('fuck fuck fuck fuck fuck fuck fuck fuck fuck ');
            if(validation.errors().length > 0){
               W.alert(validation.errors()[0]);
                return;
            }
            validation.weibo_alert_notice("<div class='fl regi_loading'><img src='/App/Public/Media/images/myimg/onload.gif'>数据提交中，请耐心等待！</div>");
            var submitValues = {'weibo_type': validation.weibo_type()};

            var formElement = win.el.find("#accountRegistor").serializeArray();
            $.map(formElement, function (e) {
                    if (e.name && e.value) {
                        submitValues[e.name] = e.value;
                    }
                }
            );

            // var defer = $.restPost('/information/account/create', submitValues);
            var defer = $.restPost('/Media/SocialAccount/create', submitValues);
            
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
                            }
                        }
                    ]
                });
                win.close();
                validation.weibo_alert_notice('');
                $('#addcount').removeClass('btn_small_disabled').addClass('btn_small_strong');
            });

            defer.fail(function (error) {
                validation.weibo_alert_notice('');
                console.log(error);
                W.alert(error);
            });
        };

        validation.reset = function () {
            win.close();
            showAccountWin();
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
        showAccountWin: showAccountWin
    };
});