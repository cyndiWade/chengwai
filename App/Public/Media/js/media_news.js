define(function(require, exports) {
    var detail = require('./detail.js');

    var weibo_type = 4;

    var grid;

    new W.Tips({
        autoHide: false,
        group: 'edit',
        target: "a[tag=js_tip]",
        type: "click",
        title: "新闻媒体",
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
            var name = (value > parseFloat(row.cells.money)) ? '提高' : '降低';
            var message = name + '价格会影响到您的账号接单哦。确定要' + name + '价格吗？';
            W.confirm(message,function(sure){
                if (sure) {
                    $.ajax({
                        type:'GET',
                        url:'/Media/SocialAccount/updateprice',
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
                    });
                    result.resolve();
                } else {
                    result.reject();
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
            var tpl = '<div class="pr"><em class="emqa" id="{1}" title="点击查看详情"></em>{0}</div>';
            return W.util.formatStr(tpl, title, id);
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
                    width: 200,
                    dataIndex: "weibo_name",
                    // 使用模板前对数据预处理
                    tmplPreprocessor: tmplColWeiboNamePreprocessor,
                    tmplId: 'tmplColWeiboName'
                },
                // {
                    // align: "left",
                    // text: "粉丝数",
                    // cls: "t2",
                    // dataIndex: "followers_count",
                    // formatter: formatters.followers_count
                // },
                {
                    align: "left",
                    text: renderTitle("价格", "price"),
                    dataIndex: "money",
                    cls: "t3",
                    editable: 1,
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
                    text: "审核通过",
                    dataIndex: "is_verify",
                    cls: "t1",
                    formatter: function (data, row) {
                        if (data == "0" || data == '2') {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_review' class='blue' data-accountId = '" + row.cells.account_id + "' data-accountType = '" + row.cells.weibo_type + "'></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");
                            return div.append("否&nbsp;").append(a);
                        } else {
                            return '是';
                        }
                    }
                },
               /* {
                    align: "left",
                    text: "接单状态",
                    cls: "t1",
                    dataIndex: "is_allow_order",
                    formatter: function (data, row) {
                        var div = $("<div></div>");
                        if (row.cells.is_verify == '2' || row.cells.is_verify == '0') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var a = $("<a id='js_show_allow_order' class='blue' data-accountId = '" + row.cells.account_id + "' data-accountType = '" + row.cells.weibo_type + "'></a>");

                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append(a);
                        }
                    }
                },*/
                {
                    align: "left",
                    text: renderTitle("是否上架", "shifoushangjia"),
                    dataIndex: "is_online",
                    cls: "t1",
                    formatter: function (data, row) {
                        if (row.cells.is_verify == '2' || row.cells.is_allow_order == '2') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_online' class='blue' data-accountId = '" + row.cells.account_id + "' data-accountType = '" + row.cells.weibo_type + "'></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append('<br/>').append(a);

                        }
                    }
                },
                {
                    align: "center",
                    cls: "t1",
                    text: renderTitle("暂不接单", "zanbujiedan"),
                    dataIndex: "leave",
                    formatter: function (data, row) {
                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";

                        if (row.cells.is_verify == 1) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "'id='leaveDialogue' src='/resources/images/ico_set.gif' class='set_img' /></a>";
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "' data_account_type = '" + weiboType + "' id='leaveDialogue'></a>";
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
                    text: '<span class="js_tour_detail" style="margin:auto">详情</span>',
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
                    var type = this.getCurrentTarget().attr("data_account_type");
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
                target: "#price",
                title: "价格",
                floor: 'high',
                text: "价格是指当前账号发一条指定内容为客户账号带粉所获得的收益。"
            },
            {
                target: "#daihaojia",
                title: "带号价",
                text: "带号价是指当前账号转发一条指定内容为客户账号带粉所获得的收益。"
            },
            {
                target: "#shifoushangjia",
                title: "是否上架",
                text: "是否上架是指当前账号是否可在“城外圈企业客户推广平台”的账号列表中被查看或被搜索到。"
            },
            {
                target: "#zanbujiedan",
                title: "暂不接单",
                text: "暂不接单是指当前账号可以设置为在一段时期内不可接单。若显示为“是”，点击“详情”可查看不可接单的时段；若显示为“否”，则表示当前均可接单。"

            },
            {
                target: "#order_auto_send",
                title: "订单托管",
                html: '订单托管，是指用户将订单执行委托给城外圈平台，当客户派单后，托管功能会自动执行完成订单，用户设置托管功能后无需进行任何操作！<br/>\
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
                text: "是否公开是指当前账号是否能在“城外圈企业客户推广平台”中被展示或被搜索到。若显示为“是”，则该账号能被展示或被搜索；若显示为“否”，则不能。"
            },
            {
                target: "#d_beishoucangshu",
                floor: 'high',
                title: "被收藏数",
                text: "被收藏数反映了客户在“城外圈企业平台”中主动关注优质账号的情况；通常来说，被收藏的数量越多，说明越多的客户/厂商已经关注您的账号，因此您接到的订单机会将增多。"
            },
            {
                target: "#d_beilaheishu",
                floor: 'high',
                title: "被加黑名单数",
                text: "被加黑名单数反映了当前账号在“城外圈企业投放平台”中被多少位客户/厂商加入到黑名单。通常情况下，被加黑名单就意味着客户/厂商对该账号的拒单率、流单率、投放效果等指标不满意，而账号一旦被客户/厂商拉黑，则在派单时将不会再看到该账号，这会直接影响到账号今后的接单数量。"
            }
        ];

        createTips(tips);
    }

    return {
        initGrid: initGrid,
        init: function () {
            initSimpleTips();
            initCeilingTips();
            // initIsHardAdTips(grid);
            initIsLeaveTips();
            initNotAllowedTips();
            initReviewTips();
        },
        getGrid: function() {
            return grid;
        }
    }

});