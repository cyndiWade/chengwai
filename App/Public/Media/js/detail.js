define(function(require) {

    var ko = require('knockout'),
        AccountModel = require('./account_model.js'),
        ValidationModel = require('./account_validation_model.js'),
        detailAccountInfoTabTpl = require('./tpl/detail_account_info_tab.tpl#');

    //动态设置需要展示的tab
    var displayableTabs;
    
    var WEIBO_TYPE_WEIXIN = 3;
    var admin_qq;
    /**
     * 创建查看详情弹窗
     * @param row
     * @returns {*}
     */
    function createDetailWin(row) {
        admin_qq = row.cells.admin_qq;
        var items = [];
        
        if(displayableTabs.accountOpen){
            items.push(            {
                tabTitle: "账号数据",
                icon: "freedataIcon1",
                loader: {
                    url: '/Media/SocialAccount/detail',
                    data: {
                        account_id: row.cells.account_id,
                        account_type: row.cells.weibo_type
                    },
                    success: function (data) {
                        this.setHtml(detailAccountInfoTabTpl);
                        var accountInfo = data.data;
                        accountInfo.weixin_image_option = {
                            viewBasePath : $.pluploadOpts.viewBasePath,
                            // url: $.pluploadOpts.url
                            // viewBasePath : '',
                            url: $.pluploadOpts.url
                        };

                        var account = new ValidationModel(new AccountModel(accountInfo), accountInfo);
                        ko.applyBindings(account, this.el[0]);

                        //微信账号修改头像二维码等
                        if (accountInfo.weibo_type == WEIBO_TYPE_WEIXIN) {
                            doUploadImgs(this.el, accountInfo.account_id, WEIBO_TYPE_WEIXIN);
                            showFollowersReviewDialog(this.el, accountInfo.account_id, WEIBO_TYPE_WEIXIN);
                        }
                    }
                }
            });
        }
        
        var win = new W.Window({
            title: row.cells.weibo_name,
            id: "detail_" + row.id + '_' + row.weibo_type,
            height: 560,
            cls: "freedataWindow",
            width: 780,
            layout: {
                type: "tab"
            },
            items: items,
            bbar: []
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
        row.detailWin = W.getCmp("detail_" + row.id + '_' + row.weibo_type) || createDetailWin(row);
        row.detailWin.show();
    }

    // 设置 详情->粉丝截图:修改 按钮行为: 显示 修改粉丝截图 对话框
    function showFollowersReviewDialog(container, accountId, weiboType) {
        if (W.util.ie6 && !window.onerror) {window.onerror = function() {return true;}}
        var data = {accountId: accountId, accountType: weiboType};
        data['admin_qq'] = admin_qq.split(',')[0];
        var html = doT.template($('#followersUploadForm_html').html())(data);

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
        // var url = "/ajax/upload/modifyreview";
        var url = '/Media/Public/uploadImg';

        $(item.el).uploadify({
            uploader: url,
            buttonText: "上传",
            fileTypeExts: "*.jpg;*.png;*.gif;*.jpeg",
            fileTypeDesc: "JPG 图片; PNG 图片; GIF 图片",
            fileSizeLimit: item.size,
            formData: {
                from: item.from,
                account_id: accountId,
                account_type: weiboType,
                sessionid: $("#session_id").val()
            },
            queueID: "none",
            onUploadSuccess: function (file, data, response) {
                data = $.parseJSON(data);
                if (data && data.success) {
                    $('#js_followersUploaded_' + accountId).val('1');
                    if (data.url) {
                        var url = (/^http/.test(data.url) === false) ? $.pluploadOpts.viewBasePath + data.url : data.url;
                        $('input[name=uploadimg]').val(url);
                        var ctner_id = '#uploaded_img_td_' + accountId;
                        $(ctner_id).html('<div class="img_ctner"><a target="_blank" href="' + url + '" title="点击查看原图"><img  alt="' + (data.msg || '上传成功') + '" src="' + url + '" class="platform_imgs_followerImg" /></a></div>');
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

    function doUploadImgs(el, accountId, weiboType) {

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
                    // uploader: "/ajax/upload/modifyreview",
                    uploader: '/Media/SocialAccount/modifyreview',
                    buttonText: "修改",
                    fileTypeExts: "*.jpg;*.png;*.gif;*.jpeg;*.csv",
                    fileTypeDesc: "JPG 图片; PNG 图片; GIF 图片",
                    uploadLimit: 10,
                    fileSizeLimit: item.size,
                    formData: {
                        from: item.from,
                        account_id: accountId,
                        account_type: weiboType
                    },
                    //这里随意设置了一个没有的id,表示不显示上传的进度
                    queueID: "none",
                    'onUploadSuccess': function (file, data, response) {
                        data = $.parseJSON(data);
                        console.log(data);
                        if (data && data.success) {
                            var ul = this.button.closest("ul");
                            console.log(ul);
                            W.message(data.msg, "success");
                            ul.find("img").attr("src", data.url);
                            ul.find("a").attr("href", data.url);
                            console.log(ul.find("a").attr("href"));
                        }
                        else {
                            W.alert(data.msg, "error");
                        }
                    }
                });
            }
        });
        el.find(".submitHistory").bind("click",function(){
            $.post('/Media/SocialAccount/modifyreview',{filename : el.find("[name=uploadImgReleaseHistory]").val(),from : "releaseHistory",account_id : accountId},function(data){
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

    return  {
        detail: detail,
        setDisplayableTabs: setDisplayableTabs
    }
});