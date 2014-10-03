define(function(require, exports) {
    require('./update_price.js');
    // var initIsHardAdTips = require("./is_extend.js");

    var detail = require('./detail.js');
    var priceTpl = require('./tpl/weixin_price.tpl#');

    var weibo_type = 3;

    var grid;

    new W.Tips({
        autoHide: false,
        group: 'edit',
        target: "a[tag=js_tip]",
        type: "click",
        title: "微信号",
        height: 'auto',
        listeners: {
            ontargetchanged: function () {
                var weibo_id = this.getCurrentTarget().attr("weibo_id");
                this.setHtml(weibo_id);
            }
        }
    });

    function initGrid(filters,weiboType) {
        var formatters = {
            price: function (data, row) {
                return W.util.encodeHtmlChar($.trim(data)) || "暂无报价";
            },
            detail: function (data, row) {
                var a = $("<a href='javascript:void(0)' class='yel'></a>").html("查看");
                return a.click(function () {
                    detail.detail(row);
                });
            },
            followers_count: function (data, row) {
                return data > 0 ? data : "暂无数据";
            }
        };

        var PreParseData = function(row,col) {
            var soft2hard = {
                single_graphic_price:'single_graphic_hard_price',
                multi_graphic_top_price:'multi_graphic_hard_top_price',
                multi_graphic_second_price:'multi_graphic_hard_second_price',
                multi_graphic_other_price:'multi_graphic_hard_other_price'
            };

            row.cells.hard_price_title = soft2hard[col.dataIndex];
            row.cells.hard_price_value = formatters.price(row.cells[row.cells.hard_price_title],row);
            row.cells.hard_price_operate_class = getOperateClass(row,soft2hard[col.dataIndex]);

            row.cells.soft_price_title = col.dataIndex;
            row.cells.soft_price_value = formatters.price(row.cells[col.dataIndex],row);
            row.cells.soft_price_operate_class = getOperateClass(row,col.dataIndex);

            return row;
        }

        var editHandler = function (valu, textarea, row, col){
            var value = W.util.encodeHtmlChar(valu);
            var num = Number(value);
            if(valu===''|| parseFloat(valu) == row.cells[col.dataIndex]) return false;
            if (isNaN(num)) {
                W.alert('请输入合法的价格');
                return false;
            }
            var params = {}
            params['price_type'] = col.dataIndex;
            params['price_value'] = valu;
            params['row'] = row.cells;
            var checkResult = true;
            var result = $.Deferred();
            $.ajax({
                    type:'GET',
                    url:'/information/accountmanage/checkprice',
                    data:params,
                    async:false,
                    dataType:'json',
                    success:function(data){
                        if(!data.isEditAble){
                            W.alert(data.message);
                            result.reject();
                        }else{
                            W.confirm(data.message,function(sure){
                                if(sure){
                                    $.ajax({
                                            type:'GET',
                                            url:'/information/accountmanage/updateprice',
                                            data:params,
                                            async:false,
                                            dataType:'json',
                                            success:function(data){
                                                    if(!data.result){
                                                        result.reject();
                                                        W.alert(data.message);
                                                    }else{
                                                        W.alert(data.message, 'success');
                                                        grid.reload();
                                                    }
                                            },
                                            error:function (){
                                                W.alert("更新失败，联系管理员！");
                                            }
                                            })
                                    result.resolve();
                                }else{
                                    result.reject();
                                }

                            })
                        }
                    },
                   error:function (){
                       W.alert("操作失败！请联系管理员");
                   }
                   });
            return result;
        }

        function isPriceEditable(value, row,item_info) {
            return row.cells.is_verify == 2;
        }

        /**
         * 创建带tips的title
         */
        function renderTitle(title, id) {
            // var tpl = "<div><span>{0}</span><img id='{1}' src='/resources/images/icon_tips.jpg' title='点击查看详情'></div>";
            var tpl = '<div class="pr"><em class="emqa" id="{1}" title="点击查看详情"></em>{0}</div>';
            return W.util.formatStr(tpl, title, id);
        }

        function renderTitleMiddelImg(title, id, titlesub) {
            // var tpl = "<div><span>{0}</span><img id='{1}' src='/resources/images/icon_tips.jpg' title='点击查看详情'><span>{2}</span></div>";
            var tpl = '<div class="pr"><em class="emqa" id="{1}" title="点击查看详情"></em>{0}{2}</div>';
            return W.util.formatStr(tpl, title, id, titlesub);
        }

        grid = exports.grid = new W.Grid({
            messages: {
                nodata: "很抱歉，没有找到合适的账号",
                noQueryResult: "很抱歉，没有找到合适的账号"
            },
            renderTo: "#list",
            url: '/Media/SocialAccount/getAccountList/?filters%5Btype%5D=' + weiboType,
            params: filters,
            columns: [
                {
                    align: "left",
                    text: "账号名",
                    cls: "t1",
                    dataIndex: "weibo_name",
                    // 使用模板前对数据预处理
                    tmplPreprocessor: tmplColWeiboNamePreprocessor,
                    tmplId: 'tmplColWeiboName'
                },
                {
                    align: "left",
                    text: "粉丝数",
                    cls: "t2",
                    dataIndex: "followers_count",
                    formatter: formatters.followers_count
                },
                {
                    align: "left",
                    text: renderTitleMiddelImg("单图文报", "singleGraphicPrice", '<br/>价'),
                    dataIndex: "single_graphic_price",
                    tmplPreprocessor: PreParseData,
                    cls: "t8",
                    tmpl: priceTpl
                },
                {
                    align: "left",
                    text: renderTitleMiddelImg('多图文第', "multiGraphicTopPrice", "<br/>一条报价"),
                    dataIndex: "multi_graphic_top_price",
                    tmplPreprocessor: PreParseData,
                    cls: "t8",
                    tmpl: priceTpl
                },
                {
                    align: "left",
                    text: renderTitleMiddelImg("多图文第", "multiGraphicSecondPrice", '<br/>二条报价'),
                    dataIndex: "multi_graphic_second_price",
                    tmplPreprocessor: PreParseData,
                    cls: "t8",
                    tmpl: priceTpl
                },
                {
                    align: "left",
                    text: renderTitleMiddelImg("多图文其他", "multiGraphicOtherPrice", '<br/>位置报价'),
                    dataIndex: "multi_graphic_other_price",
                    cls: "t8",
                    tmplPreprocessor: PreParseData,
                    tmpl: priceTpl
                },
                {
                    align: "left",
                    text: renderTitle("带号价", "daihaojia"),
                    dataIndex: "content_price",
                    cls: "t7",
                    editable: isPriceEditable,
                    editHandler: editHandler,
                    formatter: formatters.price
                },

                {
                    align: "left",
                    text: "周订单",
                    cls: "t5",
                    dataIndex: "orders_weekly"

                },
                {
                    align: "left",
                    text: "月订单",
                    cls: "t6",
                    dataIndex: "orders_monthly"
                },
                {
                    align: "left",
                    text: "审核<br/>通过",
                    dataIndex: "is_verify",
                    cls: "t10",
                    formatter: function (data, row) {
                        if (data == "0" || data == '2') {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_review' class='blue' data-accountId = " + row.cells.account_id + " data-accountType = " + row.cells.weibo_type + "></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");
                            return div.append("否&nbsp;").append(a);
                        } else {
                            return '是';
                        }
                    }
                },
                {
                    align: "left",
                    text: "接单<br/>状态",
                    cls: "t11",
                    dataIndex: "is_allow_order",
                    formatter: function (data, row) {
                        var div = $("<div></div>");
                        if (row.cells.is_verify == '2' || row.cells.is_verify == '0') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var a = $("<a id='js_show_allow_order' class='blue' data-accountId = " + row.cells.account_id + " data-accountType = " + row.cells.weibo_type + "></a>");

                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append(a);
                        }
                    }
                },
                {
                    align: "left",
                    text: renderTitleMiddelImg("是否", "shifoushangjia", '<br/>上架'),
                    dataIndex: "is_online",
                    cls: "t11",
                    formatter: function (data, row) {
                        if (row.cells.is_verify == '2' || row.cells.is_allow_order == '2') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_online' class='blue' data-accountId = " + row.cells.account_id + " data-accountType = " + row.cells.weibo_type + "></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append('<br/>').append(a);

                        }
                    }
                },
                /* {
                    align: "center",
                    text: renderTitleMiddelImg("是否", "shifouyingguang", '<br/>接硬广'),
                    dataIndex: "is_extend",
                    cls: "t12",
                    formatter: function (data, row) {

                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";
                        if (row.cells.is_verify == 1) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "' class = 'js_isHardDialogue set_img' src='/resources/images/ico_set.gif'/></a>"
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "' data_account_type = '" + weiboType + "' class = 'js_isHardDialogue' data_radio = '" + data + "'></a>";
                        }
                        if (data == "1") {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;是');
                            return $(imgTip).append('<em class="yes">是</em>');
                        } else {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;否');
                            return $(imgTip).append('<em class="no">否</em>');
                        }
                    }
                }, */
                {
                    align: "center",
                    cls: "t11",
                    text: renderTitleMiddelImg("暂不", "zanbujiedan", '<br/>接单'),
                    dataIndex: "leave",
                    formatter: function (data, row) {

                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";

                        if (row.cells.is_verify == 1) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "'id='leaveDialogue' src='/resources/images/ico_set.gif' class='set_img' /></a>";
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "'id='leaveDialogue'></a>";
                        }

                        // var div = $("<div></div>");
                        // div.append(imgTip);

                        if (data == true) {
                            // div.append('&nbsp;是');
                            // return div;
                            return $(imgTip).append('<em class="yes">是</em>');
                        } else {
                            // div.append("&nbsp;否" + data);
                            // return div;
                            return $(imgTip).append('<em class="no">否</em>');
                        }
                    }
                },

                {
                    align: "center",
                    text: '<span class="js_tour_detail">详情</span>',
                    dataIndex: "",
                    cls: "last",
                    formatter: formatters.detail
                }
            ],
            listeners: {
                //列表加载完成后要触发方法
                afterrefresh: function () {
                    //找到相关账号数量
                    $("#totalNum").text(this.getTotal());
                    //是否显示‘查看全部按钮’
                    // if (this.status.hasFilters) {
                        // $("#searchAll").show();
                    // } else {
                        // $("#searchAll").hide();
                    // }
                    hideAllTips();
                },
                // 列表插入一行的事件
                afterinsertRowBefore: function (state, row, index) {
                    // 判断改行是否是高亮显示
                    if (row.cells.is_tip == 1 && !!row.cells.tip_content) {
                        row.highlight = true;
                        row.el.addClass("highlightLine");
                        new W.Tips({
                            target: row.el,
                            text: row.cells.highlightLineContent,
                            type: "mouseenter",
                            autoHide: 0
                        });
                    }
                }
            }
        });
    }



    function hideAllTips() {
        $.each(W.getCmps({
            xtype: "tips"
        }), function (i, e) {
            e.hide();
        });
    }

    /**
     * /根据不同的价格类型获得不同的操作
     * @param row
     * @param price_type
     */
    function getOperateClass(row, price_type){
       // allowReduce ,allowReduceAndRaise，notReduceOrRaise
       var countKey = price_type + '_raise_count_two_week';
        //两周内只能上调一次,但是下调次数无限制

        //未审核通过则不可调价格
       if(row.cells.is_verify != 1) return 'notReduceOrRaise';

       if(row.cells[countKey] && row.cells[countKey] >= 1 ){
           //两周内上涨一次了则只能降价
           return 'allowReduce';
       }else{
           return 'allowReduceAndRaise';
       }



    }

    /**
     * 是否公开
     */
    function initIsOpenTips() {
        tips = new W.Tips({
            group: 'header',
            target: ".js_setIsOpen",
            floor: 'high',
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "是否公开",
            html: "<div class='policy_modifications'>" + "<table border='0' cellspacing='0' cellpadding='0'>" + "<tr>" + "<td>" + "<label class='policy_modifications_yes'>" + "<input type='radio' name='openRadio' class='js_openTrue' value=1 />" + "<span>是</span>" + "</label>" + "<label>" + "<input type='radio' name='openRadio' class='js_openFalse' value=2/>" + "<span>否</span>" + "</label>" + "</td>" + "</tr>" + "<tr><td class='modifications_select_all'><label><input type='checkbox'  name='selectAllPolicyCheckbox' /><span>本次操作应用于所有账号。</span></label></td></tr>" + "</table>" + "</div>",
            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var accountId = this.getCurrentTarget().attr('data_account_id');
                        var isOpenRadio = this.el.find('input[name=openRadio]:checked').val();
                        var selectAllPolicyCheckbox_ = this.el.find('input[name=selectAllPolicyCheckbox]').attr('checked');
                        if ('undefined' == typeof(selectAllPolicyCheckbox_)) {
                            selectAllPolicyCheckbox_ = this.el.find('input[name=selectAllPolicyCheckbox]').prop('checked');
                            selectAllPolicyCheckbox_ = selectAllPolicyCheckbox_ ? 'checked' : '';
                        }
                        var selectAllPolicyCheckbox = selectAllPolicyCheckbox_ == 'checked' ? 'checked' : '';
                        var confirmText;
                        if (selectAllPolicyCheckbox) {
                            confirmText = isOpenRadio == 1 ? '亲！确定要将全部账号设置为公开吗？' : '亲！确定要将全部账号设置为不公开吗？';
                        } else {
                            confirmText = isOpenRadio == 1 ? '亲！确定要将账号设置为公开吗？' : '亲！确定要将账号设置为不公开吗？';
                        }

                        W.confirm(confirmText, function (cflag) {
                            if (cflag) {
                                var message = W.message("处理中", "loading", 1000);
                                return $.ajax({
                                    type: "POST",
                                    url: "/information/accountmanage/setopen",
                                    data: "accountId=" + accountId + '&isOpen=' + isOpenRadio + '&selectAllCheckbox=' + selectAllPolicyCheckbox,
                                    dataType: "json",
                                    success: function (msg) {
                                        message.close();
                                        location.reload();
                                    }
                                });
                            } else {
                                return false;
                            }
                        });
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
                    if (data_radio == 2) {
                        this.el.find(".js_openFalse").attr('checked', 'checked');
                    } else {
                        this.el.find(".js_openTrue").attr('checked', 'checked');
                    }
                    this.el.find("input[name='selectAllPolicyCheckbox']").attr("checked", false);
                }
            }
        });
    }

    /**
     * 微信是否接单图文
     */
    function initWeixinIsAllowDantuwenTips() {
        var tips = new W.Tips({
            group: 'header',
            target: ".js_weixin_is_allow_dantuwen",
            floor: 'high',
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "是否接“单图文”",
            html: "<div class='policy_modifications'>" + "<table border='0' cellspacing='0' cellpadding='0'>" + "<tr>" + "<td>" + "<label class='policy_modifications_yes'>" + "<input type='radio' name='openRadio' class='js_openTrue' value=1 />" + "<span>是</span>" + "</label>" + "<label>" + "<input type='radio' name='openRadio' class='js_openFalse' value=2 />" + "<span>否</span>" + "</label>" + "</td>" + "</tr>"
                + "<tr class='js_tips'><td><label><span style=\"color:#f00\">您已设置为‘否’，成功提交后当前账号将不会再接到微信“单图文”的所有订单。</span></label></td></tr>"
                + "<tr><td class='modifications_select_all'><label><input type='checkbox'  name='selectAllPolicyCheckbox' /><span>本次操作应用于所有<em style=\"color:#f00\">微信</em>账号。</span></label></td></tr>" + "</table>" + "</div>",

            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var accountId = this.getCurrentTarget().attr('data-account_id');
                        var isOpenRadio = this.el.find('input[name=openRadio]:checked').val();
                        var selectAllPolicyCheckbox_ = this.el.find('input[name=selectAllPolicyCheckbox]').attr('checked');
                        if ('undefined' == typeof(selectAllPolicyCheckbox_)) {
                            selectAllPolicyCheckbox_ = this.el.find('input[name=selectAllPolicyCheckbox]').prop('checked');
                            selectAllPolicyCheckbox_ = selectAllPolicyCheckbox_ ? 'checked' : '';
                        }
                        var selectAllPolicyCheckbox = selectAllPolicyCheckbox_ == 'checked' ? 'checked' : '';
                        var confirmText;
                        if (selectAllPolicyCheckbox) {
                            confirmText = isOpenRadio == 1 ? '亲！确定要将全部账号设置为接‘单图文’吗？' : '亲！确定要将全部账号设置为不接‘单图文’吗？';
                        } else {
                            confirmText = isOpenRadio == 1 ? '亲！确定要将账号设置为接‘单图文’吗？' : '亲！确定要将账号设置为不接‘单图文’吗？';
                        }

                        W.confirm(confirmText, function (cflag) {
                            if (cflag) {
                                var message = W.message("处理中", "loading", 1000);
                                return $.ajax({
                                    type: "POST",
                                    url: "/information/accountmanage/setweixinisallowdtuwen",
                                    data: "accountId=" + accountId + '&isOpen=' + isOpenRadio + '&selectAllCheckbox=' + selectAllPolicyCheckbox,
                                    dataType: "json",
                                    success: function (msg) {
                                        message.close();
                                        location.reload();
                                    }
                                });
                            } else {
                                return false;
                            }
                        });
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
                    var data_radio = this.getCurrentTarget().attr("data-radio");
                    if (data_radio == 2) {
                        this.el.find(".js_openFalse").prop('checked', true);
                        this.el.find('.js_tips').show();
                    } else {
                        this.el.find(".js_openTrue").prop('checked', true);
                        this.el.find('.js_tips').hide();
                    }
                    this.el.find("input[name='selectAllPolicyCheckbox']").attr("checked", false);
                }
            }
        });
        tips.el.on('click', ':radio', function() {
            if ($(this).val() === '2') {
                tips.el.find('.js_tips').show();
            } else {
                tips.el.find('.js_tips').hide();
            }
        });
    }

    /**
     * 接单上限
     */

    function initCeilingTips() {
        new W.Tips({
            group: 'edit',
            target: ".js_onlineDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "接单上限",
            //lazyLoad : false,
            html: '<div class="policy_modifications">' + '<table border="0" cellspacing="0" cellpadding="0">' + '<tr><td><label class="policy_modifications_yes js_appendPeriod"><input class="js_periodAccountId" type="text" style="display:none" value="" /><input type="radio" name="periodRadio" class="js_periodRadioTrue" value="1"/><span>是</span></label><label><input type="radio" class="js_periodRadioFalse" name="periodRadio" value="0"/><span>否</span></label></td></tr><tr class="js_orderMaxDiv" style="display:none" ><td><label><span>最大接单数&nbsp;</span><input type="text" class="inputXLarge js_orderMaxInput" /></label></td></tr><tr><td class="modifications_select_all"><label><input type="checkbox"  name="selectAllPeriodCheckbox" /><span>本次操作应用于所有账号。</span></label></td></tr></table></div>',
            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var isOnlineRadio = $('input[name=periodRadio]:checked').val();
                        var selectAllCheckbox = $('input[name=selectAllPeriodCheckbox]').attr('checked');
                        if ('undefined' == typeof(selectAllCheckbox)) {
                            selectAllCheckbox = $('input[name=selectAllPeriodCheckbox]').prop('checked');
                            selectAllCheckbox = selectAllCheckbox ? 1 : 0;
                        }
                        var orderMaxInput = $(".js_orderMaxInput").val();
                        var accountId = $(".js_periodAccountId").val();
                        var accountType = $(".js_periodAccountId").data('weibo_type');
                        var self = this;

                        if (isOnlineRadio == 1) {
                            if (!/^[1-9]{1}[0-9]?$/.test(orderMaxInput)) {
                                W.alert('请输入1-99之间的正整数！');
                                return false;
                            }
                        }

                        var toSetPeriod = function() {
                            var message = W.message("处理中", "loading", 1000);
                            $.ajax({
                                type: "POST",
                                url: "/Media/SocialAccount/setperiod",
                                data: "isOnlineRadio=" + isOnlineRadio + "&selectAllCheckbox=" + selectAllCheckbox + "&orderMaxInput=" + orderMaxInput + "&accountId=" + accountId + "&accountType=" + accountType,
                                dataType: "json",
                                success: function (msg) {
                                    message.close();
                                    if (msg['status'] == false) {
                                        W.alert(msg['message']);
                                    } else {
                                        var alert_msg=msg['message']?msg['message']:"操作成功！";
                                        W.alert(alert_msg, "success");
                                        self.close();
                                        grid.reload();
                                    }
                                }
                            });
                        }
                        
                        if (selectAllCheckbox) {
                            W.confirm("确认对全部账号进行接单上限的设置吗？", function (sure) {
                                if (sure) {
                                    toSetPeriod();
                                }
                            });
                        } else {
                            toSetPeriod();
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
                    var data_account_type = this.getCurrentTarget().attr("data_account_type");
                    var data_orderMax = this.getCurrentTarget().attr("data_orderMax");
                    $(".js_periodAccountId").val(data_account_id).data('weibo_type', data_account_type);
                    console.log(data_radio);
                    if (data_radio == 'false' || data_radio == '') {
                        $(".js_periodRadioFalse").prop('checked', true);
                        $(".js_orderMaxInput").val(null);
                        $(".js_orderMaxDiv").hide();
                    } else {
                        $(".js_orderMaxInput").val(data_orderMax);
                        $(".js_orderMaxDiv").show();
                        $(".js_periodRadioTrue").prop('checked', true);
                    }
                    $("input[name='selectAllPeriodCheckbox']").prop("checked", false);
                }
            }
        });

        $(document).on('click', '.js_periodRadioTrue', function () {
            $(".js_orderMaxDiv").show();
        });
        $(document).on('click', '.js_periodRadioFalse', function () {
            $(".js_orderMaxDiv").hide();
        });
    }




    function initIsAutoSendTips() {
        var tips = new W.Tips({
            group: 'edit',
            target: ".js_isAutoSendDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "订单托管",
            html:
                '<div class="policy_modifications">\
                    <table border="0" cellspacing="0" cellpadding="0">\
                        <tr>\
                            <td>\
                                <input class="js_extendAccountId" type="text" style="display:none" value="" />\
                                <label class="policy_modifications_yes js_appendExtend">\
                                    <input type="radio" name="isExtendRadio" class="js_extendRadioTrue" value="1"/>\
                                    <span>是</span>\
                                </label>\
                                <label>\
                                    <input type="radio" class="js_extendRadioFalse" name="isExtendRadio" value="0"/>\
                                    <span>否</span>\
                                </label>\
                            </td>\
                        </tr>\
                        <tr class="js_tips">\
                            <td>\
                                <em class="warningColor">温馨提示：\
                                <br/>1）订单托管只支持新浪微博和腾讯微博的活动；\
                                <br/>2）当设置为“是”时，以下类型活动将无法派给您：博主自拟、特殊活动、分享类活动，以及新浪直发多图。</em>\
                            </td>\
                        </tr>\
                        <tr>\
                            <td class="modifications_select_all">\
                                <label>\
                                    <input type="checkbox"  name="selectAllExtendCheckbox" />\
                                    <span>本次操作应用于所有账号。</span>\
                                </label>\
                            </td>\
                        </tr>\
                    </table>\
                </div>',
            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var isExtendRadio = $('input[name=isExtendRadio]:checked').val();
                        var selectAllCheckbox = $('input[name=selectAllExtendCheckbox]').attr('checked');
                        var accountId = $(".js_extendAccountId").val();
                        var self = this;
                        if (selectAllCheckbox) {
                            W.confirm("确认对全部账号进行订单托管的设置吗？", function (sure) {
                                if (!sure) {
                                    return false;
                                } else {
                                    var message = W.message("处理中", "loading", 1000);
                                    $.ajax({
                                        type: "POST",
                                        url: "/information/accountmanage/setisautosend",
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
                                url: "/information/accountmanage/setisautosend",
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
                        this.el.find('.js_tips').hide();
                    } else {
                        $(".js_extendRadioTrue").prop('checked', true);
                        this.el.find('.js_tips').show();
                    }
                    $("input[name='selectAllExtendCheckbox']").attr("checked", false);
                }
            }
        });

        tips.el.on('change', ':radio', function() {
            if ($(this).val() === '1') {
                tips.el.find('.js_tips').show();
            } else {
                tips.el.find('.js_tips').hide();
            }
        });
    }

    function initIsAuthDialogue() {
        new W.Tips({
            group: 'edit',
            target: ".js_isAuthDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            title: "账号授权",
            type: "click",
            html: '<p id="authtips">该账号授权已过期，请点击 <a href="#" target="_blank">这里</a>进入账号授权页面重新进行订单授权。</p>',
            listeners: {
                ontargetchanged: function () {

                    var data_auth = this.getCurrentTarget().attr("data_auth");
                    var data_account_id = this.getCurrentTarget().attr("data_account_id");
                    var tips = '该账号授权已过期，';
                    if (data_auth == false || data_auth == 2) {
                        tips = '该账号尚未授权，';
                    }else if(data_auth == 3) {
                        tips = '该账号授权即将过期，';
                    }
                    var authUrl = '/information/accountmanage/auth?account_id='+data_account_id;
                    $('#authtips').html(tips+'请点击 <a href="'+authUrl+'" target="_blank">这里</a>进入账号授权页面重新进行订单授权。');
                }
            }
        });
    }

    function initIsLeaveTips() {

        var tips = new W.Tips({
            group: 'edit',
            target: "#leaveDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "是否暂离",
            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var self = this;
                        var $leave = $("input[name='leave']:checked").val();
                        // var $type = $("input[name='type']:checked").val();
                        // if ($leave == 1) {
                            // if (!$type) {
                                // W.alert("暂离类型不能为空，请选择");
                                // return false;
                            // }
                            // switch ($type) {
                                // case "every_day":
                                    // var $lefttimehours = $("#every_day_lefttimehours").val();
                                    // var $lefttimemin = $("#every_day_lefttimemin").val();
                                    // var $backtimehours = $("#every_day_backtimehours").val();
                                    // var $backtimemin = $("#every_day_backtimemin").val();
                                    // if ($lefttimehours == $backtimehours && $lefttimemin == $backtimemin) {
                                        // W.alert('离开日期不能与返回日期相同');
                                        // return false;
                                    // }

                                    // if ($lefttimehours > $backtimehours) {
                                        // W.alert('返回日期不能早于离开日期');
                                        // return false;
                                    // }

                                    // if ($lefttimehours >= $backtimehours && $lefttimemin > $backtimemin) {
                                        // W.alert('返回日期不能早于离开日期');
                                        // return false;
                                    // }
                                    // break;
                                // case "every_week":
                                    // var $num = $("input[name='weeks[]']:checked").length;
                                    // if (!$num) {
                                        // W.alert("星期是必选项，不能为空！");
                                        // return false;
                                    // }
                                    // var $lefttimehours = $("#every_week_lefttimehours").val();
                                    // var $lefttimemin = $("#every_week_lefttimemin").val();
                                    // var $backtimehours = $("#every_week_backtimehours").val();
                                    // var $backtimemin = $("#every_week_backtimemin").val();
                                    // if ($lefttimehours == $backtimehours && $lefttimemin == $backtimemin) {
                                        // W.alert('离开日期不能与返回日期相同');
                                        // return false;
                                    // }

                                    // if ($lefttimehours > $backtimehours) {
                                        // W.alert('返回日期不能早于离开日期');
                                        // return false;
                                    // }

                                    // if ($lefttimehours >= $backtimehours && $lefttimemin > $backtimemin) {
                                        // W.alert('返回日期不能早于离开日期');
                                        // return false;
                                    // }
                                    // break;
                                // case "other":
                                    // var backTime = $.trim($('#backTime').val());
                                    // var leftTime = $.trim($('#leftTime').val());
                                    // if (backTime == '' || leftTime == '') {
                                        // W.alert('离开日期和返回日期不能为空');
                                        // return false;
                                    // }
                                    // if (backTime == leftTime) {
                                        // W.alert('离开日期不能与返回日期相同');
                                        // return false;
                                    // }

                                    // var bTime = W.util.parseDate(backTime);
                                    // var lTime = W.util.parseDate(leftTime);
                                    // if (bTime < lTime) {
                                        // W.alert('返回日期不能早于离开日期');
                                        // return false;
                                    // }


                                    // if ((bTime.getTime() - lTime.getTime()) > 180 * 24 * 3600 * 1000) {
                                        // W.alert('暂离时间最大为180天，请重新设置');
                                        // return false;
                                    // }
                                    // break;
                                // default:
                                    // W.alert("暂离类型有误，请重新选择！");
                                    // return false;
                                    // break;
                            // }
                        // }

                        var processall = $("input[name='processall']").attr("checked");
                        if ('undefined' == typeof(processall)) {
                            processall = $("input[name='processall']").prop("checked");
                            processall = processall ? 1 : 0;
                        }
                        var toSetLeave = function (){
                            var message = W.message("处理中", "loading", 1000);
                            return $.ajax({
                                url: "/Media/SocialAccount/setleave",
                                data: $("#leaveform").serializeArray(),
                                dataType: "json",
                                type: "post",
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
                                    message.close();
                                    W.alert("服务器有误，请联系管理员！");
                                }
                            });
                        }
                        if (processall) {
                            W.confirm("确认对全部账号进行暂离的设置吗？", function (sure) {
                                if (sure) {
                                    toSetLeave();
                                }
                            });
                        } else {
                            toSetLeave();
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
                    var id = this.getCurrentTarget().attr("data_account_id");
                    tips.setLoader({
                        // url: "/information/accountmanage/leavedetails",
                        url: "/Media/SocialAccount/leavedetails",
                        success: function (data) {
                            this.setHtml(data);
                        },
                        data: {
                            accountId: id,
                            accountType: type
                        }
                    });
                }
            }
        });
    }

    /**
     * 好评
     */

    function initFavorableTips() {

        var tips = new W.Tips({
            group: 'edit',
            target: ".js_haopinglv",
            autoHide: 0,
            width: 170,
            autoHide: false,
            type: "click",
            floor: "high",
            title: "好评详情",
            listeners: {
                ontargetchanged: function () {
                    var id = this.getCurrentTarget().attr("data_account_id");

                    tips.setLoader({
                        url: "/information/accountmanage/goodrating",
                        data: {
                            accountId: id
                        },
                        success: function (data) {
                            this.setHtml(data);
                            // js
                        }
                    });
                }
            }
        });
    }

    function initShowOnlineTips() {
        var showOnline = new W.Tips({
            autoHide: false,
            group: 'edit',
            target: "#js_show_online",
            type: "click",
            title: "账号下架原因",
            height: 'auto',
            listeners: {
                ontargetchanged: function () {
                    var id = this.getCurrentTarget().attr("data-accountId");
                    showOnline.setLoader({
                        url: '/information/accountmanage/online',
                        dataType: "json",
                        data: {
                            account_id: id
                        },
                        success: function (r) {
                            var html = "<ul>";
                            $.each(r || {}, function (k, v) {
                                html += "<li>" + v + "</li>";
                            });
                            html += "</ul>";
                            this.setHtml(html);
                        }
                    });
                }
            }
        });

        $(document).on("click", "#setOpenClick", function () {
            var id = showOnline.getCurrentTarget().parents("tr").attr("data-rowid");
            var row = grid.getRow(id);
            detail.detail(row);
        });
    }

    /**
     * 不可接单原因
     */

    function initNotAllowedTips() {
        var tips = new W.Tips({
            autoHide: false,
            group: 'edit',
            target: "#js_show_allow_order",
            title: "账号不可接单原因",
            type: "click",
            height: 'auto',
            listeners: {
                ontargetchanged: function () {
                    var id = this.getCurrentTarget().attr("data-accountId");
                    var type = this.getCurrentTarget().attr("data-accountType");
                    tips.setLoader({
                        url: '/Media/SocialAccount/notallow',
                        dataType: "json",
                        data: {
                            account_id: id,
                            account_type: type
                        },
                        success: function (r) {
                            var datas = (r && r.data) ? r.data : {};
                            var html = "<ul>";
                            $.each(datas || {}, function (k, v) {
                                html += "<li>" + v + "</li>";
                            });
                            html += "</ul>";
                            this.setHtml(html);
                        }
                    });
                }
            }
        });
    }

    /**
     * 审核通过
     */

    function initReviewTips() {
        var t = new W.Tips({
            autoHide: false,
            group: 'edit',
            target: "#js_show_review",
            type: "click",
            title: "详情",
            cls: "breakword",
            height: 'auto',
            listeners: {
                ontargetchanged: function () {
                    var id = this.getCurrentTarget().attr("data-accountId");
                    var type = this.getCurrentTarget().attr("data-accountType");
                    t.setLoader({
                        url: '/Media/SocialAccount/review',
                        dataType: "json",
                        data: {
                            account_id: id,
                            account_type: type
                        },
                        success: function (r) {
                            var html = "<ul>";
                            $.each(r || {}, function (k, v) {
                                html += "<li>" + W.util.encodeHtmlChar(v) + "</li>";
                            });
                            html += "</ul>";
                            t.setHtml(html);
                        }
                    });
                }
            }
        });
    }


    function initSimpleTips() {
        function createTips(tips) {
            $.each(tips, function (i, e) {
                var tips = new W.Tips({
                    autoHide: false,
                    width: 300,
                    type: "click",
                    floor: e.floor || "middle",
                    title: e.title,
                    group: 'header',
                    offset: {
                        y: 5
                    },
                    target: e.target,
                    text: e.text,
                    html: e.html
                });
            });
        }

        var tips = [
            {
                target: ".js_rating_score",
                title: "信用等级",
                html: "信用等级是指成功完成订单之后，客户从多方面对账号完成这次订单的情况做出“好评、中评和差评”三种评价（若客户未在订单结束后的7天内做出评价，系统会默认给出好评），评价产生的累计信用积分将对应于相应信用等级（<a href='/auth/index/help/type/8/opt/5#type8opt5' target='_blank'>点击查看《信用等级表》</a>）。"
            },
            {
                target: "#haopinglv",
                title: "好评率",
                floor: 'high',
                text: "好评率是指成功完成订单后，当前账号收到来自客户的所有好评数与历史总评价数的百分比。"
            },
            {
                target: "#fensirenzheng",
                title: "粉丝认证",
                floor: 'high',
                text: "“是否粉丝认证”是指当前账号是否已通过媒介经理进行远程视频认证，确定粉丝数的真实性。1）每次更新粉丝截图，媒介经理均远程视频认证；2）视频认证后的账号，客户会优先选择。"
            },
            {
                target: "#weixin_is_allow_dantuwen",
                title: "是否接‘单图文’",
                floor: 'high',
                text: "微信平台订单分为“单图文”、“双图文第一条”和“双图文第二条”三种。当“是否接‘单图文’”设置为否时，该账号将不会接到“单图文”的订单。"
            },
            {
                target: "#weixin_gender_distribution",
                title: "性别分布",
                floor: 'high',
                text: "性别分布指微信公众账号的粉丝中，男、女或其他所占比例。"
            },
            {
                target: "#weixin_gender_distribution_authenticate",
                title: "性别分布认证",
                floor: 'high',
                text: "“性别分布认证”是指当前账号是否已通过媒介经理进行远程视频认证，确定性别分布的真实性。1）每次更新媒介经理均远程视频认证；2）视频认证后的账号，客户会优先选择。"
            },
            {
                target: "#weixin_open_rates",
                title: "打开率",
                floor: 'high',
                text: "打开率指最近七天内，平均每个粉丝每天访问图文页面的比率。计算公式为：（近7天的“图文页阅读人数”÷账号粉丝数）÷ 7"
            },
            {
                target: "#weixin_open_authenticate",
                title: "打开率认证",
                floor: 'high',
                text: "“打开率认证”是指当前账号是否已通过媒介经理进行远程视频认证，确定打开率的真实性。1）每次更新媒介经理均远程视频认证；2）视频认证后的账号，客户会优先选择。"
            },
            {
                target: "#singleGraphicPrice",
                title: "单图文报价",
                text: "单图文价格：指账号以“单图文消息”形式发布订单内容所获得的收益。"
            },
            {
                target: "#multiGraphicTopPrice",
                title: "单图文第一条报价",
                text: "多图文第一条价格：指账号以“多图文消息”形式，在第一条位置发布订单内容所获得的收益。"
            },
            {
                target: "#multiGraphicSecondPrice",
                title: "单图文第二条报价",
                text: "指账号以“多图文消息”形式，在第二条位置发布订单内容所获得的收益。"
            },

            {
                target: "#multiGraphicOtherPrice",
                title: "多图文其他位置价格",
                text: "指账号以“多图文消息”形式，在第三条及以后位置发布订单内容所获得的收益。"
            },

            {
                target: "#softTweetPriceHover",
                title: "软广直发价",
                text: "是指当前账号发布1条指定的软广内容所获得的收益。"
            },
            {
                target: "#daihaojia",
                title: "带号价",
                text: "带号价是指当前账号转发一条指定内容为客户账号带粉所获得的收益。"
            },
            {
                target: "#shifoushangjia",
                title: "是否上架",
                text: "是否上架是指当前账号是否可在“微播易企业客户推广平台”的账号列表中被查看或被搜索到。"
            },
            {
                target: "#zanbujiedan",
                title: "暂不接单",
                text: "暂不接单是指当前账号可以设置为在一段时期内不可接单。若显示为“是”，点击“详情”可查看不可接单的时段；若显示为“否”，则表示当前均可接单。"

            },
            {
                target: "#order_auto_send",
                title: "订单托管",
                html: '订单托管，是指用户将订单执行委托给微播易平台，当客户派单后，托管功能会自动执行完成订单，用户设置托管功能后无需进行任何操作！<br/>\
                <em class="warningColor">温馨提示：</em><br/>\
                <em class="warningColor">1）订单托管只支持新浪微博和腾讯微博的活动；</em><br/>\
                <em class="warningColor">2）当设置为“是”时，以下类型活动将无法派给您：博主自拟、特殊活动、分享类活动，以及新浪直发多图。</em>'

            },
            {
                target: "#shifouyingguang",
                title: "是否硬广",
                text: "是否硬广是指当前账号是否能够接到来自客户派发的硬广订单，若显示为“是”，则表示接硬广订单；若显示为“否”，则表示不可接硬广订单。"
            },
            {
                target: "#jiedanshangxian",
                title: "接单上限",
                text: "接单上限是指当前账号每天可接订单的最大数量。若显示为“数值”，则表示该账号每天最多可接该数值的订单；若显示为“否”，则表示该账号每天接单没有数量限制。"
            },
            {
                target: "#isEnableMicroTask",
                title: "订单是否可带链接",
                html: "用户可以给每个账号设置是否可以接带链接的订单，选项包括“是”、“否”和“特殊活动”。<br/>--&nbsp;选择“是”，则当前账号能接收到所有订单（包括带链接或不带链接）；<br/>--&nbsp;选择“否”，则当前账号仅能接收到不带链接的订单；"
            },
            {
                target: "#d_zhuanpingzhi",
                floor: 'high',
                title: "转评值",
                text: "采集账号的历史转发和评论记录，在剔除异常数据后除以总粉丝数得到的历史平均转发评论值，转评值的范围是1-100，数字越高，表示平均转发率越高；该值不等同于购买力、点击力。"
            },
            {
                target: "#d_yuehegelv",
                floor: 'high',
                title: "月合格率",
                text: "月合格率是指近一个月内，在当前账号完成的所有订单中，合格订单数量除以所有已完成订单的数量而得出的比率。注：近一个月指当天凌晨0:00往前推算30天。"
            },
            {
                target: "#d_yuejudanlv",
                floor: 'high',
                title: "月拒单率",
                text: "月拒单率是指当前账号在最近一个月内的拒单数量除以订单总数量得出的比率。注：1.近一个月指当天凌晨0:00往前推算30天；2.订单总数量指包括流单、拒单和已完成在内的订单数量总和。"
            },
            {
                target: "#d_yueliudanlv",
                floor: 'high',
                title: "月流单率",
                text: "月流单率是指当前账号在最近一个月内的流单数量除以订单总数量得出的比率。注：1.近一个月指当天凌晨0:00往前推算30天；2.订单总数量指包括流单、拒单和已完成在内的订单数量总和。"
            },
            {
                target: "#d_shifougongkai",
                floor: 'high',
                title: "是否公开",
                text: "是否公开是指当前账号是否能在“微播易企业客户推广平台”中被展示或被搜索到。若显示为“是”，则该账号能被展示或被搜索；若显示为“否”，则不能。"
            },
            {
                target: "#d_beishoucangshu",
                floor: 'high',
                title: "被收藏数",
                text: "被收藏数反映了客户在“微播易企业平台”中主动关注优质账号的情况；通常来说，被收藏的数量越多，说明越多的客户/厂商已经关注您的账号，因此您接到的订单机会将增多。"
            },
            {
                target: "#d_beilaheishu",
                floor: 'high',
                title: "被加黑名单数",
                text: "被加黑名单数反映了当前账号在“微播易企业投放平台”中被多少位客户/厂商加入到黑名单。通常情况下，被加黑名单就意味着客户/厂商对该账号的拒单率、流单率、投放效果等指标不满意，而账号一旦被客户/厂商拉黑，则在派单时将不会再看到该账号，这会直接影响到账号今后的接单数量。"
            }
        ];

        createTips(tips);
    }


    /**
     * 订单是否可带链接
     */
    function initIsEnableMicroTaskTips() {
        new W.Tips({
            group: 'header',
            target: ".js_isEnableMicroTask",
            id: "openTip",
            floor: 'high',
            width: 300,
            autoHide: false,
            type: "click",
            title: "订单是否可带链接",
            bbar: [
                {
                    text: "确定",
                    handler: function () {
                        var iAccountId = this.getCurrentTarget().closest('tr').attr('data-rowid');
                        var iAccountType = this.getCurrentTarget().closest('tr').attr('data-rowtype');
                        var isEnableMicroTask = this.el.find('input[name=dataRadio]:checked').val();
                        var isSelectAll = (this.el.find('input[name=selectAllData]:checked').length === 1);

                        var confirmText = isSelectAll ? '亲！确定要将全部账号应用此项设置吗？' : '亲！确定要将账号应用此项设置吗？';

                        W.confirm(confirmText, function (cflag) {
                            if (cflag) {
                                var message = W.message("处理中", "loading", 1000);
                                return $.ajax({
                                    type: "POST",
                                    url: "/Media/SocialAccount/setisenablemicrotask",
                                    data: {
                                        accountId : iAccountId,
                                        accountType : iAccountType,
                                        isEnableMicroTask : isEnableMicroTask,
                                        isSelectAll : isSelectAll
                                    },
                                    dataType: "json",
                                    success: function (msg) {
                                        message.close();
                                        location.reload();
                                    }
                                });
                            } else {
                                return false;
                            }
                        });
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
                    var iAccountId = this.getCurrentTarget().closest('tr').attr('data-rowid');
                    var row = grid.getRow(iAccountId);
                    this.setHtml(doT.template($('#tmplIsEnableMicroTask').html())(row.cells));
                }
            }
        });
    }

    //动态设置需要展示的tab
    var displayItems = [0];
    var fansOpen = 0;
    var trendsOpen = 0;
    var flag = 0;
    //getchecktips();

    function getchecktips() {
        var checkTips = '/information/accountmanage/checktips';
        $.ajax({
            "url": checkTips,
            'type': 'post',
            success: function (data) {
                if (data['result'] == "succ") {
                    fansOpen = data['fansOpen'];
                    trendsOpen = data['trendsOpen'];
                    flag = 1;
                }
            },
            error: function () {

            }
        });
    }

    return {
        initGrid: initGrid,
        init: function () {
            initSimpleTips();
            initIsOpenTips();
            initWeixinIsAllowDantuwenTips();
            initCeilingTips();
            // initIsHardAdTips(grid);
            initIsAutoSendTips();
            initIsLeaveTips();
            initFavorableTips();
            initShowOnlineTips();
            initNotAllowedTips();
            initReviewTips();
            initIsEnableMicroTaskTips();
            getchecktips();
            initIsAuthDialogue();
        },
        getGrid: function() {
            return grid;
        }
    }

});