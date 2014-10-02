define(function(require) {

    // var feedback = require('./feedback.js');
    var ko = require('knockout'),
        AccountModel = require('./account_model.js'),
        ValidationModel = require('./account_validation_model.js'),
        detailAccountInfoTabTpl = require('./tpl/detail_account_info_tab.tpl');
        // pengyouquanDetailAccountInfoTabTpl = require('./tpl/pengyouquan_detail_account_info_tab.tpl'),
        // pengyouquanDetailPlatformInfoTabTpl = require('./tpl/pengyouquan_detail_platform_info_tab.tpl'),
        // detailFansTrendsTab = require('./detail_fans_trends_tab.js');

    //动态设置需要展示的tab
    var displayableTabs;

    // var WEIBO_TYPE_WEIXIN = 9,
    var WEIBO_TYPE_WEIXIN = 3,
        WEIBO_TYPE_WEITAO = 17,
        WEIBO_TYPE_PENGYOUQUAN = 23;
    var admin_qq;
    /**
     * 创建查看详情弹窗
     * @param row
     * @returns {*}
     */
    function createDetailWin(row) {
        admin_qq = row.cells.admin_qq;
        var items = [];
        if(displayableTabs.pengyouquanAccount){
            items.push(            {
                tabTitle: "账号数据",
                icon: "freedataIcon1",
                loader: {
                    url: '/information/accountmanage/detail',
                    data: {
                        account_id: row.cells.account_id
                    },
                    success: function (data) {
                        var self=this;
                        this.setHtml(pengyouquanDetailAccountInfoTabTpl);
                        var accountInfo = data.data;
                        accountInfo.weixin_image_option = {
                            viewBasePath : '/img/uploadimg/weixin_follower_img/',
                            url:'/information/account/uploadfile'
                        };
                        var account = new ValidationModel(new AccountModel(accountInfo), accountInfo);
                        otherKOAttribute(account,accountInfo);
                        account.isEditable(data.data.isEditable);
                        accountSubmit(self,account);
                        ko.applyBindings(account, this.el[0]);
                        validationNoticeCssInit(account);
                        self.el.find('[name=edit]').hide();
                        self.el.find('.modify').bind('click',function(){
                            if(account.isEditable() == true){
                                self.el.find('[name=edit]').show();
                                self.el.find('[name=show]').hide();
                            }else{
                                W.alert("数据正在审核中！");
                            }
                        });
                        if (accountInfo.weibo_type == WEIBO_TYPE_PENGYOUQUAN) {
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_shoe_weixinpengyouquan.jpg">', target: '.followExShowimg'});
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_history_weixinpengyouquan.jpg">', target: '.historyExShowimg'});
                            doUploadImgs(this.el, accountInfo.account_id);
                            showFollowersReviewDialog(this.el, accountInfo.account_id, WEIBO_TYPE_PENGYOUQUAN);
                        }
                    }
                }
            });
        }
        if(displayableTabs.pengyouquanPlatform){
            items.push(            {
                tabTitle: "平台数据",
                icon: "freedataIcon1",
                loader: {
                    url: '/information/accountmanage/detail',
                    data: {
                        account_id: row.cells.account_id
                    },
                    success: function (data) {
                        this.setHtml(pengyouquanDetailPlatformInfoTabTpl);
                        var accountInfo = data.data;
                        accountInfo.weixin_image_option = {
                            viewBasePath : '/img/uploadimg/weixin_follower_img/',
                            url:'/information/account/uploadfile'
                        };

                        var account = new ValidationModel(new AccountModel(accountInfo), accountInfo);
                        ko.applyBindings(account, this.el[0]);
                        if (accountInfo.weibo_type == WEIBO_TYPE_PENGYOUQUAN) {
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_shoe_weixinpengyouquan.jpg">', target: '.followExShowimg'});
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_history_weixinpengyouquan.jpg">', target: '.historyExShowimg'});
                            doUploadImgs(this.el, accountInfo.account_id);
                            showFollowersReviewDialog(this.el, accountInfo.account_id, WEIBO_TYPE_PENGYOUQUAN);
                        }
                    }
                }
            });
        }
        if(displayableTabs.accountOpen){
            items.push(            {
                tabTitle: "账号数据",
                icon: "freedataIcon1",
                loader: {
                    url: '/information/accountmanage/detail',
                    data: {
                        account_id: row.cells.account_id
                    },
                    success: function (data) {
                        this.setHtml(detailAccountInfoTabTpl);
                        var accountInfo = data.data;
                        accountInfo.weixin_image_option = {
                            viewBasePath : '/img/uploadimg/weixin_follower_img/',
                            url:'/information/account/uploadfile'
                        };

                        var account = new ValidationModel(new AccountModel(accountInfo), accountInfo);
                        ko.applyBindings(account, this.el[0]);

                        //微信账号修改头像二维码等
                        if (accountInfo.weibo_type == WEIBO_TYPE_WEIXIN) {
                            doUploadImgs(this.el, accountInfo.account_id);
                            showFollowersReviewDialog(this.el, accountInfo.account_id);
                        }

                        if (accountInfo.weibo_type == WEIBO_TYPE_PENGYOUQUAN) {
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_shoe_weixinpengyouquan.jpg">', target: '.followExShowimg'});
                            new W.Tips({type: 'click',floor: "high", autoHide: false, html: '<img width="250" src="/resources/images/model_history_weixinpengyouquan.jpg">', target: '.historyExShowimg'});
                            doUploadImgs(this.el, accountInfo.account_id);
                            showFollowersReviewDialog(this.el, accountInfo.account_id, WEIBO_TYPE_PENGYOUQUAN);
                        }
                    }
                }
            });
        }
        if (displayableTabs.fansOpen) {
            items.push({
                tabTitle: "粉丝分析",
                icon: "freedataIcon2",
                loader: {
                    url: '/information/accountmanage/gettip?accountId=' + row.cells.account_id,
                    dataType: "json",
                    success: function (json) {
                        var html = detailFansTrendsTab.fetchContent(json, 'FANS', '', '', this);
                        this.setContent(html);
                    }
                }
            });
        }

        if (displayableTabs.trendsOpen) {
            items.push({
                tabTitle: "趋势分析",
                icon: "freedataIcon3",
                loader: {
                    url: '/information/accountmanage/gettip?accountId=' + row.cells.account_id,
                    dataType: "json",
                    success: function (json) {
                        var html = detailFansTrendsTab.fetchContent(json, 'TRENDS', '', '', this);
                        this.setContent(html);
                    }
                }
            });
        }

        var win = new W.Window({
            title: row.cells.weibo_name,
            id: "detail_" + row.id,
            height: 560,
            cls: "freedataWindow",
            width: 780,
            layout: {
                type: "tab"
            },
            items: items,
            bbar: [{
                cls: "freedataWindow_btn",
                handler: function () {
                    // feedback.showFeedbackWin(row.cells.account_id);
                }
            }]
        });


        win.addListener('beforeclose', function() {
            var allTips = W.getCmps({
                group: "header"
            });

            $.each(allTips, function (i, e) {
                e.hide();
            });
        });

        win.addListener('aftersetActiveTab', function () {
            W.getCmp('openTip').hide();
        });

        // 设置标签页的宽度问题
        win.addListener('aftershow', function () {
            var w = Math.floor(100 / this.getItems().length) - 1;
            this.el.find(".weiboyiTabCtnr_tabhead li").css({
                width: w + "%"
            });
        });

        return win;
    }

    /**
     * 点击查看详情
     * @param row
     */
    function detail(row) {
        row.detailWin = W.getCmp("detail_" + row.id) || createDetailWin(row);
        row.detailWin.show();
    }

    // 设置 详情->粉丝截图:修改 按钮行为: 显示 修改粉丝截图 对话框
    function showFollowersReviewDialog(container, accountId, weiboType) {
        if (W.util.ie6 && !window.onerror) {window.onerror = function() {return true;}}
        var data = {accountId: accountId};
        data['admin_qq'] = admin_qq.split(',')[0];
        if(weiboType == undefined){
            var html = doT.template($('#followersUploadForm_html').html())(data);
        }else if(weiboType == WEIBO_TYPE_PENGYOUQUAN){
            data['weiboType'] = weiboType;
            var html = doT.template($('#followersUploadForm_pengyouquan_html').html())(data);
        }

        if (typeof(container.dialog) == 'undefined') {
            container.dialog = new W.Tips({
                target: '#showUploadFollowers_' + accountId,
                floor: 'middle',
                autoHide: false,
                lazyLoad: false,
                width:430,
                html: html,
                group: 'header',
                listeners: {
                    aftershow: function() {
                        doFollowersUploadImgs(container, accountId, weiboType);
                        uploadFollowerFormBehavior(container, accountId, weiboType);
                    }
                }
            });
        }
    }

    // 设置 (修改粉丝截图)对话框中的 上传按钮 行为
    function doFollowersUploadImgs(container, accountId, weiboType) {
        if (!container || !accountId) {return;}
        var item = {
            el: "#js_uploadFollowers_" + accountId,		//粉丝截图
            thumb: "#followersThumb",
            from: "followers",
            size: 1024 * 2
        };
        if(weiboType == undefined){
            var url = "/ajax/upload/modifyreview";
        }else if(weiboType == WEIBO_TYPE_PENGYOUQUAN){
            var url = "/ajax/upload/modifyreviewpengyouquan";
        }

        $(item.el).uploadify({
            uploader: url,
            buttonText: "上传",
            fileTypeExts: "*.jpg;*.png;*.gif;*.jpeg",
            fileTypeDesc: "JPG 图片; PNG 图片; GIF 图片",
            fileSizeLimit: item.size,
            formData: {
                from: item.from,
                account_id: accountId,
                sessionid: $("#session_id").val()
            },
            queueID: "none",
            onUploadSuccess: function (file, data, response) {
                data = $.parseJSON(data);
                if (data && data.success) {
                    $('#js_followersUploaded_' + accountId).val('1');
                    if (data.url) {
                        var ctner_id = '#uploaded_img_td_' + accountId;
                        $(ctner_id).html('<div class="img_ctner"><a target="_blank" href="' + data.url + '" title="点击查看原图"><img  alt="' + (data.msg || '上传成功') + '" src="' + data.url + '" class="platform_imgs_followerImg" /></a></div>');
                        $(ctner_id + ' img').imageScale({width: 72,height: 72});
                    } else {
                        $('#js_uploadFollowers_label_' + accountId).hide();
                    }
                }
                else {
                    W.alert((data && data.msg) || "上传失败！", "error");
                }
            }
        });
    }

    // 调整 上传粉丝截图/输入粉丝数 表单行为
    function uploadFollowerFormBehavior(container, accountId, weiboType) {
        var formId = '#followers_num_form_' + accountId;
        $(formId + ' .btn_small_normal').click(function() {
            if (container.dialog) {
                container.dialog.close();
            }
        });
        if(weiboType == undefined){
            var validate = {
                rules: {
                    followers_count: {
                        required: true,
                        customRegExp: /^\d+$/,
                        min: weixin_min_followers
                    }
                },
                messages: {
                    followers_count: {
                        required: '此处为必填',
                        customRegExp: '请填写正确的数字！',
                        min: '粉丝数至少' + weixin_min_followers + '！'
                    }
                }
            };
        }else if(weiboType == WEIBO_TYPE_PENGYOUQUAN){
            var validate = {
                rules: {
                    followers_count: {
                        required: true,
                        customRegExp: /^\d+$/,
                        min: pengyouquan_min_followers
                    }
                },
                messages: {
                    followers_count: {
                        required: '必须填写“好友数”',
                        customRegExp: '请填写正确的数字！',
                        min: '好友数至少' + pengyouquan_min_followers + '！'
                    }
                }
            };
        }

        var form = new W.Form({
            form: formId,
            validate: validate,
            type: 'post',
            submitSuccess: function(data) {
                if (data.success) {
                    W.message(data.msg || '提交成功', 'success');
                } else {
                    W.alert(data.msg || '服务器端处理出错', "error");
                }
                if (container.dialog) {
                    var img = $('img.platform_imgs_followerImg', container.dialog.getCurrentTarget()[0].parentNode.parentNode);
                    img.attr('src', data.url);
                    img.parent().attr('href', data.url);
                    $('#uploaded_img_td_' + accountId + ' img').attr('src', data.url);
                    $('#uploaded_img_td_' + accountId + ' a').attr('href', data.url);
                    container.dialog.close();
                }
            },
            submitError: function(data) {
                W.alert((data && data.msg) || "提交数据出现错误", "error");
                if (data && data.success && data.url) {
                    if (container.dialog) {
                        var img = $('img.platform_imgs_followerImg', container.dialog.getCurrentTarget()[0].parentNode.parentNode);
                        img.attr('src', data.url);
                        img.parent().attr('href', data.url);
                        $('#uploaded_img_td_' + accountId + ' img').attr('src', data.url);
                        $('#uploaded_img_td_' + accountId + ' a').attr('href', data.url);
                    }
                }
            }
        });
        $(formId + ' .btn_small_important').click(function() {
            var valid = form.valid();
            var uploaded = !!($('#js_followersUploaded_' + accountId).val() == 1);
            if (!uploaded) {
                $('#js_uploadFollowers_label_' + accountId).show();
            }
            if (uploaded && valid) {
                W.confirm('亲，确定要提交嘛？', function(sure) {
                    if (sure) {
                        var res = form.ajaxSubmit();
                        if (W._isDeferred(res)) {
                            if (container.dialog) {
                                container.dialog.close();
                            }
                        }
                    }
                });
            }
        });
    }

    function doUploadImgs(el, accountId) {

        el.find(".platformImgsDiv img").imageScale({
            width: 72,
            height: 72
        });

        el.find(".js_platform_imgs").fancybox();

        var uploadItems = [
            {
                el: ".js_uploadInfo",			//趋势截图
                thumb: "#infoThumb",
                from: "info",
                size: 1024 * 2
            },
            {
                el: ".js_uploadQrCode",			//二维码
                thumb: "#qrCodeThumb",
                from: "qr_code",
                size: 1024 * 2
            },
            {
                el: ".js_uploadAvatar",			//头像
                thumb: "#avatarThumb",
                from: "avatar",
                size: 1024 * 2
            },
            {
                el: ".js_uploadAccountAvatar",			//账号头像
                thumb: "#accountAvatarThumb",
                from: "accountAvatar",
                size: 1024 * 2
            },
            {
                el: ".js_uploadPersonAvatar",			//真人头像
                thumb: "#personAvatarThumb",
                from: "personAvatar",
                size: 1024 * 2
            }
        ];
        $.each(uploadItems, function (i, item) {
            var uploadEL = el.find(item.el);
            if (uploadEL.length) {
                uploadEL.uploadify({
                    uploader: "/ajax/upload/modifyreview",
                    buttonText: "修改",
                    fileTypeExts: "*.jpg;*.png;*.gif;*.jpeg;*.csv",
                    fileTypeDesc: "JPG 图片; PNG 图片; GIF 图片",
                    uploadLimit: 10,
                    fileSizeLimit: item.size,
                    formData: {
                        from: item.from,
                        account_id: accountId
                    },
                    //这里随意设置了一个没有的id,表示不显示上传的进度
                    queueID: "none",
                    'onUploadSuccess': function (file, data, response) {
                        data = $.parseJSON(data);
                        console.log(data);
                        if (data && data.success) {
                            var ul = this.button.closest("ul");
                            W.message(data.msg, "success");
                            ul.find("img").attr("src", data.url);
                            ul.find("a").attr("href", data.url);
                        }
                        else {
                            W.alert(data.msg, "error");
                        }
                    }
                });
            }
        });
        el.find(".submitHistory").bind("click",function(){
            $.post("/ajax/upload/modifyreview",{filename : el.find("[name=uploadImgReleaseHistory]").val(),from : "releaseHistory",account_id : accountId},function(data){
                if (data && data.success) {
                    W.message(data.msg, "success");
                }
                else {
                    W.alert(data.msg, "error");
                }
            });
        });
    }

    /**
     * 设置是否显示粉丝分析和趋势分析标签页
     * @param tabs
     */
    function setDisplayableTabs(tabs) {
        displayableTabs = tabs;
    }
    function otherKOAttribute(account,accountInfo){
        account.isEditable=ko.observable();
        account.weibo_alert_notice = ko.observable();
        account.edu_error_display = ko.computed(function () {
                return account.edu_degree.error();
            }
        );
        account.errors = ko.validation.group([account.true_name,account.age,account.profession_type,account.gender,account.area_id,account.edu_degree,account.weibo_link,account.friend_desc,account.account_advantage]);
        account.area_id.province_option(accountInfo.province_option);
        account.area_id.city_option(accountInfo.city_option);
        account.area_id.district_option(accountInfo.district_option);
        if(accountInfo.area_id != null){
            if(parseInt(accountInfo.area_id.toString().substr(0,3)) == 101){
                account.area_id.district(parseInt(accountInfo.area_id.toString().substr(0)));
                account.area_id.city(parseInt(accountInfo.area_id.toString().substr(0,7)));
                account.area_id.province(parseInt(accountInfo.area_id.toString().substr(0,5)));
                account.area_id.country(parseInt(accountInfo.area_id.toString().substr(0,3)));
            }else{
                account.area_id.country(500);
            }
        }
        account.profession_error_display = ko.computed(function () {
                return account.profession_type.error() || account.profession_other_info.error();
            }
        );
    }

    function validationNoticeCssInit(account){
        var param=[account.age,account.friend_desc,account.true_name,account.account_advantage,account.weibo_link];
        for(var i=0;i<param.length;i++){
            if(param[i].error()){
                param[i].notice_css('error');
                param[i].notice_text(param[i].error());
            }else{
                param[i].notice_css("correct");
                param[i].notice_text("");
            }
        }
    }

    function accountSubmit(win,validation){
        validation.cancel = function(){
            win.el.find('[name=edit]').hide();
            win.el.find('[name=show]').show();
        };
        validation.submit = function () {
            if(validation.errors().length > 0){
//                W.alert(validation.errors()[0]);
                return;
            }
            W.confirm("确定要提交！",function(ok){
                if(ok){
                    validation.weibo_alert_notice("<div class='fl regi_loading'><img src='/resources/images/onload.gif'>数据提交中，请耐心等待！</div>");
                    var submitValues = {'weibo_type': validation.weibo_type()};

                    var formElement = win.el.find("#accountUpdate").serializeArray();
                    $.map(formElement, function (e) {
                            if (e.name && e.value) {
                                submitValues[e.name] = e.value;
                            }
                        }
                    );
                    console.log(submitValues);

                    var defer = $.restPost('/information/accountmanage/updateaccountdetail', submitValues);
                    defer.done(function (msg, data) {
                        W.alert(msg || '修改成功', 'success', function() {
                            tips.close();
                            win.el.find('[name=edit]').hide();
                            win.el.find('[name=show]').show();
                            validation.isEditable(false);
                        });
                        validation.weibo_alert_notice('');
                    });

                    defer.fail(function (error) {
                        validation.weibo_alert_notice('');
                        W.alert(error);
                    });
                }
            });
        };
    }

    return  {
        detail: detail,
        setDisplayableTabs: setDisplayableTabs
    }
});