define(function(require, exports) {
    // var detail = require('./detail.js');

    // var weibo_type_weixin = 9;
    var weibo_type_weixin = 3;
    var weibo_type_weitao = 17;
    var weibo_type_qzone = 5;
    var soft_price_to_hard_rate = 7;
    var content_price_to_tweet_price_rate = 45;
    var soft_price_to_hard_rate_base = 10;
    var content_price_to_tweet_price_rate_base = 100;
    var no_compute_hard_price_boundary = 2.5;
    var price_default_value = 99999999.99;
    var account_level_normal = 1;//普通账号 = 1,重点账号 > 1

    function substrName(str, start, len) {
        if (str.length > len) {
            return str.substr(start, len) + '...';
        }
        return str;
    }

    /**
     * 检测是否符合比率
     * @param num_from
     * @param num_to
     * @param rate
     * @returns {boolean}
     */
    function ifCheckRate(num_from, num_to, rate) {
        var _num_from = Number(num_from);
        var _num_to = Number(num_to);
        if (_num_from * rate >= _num_to) {
            return true;
        }
        return false;
    }

    /**
     * 精确到小数点后一位，只舍不入
     * @param num
     * @returns {string}
     */
    function getFormat(num){
        return (num + '').replace(/^(\d+\.\d)(\d*)/, function(a, b) {return b;})
    }

    /**
     * 依据级别判断是否返回涨价提示
     * 重点账号，只能降价
     * @param new_price
     * @param old_price
     * @param level
     * @param weibo_type
     */
    function checkPriceByLevel(new_price,old_price,level,weibo_type)
    {
        var msg = '';
        if (Number(level) == account_level_normal && Number(weibo_type) != weibo_type_weixin){

        }else{
            if (Number(new_price) > Number(old_price))
            {
                msg = '当前账号无法上调价格，如需更多帮助，请联系媒介经理！';
            }
        }
        return msg;
    }


    function returnMax(price)
    {
        if (Number(price) < 1){
            return 1;
        }else{
            return price;
        }
    }

    /**
     * 检测更新时的各比率约束
     * @param rowCells
     * @param price
     * @param type
     * @return string
     */
    function checkPrice(rowCells,price,type)
    {
        var msg = '';
        var retweetPrice = Number(rowCells.retweet_price);
        var tweetPrice = Number(rowCells.tweet_price);
        var softRetweetPrice = Number(rowCells.soft_retweet_price);
        var softTweetPrice = Number(rowCells.soft_tweet_price);
        var contentPrice = Number(rowCells.content_price);
        var weiboType = Number(rowCells.weibo_type);
        switch (type){
            case 'retweetPrice':
                if (price == retweetPrice){
                    return '';
                }

                if (weiboType == weibo_type_qzone){
                    if (price < softRetweetPrice)
                    {
                        msg = '修改后软广直发价会变为 '+ price +'，确定继续吗？';
                    }
                }else{
                    var softRetweetPrice_to = price * soft_price_to_hard_rate / soft_price_to_hard_rate_base;
                    var contentPrice_to = price * content_price_to_tweet_price_rate / content_price_to_tweet_price_rate_base;

                    if (softRetweetPrice_to < softRetweetPrice && contentPrice_to >= contentPrice)
                    {
                        msg = '亲，由于软广转发价只能是硬广转发价的70%，因此，若您将硬广转发价改为'+ price +'元，软广转发价会自动变为'+ returnMax(getFormat(softRetweetPrice_to)) +'元，您确定要修改吗？'
                    }else if(softRetweetPrice_to >= softRetweetPrice && contentPrice_to < contentPrice)
                    {
                        msg = '亲，由于带号价只能是硬广转发价的45%，因此，若您将硬广价改为'+ price +'元，带号价会自动变为'+ returnMax(getFormat(contentPrice_to)) +'元，您确定要修改吗？';
                    }else if(softRetweetPrice_to < softRetweetPrice && contentPrice_to < contentPrice)
                    {
                        msg = '亲，由于软广转发价只能是硬广转发价的70%，带号价只能是硬广转发价的45%，因此，若您将硬广转发价改为'+ price +'元，软广转发价会自动变为'+ returnMax(getFormat(softRetweetPrice_to)) +'元，带号价会自动变为'+ returnMax(getFormat(contentPrice_to)) +'元，您确定要修改吗？';
                    }
                }

                break;

            case 'tweetPrice':
                if(weiboType==weibo_type_weitao || weiboType==weibo_type_weixin){
                    var softTweetPrice_to = price * soft_price_to_hard_rate / soft_price_to_hard_rate_base;
                    var contentPrice_to = price * content_price_to_tweet_price_rate / content_price_to_tweet_price_rate_base;

                    if (softTweetPrice_to < softTweetPrice && contentPrice_to >= contentPrice)
                    {
                        msg = '亲，由于软广直发价只能是硬广直发价的70%，因此，若您将硬广直发价改为'+ price +'元，软广直发价会自动变为'+ returnMax(getFormat(softTweetPrice_to)) +'元，您确定要修改吗？'
                    }else if(softTweetPrice_to >= softTweetPrice && contentPrice_to < contentPrice)
                    {
                        msg = '亲，由于带号价只能是硬广直发价的45%，因此，若您将硬广价改为'+ price +'元，带号价会自动变为'+ returnMax(getFormat(contentPrice_to)) +'元，您确定要修改吗？';
                    }else if(softTweetPrice_to < softTweetPrice && contentPrice_to < contentPrice)
                    {
                        msg = '亲，由于软广直发价只能是硬广直发价的70%，带号价只能是硬广直发价的45%，因此，若您将硬广直发价改为'+ price +'元，软广直发价会自动变为'+ returnMax(getFormat(softTweetPrice_to)) +'元，带号价会自动变为'+ returnMax(getFormat(contentPrice_to)) +'元，您确定要修改吗？';
                    }
                }else{
                    if (price == tweetPrice){
                        return '';
                    }

                    if (weiboType == weibo_type_qzone){
                        if (price < softTweetPrice){
                            msg = '修改后软广直发价会变为 '+ price +'，确定继续吗？';
                        }
                    }else{
                        var softTweetPrice_to = price * soft_price_to_hard_rate / soft_price_to_hard_rate_base;
                        if (softTweetPrice_to < softTweetPrice){
                            msg = '亲，由于软广直发价只能是硬广直发价的70%，因此，若您将硬广直发价改为'+ price +'元，软广直发价会自动变为'+ returnMax(getFormat(softTweetPrice_to)) +'元，您确定要修改吗？';
                        }
                    }
                }


                break;

            case 'softRetweetPrice':
                if (price == softRetweetPrice){
                    return '';
                }
                var softRetweetPrice_to = retweetPrice * soft_price_to_hard_rate / soft_price_to_hard_rate_base;
                if (softRetweetPrice_to < price)
                {
                    msg = '亲，由于软广转发价只能是硬广转发价的70%，请重新修改价格！';
                }
                break;

            case 'softTweetPrice':
                if (price == softTweetPrice){
                    return '';
                }
                var softTweetPrice_to = tweetPrice * soft_price_to_hard_rate / soft_price_to_hard_rate_base;
                if (softTweetPrice_to < price)
                {
                    msg = '亲，由于软广直发价只能是硬广直发价的70%，请重新修改价格！';
                }
                break;

            case 'contentPrice':
                if (price == contentPrice){
                    return '';
                }
                //如果是微信平台,按照直发价格计算
                var contentPrice_to = retweetPrice * content_price_to_tweet_price_rate / content_price_to_tweet_price_rate_base;
                if(weiboType==weibo_type_weitao || weiboType == weibo_type_weixin){
                    contentPrice_to = tweetPrice * content_price_to_tweet_price_rate / content_price_to_tweet_price_rate_base;
                }

                if (contentPrice_to < price)
                {
                    msg = '亲，由于带号价只能是硬广转发价的45%，请重新修改价格！';
                    if(weiboType==weibo_type_weitao || weiboType == weibo_type_weixin){
                        msg = '亲，由于带号价只能是硬广直发价的45%，请重新修改价格！';
                    }
                }
                break;
        }

        return msg;
    }

    var grid;

    function initGrid(filters,weiboType) {
        var formatters = {
            price: function (data, row) {
                return W.util.encodeHtmlChar($.trim(data)) || "暂无报价";
            },
            detail: function (data, row) {
                var a = $('<a href="javascript:void(0);" class="yel"></a>').html("查看");
                return a.click(function () {
                    detail.detail(row);
                });
            },
            followers_count: function (data, row) {
                return data > 0 ? data : "暂无数据";
            }
        };

        var editHandlers = {
            account_phone: function(valu, textarea, row, col){
                var value = W.util.encodeHtmlChar(valu);
                if(value == null || value == ""){
                    W.alert("不能为空！");
                    return false;
                }
                var filter = /^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|14[5|7])\d{8}$/;
                if(!filter.test(value)){
                    W.alert("请输入正确的手机号！");
                    return false;
                }
                var msgDer = $.Deferred();
                var der = $.Deferred();

                msgDer.done(function() {
                    var msg = "确定要修改手机号么";
                    W.confirm(msg, function (sure) {
                        if (sure) {
                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/changeamount",
                                data: "accountPhone=" + value + "&accountId=" + row.cells.account_id + '&priceType=accountPhone',
                                dataType: "json",
                                success: function (msg) {
                                    var success = msg[0];
                                    W.alert(msg[1], success ? "success" : "info");
                                    if (success) {
                                        grid.reload();
                                    } else {
                                        der.reject();
                                    }
                                },
                                error: function () {
                                    W.alert("修改手机号失败！", "error");
                                    der.reject();
                                }
                            });
                        } else {
                            der.reject();
                        }
                    });
                });

                msgDer.fail(function() {
                    der.reject();
                });
                msgDer.resolve();
                return der;
            },
            /**
             * 修改硬广转发价
             * @param valu
             * @param textarea
             * @param row
             * @param col
             * @returns {*}
             */
            retweetPrice: function (valu, textarea, row, col) {
                var value = W.util.encodeHtmlChar(valu);
                var num = Number(value);
                if (isNaN(num)) {
                    W.alert('请输入合法的价格');
                    return false;
                }
                var flo = num.toFixed(1);
                if (value == Number(row.cells.retweet_price)) {
                    return false;
                } else if (num != flo && value != '') {
                    W.alert('价格精确到小数点后一位');
                    return false;
                } else if (flo < 1) {
                    if(row.cells.weibo_type==23){
                        W.alert('分享报价最少为1元');
                    }else{
                        W.alert('硬广转发价最少为1元');
                    }
                    return false;
                }

                var r = /^(([1-9][0-9]{0,7})(\.[0-9]?0?)?|)$/;
                var grid = this;
                if (r.test(value) === true) {
                    if ((value === '' && row.cells.retweet_price != '') || (value != '' && row.cells.retweet_price === '')) {
                        //无报价变有报价 有报价变无报价不受改价次数限制
                    } else {
                        var level_msg = '';
                        level_msg = checkPriceByLevel(value,parseFloat(row.cells.retweet_price),row.cells.level,row.cells.weibo_type);
                        if (level_msg != '')
                        {
                            W.alert(level_msg);
                            return false;
                        }
                        var name=row.cells.weibo_type==23?'分享报价':'硬广转发价';
                        if (value > parseFloat(row.cells.retweet_price) && row.cells.retweet_price_u < 1) {
                            W.alert('一周内，同一账号的'+name+'只能调高一次！');
                            return false;
                        } else if (value < parseFloat(row.cells.retweet_price) && row.cells.retweet_price_d < 1) {
                            W.alert('当天0:00至24:00，同一账号最多可降价5次'+name+'！');
                            return false;
                        }
                    }

                    var msgDer = $.Deferred();
                    var der = $.Deferred();

                    msgDer.done(function() {
                        var msg = "";
                        var name=row.cells.weibo_type==23?'分享报价':'硬广转发价';
                        if (value === '') {
                            msg = '亲！清空价格会影响到您的账号接单哦。确定清空'+name+'吗？';
                        } else if (value !== '' && row.cells.retweet_price === '') {
                            msg = '亲！确定要修改'+name+'吗？';
                        } else if (value > parseFloat(row.cells.retweet_price)) {
                            msg = '亲！涨价很可能会影响到您的账号接单哦。确定要提高'+name+'吗？（注：未来7天内，该类型的价格不能再涨价）';
                        } else {
                            msg = '亲！确定要修改'+name+'吗？（注：今天还有' + row.cells.retweet_price_d + '次降价机会）';
                        }

                        W.confirm(msg, function (sure) {
                            if (sure) {
                                $.ajax({
                                    type: "POST",
                                    url: "/information/accountmanage/changeamount",
                                    data: "newRetweetPrice=" + value + "&accountId=" + row.cells.account_id + '&priceType=retweetprice',
                                    dataType: "json",
                                    success: function (msg) {
                                        var success = msg[0];
                                        W.alert(msg[1], success ? "success" : "info");
                                        if (success) {
                                            grid.reload();
                                        } else {
                                            der.reject();
                                        }
                                    },
                                    error: function () {
                                        W.alert("修改价格失败！", "error");
                                        der.reject();
                                    }
                                });
                            } else {
                                der.reject();
                            }
                        });
                    });

                    msgDer.fail(function() {
                        der.reject();
                    });

                    var checkResult = '';
                    if(row.cells.weibo_type != 23){
                        checkResult = checkPrice(row.cells,num,'retweetPrice');
                    }
                    if (checkResult != '')
                    {
                        W.confirm(checkResult, function(sure) {
                            if (sure) {
                                msgDer.resolve();
                            } else {
                                msgDer.reject();
                            }

                        });
                    } else {
                        msgDer.resolve();
                    }


                    return der;
                }
                W.alert('请输入小于8位的正数!');
                return false;
            },
            /**
             * 修改硬广直发价
             * @param valu
             * @param textarea
             * @param row
             * @param col
             * @returns {*}
             */
            tweetPrice: function (valu, textarea, row, col) {
                var value = W.util.encodeHtmlChar(valu);
                var num = Number(value);
                if (isNaN(num)) {
                    W.alert('请输入合法的价格');
                    return false;
                }
                var flo = num.toFixed(1);
                if (value == Number(row.cells.tweet_price)) {
                    return false;
                } else if (num != flo && value != '') {
                    W.alert('价格精确到小数点后一位');
                    return false;
                } else if (flo < 1) {
                    if(row.cells.weibo_type==23){
                        W.alert('直发报价最少为1元');
                    }else{
                        W.alert('硬广直发价最少为1元');
                    }
                    return false;
                }

                var r = /^(([1-9][0-9]{0,7})(\.[0-9]?0?)?|)$/;
                if (r.test(value) === true) {
                    if ((value === '' && row.cells.tweet_price != '') || (value != '' && row.cells.tweet_price === '')) {
                        //无报价变有报价 有报价变无报价不受改价次数限制
                    } else {
                        var level_msg = '';
                        level_msg = checkPriceByLevel(value,parseFloat(row.cells.tweet_price),row.cells.level,row.cells.weibo_type);
                        if (level_msg != '')
                        {
                            W.alert(level_msg);
                            return false;
                        }
                        var name=row.cells.weibo_type==23?'直发报价':'硬广直发价';
                        if (value > parseFloat(row.cells.tweet_price) && row.cells.tweet_price_u < 1) {
                            W.alert('一周内，同一账号的'+name+'只能调高一次！');
                            return false;
                        } else if (value < parseFloat(row.cells.tweet_price) && row.cells.tweet_price_d < 1) {
                            W.alert('当天0:00至24:00，同一账号最多可降价5次'+name+'！');
                            return false;
                        }
                    }

                    var msgDer = $.Deferred();
                    var der = $.Deferred();

                    msgDer.done(function() {
                        var msg = "";
                        var name=row.cells.weibo_type==23?'直发报价':'硬广直发价';
                        if (value === '') {
                            msg = '亲！清空价格会影响到您的账号接单哦。确定清空'+name+'吗？';
                        } else if (value !== '' && row.cells.tweet_price === '') {
                            msg = '亲！确定要修改'+name+'吗？';
                        } else if (value > parseFloat(row.cells.tweet_price)) {
                            msg = '亲！涨价很可能会影响到您的账号接单哦。确定要提高'+name+'吗？（注：未来7天内，该类型的价格不能再涨价）';
                        } else {
                            msg = '亲！确定要修改'+name+'吗？（注：今天还有' + row.cells.tweet_price_d + '次降价机会）';
                        }

                        W.confirm(msg, function (sure) {
                            if (sure) {
                                $.ajax({
                                    type: "POST",
                                    url: "/information/accountmanage/changeamount",
                                    data: "newRetweetPrice=" + value + "&accountId=" + row.cells.account_id + '&priceType=tweetprice',
                                    dataType: "json",
                                    success: function (msg) {
                                        var success = msg[0];
                                        W.alert(msg[1], success ? "success" : "info");
                                        if (success) {
                                            grid.reload();
                                        } else {
                                            der.reject();
                                        }
                                    },
                                    error: function () {
                                        W.alert("修改价格失败！", "error");
                                        der.reject();
                                    }
                                });
                            } else {
                                der.reject();
                            }
                        });
                    });

                    msgDer.fail(function() {
                        der.reject();
                    });

                    var checkResult = '';
                    if(row.cells.weibo_type != 23){
                        checkResult = checkPrice(row.cells,num,'tweetPrice');
                    }

                    if (checkResult != '')
                    {
                        W.confirm(checkResult, function(sure) {
                            if (sure) {
                                msgDer.resolve();
                            } else {
                                msgDer.reject();
                            }

                        });
                    } else {
                        msgDer.resolve();
                    }

                    return der;
                }
                W.alert('请输入小于8位的正数!');
                return false;
            },
            /**
             * 修改软广转发价
             * @param valu
             * @param textarea
             * @param row
             * @param col
             * @returns {*}
             */
            softRetweetPrice: function (valu, textarea, row, col) {
                var value = W.util.encodeHtmlChar(valu);
                var num = Number(value);
                if (isNaN(num)) {
                    W.alert('请输入合法的价格');
                    return false;
                }
                var flo = num.toFixed(1);
                if (value == Number(row.cells.soft_retweet_price)) {
                    return false;
                } else if (num != flo && value != '') {
                    W.alert('价格精确到小数点后一位');
                    return false;
                } else if (flo < 1) {
                    W.alert('软广转发价最少为1元');
                    return false;
                }

                var r = /^(([1-9][0-9]{0,7})(\.[0-9]?0?)?|)$/;
                var grid = this;
                if (r.test(value) === true) {
                    if ((value === '' && row.cells.soft_retweet_price != '') || (value != '' && row.cells.soft_retweet_price === '')) {
                        //无报价变有报价 有报价变无报价不受改价次数限制
                    } else {
                        var level_msg = '';
                        level_msg = checkPriceByLevel(value,parseFloat(row.cells.soft_retweet_price),row.cells.level,row.cells.weibo_type);
                        if (level_msg != '')
                        {
                            W.alert(level_msg);
                            return false;
                        }

                        if (value > parseFloat(row.cells.soft_retweet_price) && row.cells.soft_retweet_price_u < 1) {
                            W.alert('一周内，同一账号的软广转发价只能调高一次！');
                            return false;
                        } else if (value < parseFloat(row.cells.soft_retweet_price) && row.cells.soft_retweet_price_d < 1) {
                            W.alert('当天0:00至24:00，同一账号最多可降价5次软广转发价格！');
                            return false;
                        }
                    }

                    var checkResult = '';
                    checkResult = checkPrice(row.cells,num,'softRetweetPrice');
                    if (checkResult != '')
                    {
                        W.alert(checkResult);
                        return false;
                    }

                    var der = $.Deferred();
                    var msg = "";
                    if (value === '') {
                        msg = '亲！清空价格会影响到您的账号接单哦。确定清空软广转发价格吗？';
                    } else if (value !== '' && row.cells.soft_retweet_price === '') {
                        msg = '亲！确定要修改软广转发价格吗？';
                    } else if (value > parseFloat(row.cells.soft_retweet_price)) {
                        msg = '亲！涨价很可能会影响到您的账号接单哦。确定要提高软广转发价格吗？（注：未来7天内，该类型的价格不能再涨价）';
                    } else {
                        msg = '亲！确定要修改软广转发价格吗？（注：今天还有' + row.cells.soft_retweet_price_d + '次降价机会）';
                    }

                    W.confirm(msg, function (sure) {
                        if (sure) {
                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/changeamount",
                                data: "newRetweetPrice=" + value + "&accountId=" + row.cells.account_id + '&priceType=softretweetprice',
                                dataType: "json",
                                success: function (msg) {
                                    var success = msg[0];
                                    W.alert(msg[1], success ? "success" : "info");
                                    if (success) {
                                        grid.reload();
                                    } else {
                                        der.reject();
                                    }
                                },
                                error: function () {
                                    W.alert("修改价格失败！", "error");
                                    der.reject();
                                }
                            });
                        } else {
                            der.reject();
                        }
                    });

                    return der;
                }
                W.alert('请输入小于8位的正数!');
                return false;
            },
            /**
             * 修改软广直发价
             * @param valu
             * @param textarea
             * @param row
             * @param col
             * @returns {*}
             */
            softTweetPrice: function (valu, textarea, row, col) {
                var value = W.util.encodeHtmlChar(valu);
                var num = Number(value);
                if (isNaN(num)) {
                    W.alert('请输入合法的价格');
                    return false;
                }
                var flo = num.toFixed(1);
                if (value == Number(row.cells.soft_tweet_price)) {
                    return false;
                } else if (num != flo && value != '') {
                    W.alert('价格精确到小数点后一位');
                    return false;
                } else if (flo < 1) {
                    W.alert('硬广直发价最少为1元');
                    return false;
                }
                var r = /^(([1-9][0-9]{0,7})(\.[0-9]?0?)?|)$/;
                var grid = this;
                if (r.test(value) === true) {
                    if ((value === '' && row.cells.soft_tweet_price != '') || (value != '' && row.cells.soft_tweet_price === '')) {
                        //无报价变有报价 有报价变无报价不受改价次数限制
                    } else {
                        var level_msg = '';
                        level_msg = checkPriceByLevel(value,parseFloat(row.cells.soft_tweet_price),row.cells.level,row.cells.weibo_type);
                        if (level_msg != '')
                        {
                            W.alert(level_msg);
                            return false;
                        }

                        if (value > parseFloat(row.cells.soft_tweet_price) && row.cells.soft_tweet_price_u < 1) {
                            W.alert('一周内，同一账号的软广直发价只能调高一次！');
                            return false;
                        } else if (value < parseFloat(row.cells.soft_tweet_price) && row.cells.soft_tweet_price_d < 1) {
                            W.alert('当天0:00至24:00，同一账号最多可降价5次软广直发价格！');
                            return false;
                        }
                    }

                    var checkResult = '';
                    checkResult = checkPrice(row.cells,num,'softTweetPrice');
                    if (checkResult != '')
                    {
                        W.alert(checkResult);
                        return false;
                    }

                    var der = $.Deferred();
                    var msg = "";
                    if (value === '') {
                        msg = '亲！清空价格会影响到您的账号接单哦。确定清空软广直发价格吗？';
                    } else if (value !== '' && row.cells.soft_tweet_price === '') {
                        msg = '亲！确定要修改软广直发价格吗？';
                    } else if (value > parseFloat(row.cells.soft_tweet_price)) {
                        msg = '亲！涨价很可能会影响到您的账号接单哦。确定要提高软广直发价格吗？（注：未来7天内，该类型的价格不能再涨价）';
                    } else {
                        msg = '亲！确定要修改软广直发价格吗？（注：今天还有' + row.cells.soft_tweet_price_d + '次降价机会）';
                    }

                    W.confirm(msg, function (sure) {
                        if (sure) {
                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/changeamount",
                                data: "newRetweetPrice=" + value + "&accountId=" + row.cells.account_id + '&priceType=softtweetprice',
                                dataType: "json",
                                success: function (msg) {
                                    var success = msg[0];
                                    W.alert(msg[1], success ? "success" : "info");
                                    if (success) {
                                        grid.reload();
                                    } else {
                                        der.reject();
                                    }
                                },
                                error: function () {
                                    W.alert("修改价格失败！", "error");
                                    der.reject();
                                }
                            });
                        } else {
                            der.reject();
                        }
                    });

                    return der;
                }
                W.alert('请输入小于8位的正数!');
                return false;
            },
            /**
             * 修改改带号价
             * @param valu
             * @param textarea
             * @param row
             * @param col
             * @returns {*}
             */
            contentPrice: function (valu, textarea, row, col) {
                var value = W.util.encodeHtmlChar(valu);
                var num = Number(value);
                if (isNaN(num)) {
                    W.alert('请输入合法的价格');
                    return false;
                }
                var flo = num.toFixed(1);
                if (value == Number(row.cells.content_price)) {
                    return false;
                } else if (num != flo && value != '') {
                    W.alert('价格精确到小数点后一位');
                    return false;
                } else if (flo < 1) {
                    W.alert('带号价最少为1元');
                    return false;
                }
                var r = /^(([1-9][0-9]{0,7})(\.[0-9]?0?)?|)$/;
                if (r.test(value) === true) {
                    if ((value === '' && row.cells.content_price != '') || (value != '' && row.cells.content_price === '')) {
                        //无报价变有报价 有报价变无报价不受改价次数限制
                    } else {
                        var level_msg = '';
                        level_msg = checkPriceByLevel(value,parseFloat(row.cells.content_price),row.cells.level,row.cells.weibo_type);
                        if (level_msg != '')
                        {
                            W.alert(level_msg);
                            return false;
                        }

                        if (value > parseFloat(row.cells.content_price) && row.cells.content_price_u < 1) {
                            W.alert('一周内，同一账号的带号价只能调高一次！');
                            return false;
                        } else if (value < parseFloat(row.cells.content_price) && row.cells.content_price_d < 1) {
                            W.alert('当天0:00至24:00，同一账号最多可降价5次带号价！');
                            return false;
                        }
                    }

                    var checkResult = '';
                    checkResult = checkPrice(row.cells,num,'contentPrice');
                    if (checkResult != '')
                    {
                        W.alert(checkResult);
                        return false;
                    }

                    var der = $.Deferred();
                    var msg = "";
                    if (value === '') {
                        msg = '亲！清空价格会影响到您的账号接单哦。确定清空带号价吗？';
                    } else if (value !== '' && row.cells.content_price === '') {
                        msg = '亲！确定要修改带号价吗？';
                    } else if (value > parseFloat(row.cells.content_price)) {
                        msg = '亲！涨价很可能会影响到您的账号接单哦。确定要提高带号价吗？（注：未来7天内，该类型的价格不能再涨价）';
                    } else {
                        msg = '亲！确定要修改带号价吗？（注：今天还有' + row.cells.content_price_d + '次降价机会）';
                    }

                    W.confirm(msg, function (sure) {
                        if (sure) {
                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/changeamount",
                                data: "newRetweetPrice=" + value + "&accountId=" + row.cells.account_id + '&priceType=contentprice',
                                dataType: "json",
                                success: function (msg) {
                                    var success = msg[0];
                                    W.alert(msg[1], success ? "success" : "info");
                                    if (success) {
                                        grid.reload();
                                    } else {
                                        der.reject();
                                    }
                                },
                                error: function () {
                                    W.alert("修改价格失败！", "error");
                                    der.reject();
                                }
                            });
                        } else {
                            der.reject();
                        }
                    });

                    return der;
                }
                W.alert('请输入小于8位的正数!');
                return false;
            }
        }

        function isPriceEditable(value, row,item_info) {
            if(row.cells.weibo_type == weibo_type_weitao || row.cells.weibo_type == weibo_type_weixin){
                if(item_info.dataIndex =="retweet_price" || item_info.dataIndex=='soft_retweet_price'){
                    return false;
                }
            }
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
        var columns= null;
        if (weiboType == 23) {
            // 朋友圈
            columns = [
                {
                    align: "center",
                    text: "微信名字",
                    dataIndex: "weibo_name",
                    // 使用模板前对数据预处理
                    tmplPreprocessor: tmplColWeiboNamePreprocessor,
                    tmplId: 'tmplColWeiboName'
                },
                {
                    align: "center",
                    text: "微信号",
                    dataIndex: "weibo_id",
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg('账号手机号', "pengyouquanAccountPhoneHover", ""),
                    dataIndex: "account_phone",
                    editHandler: editHandlers.account_phone,
                    editable:true
                },
                {
                    align: "center",
                    text: "好友数",
                    dataIndex: "followers_count",
                    formatter: formatters.followers_count
                },
                {
                    align: "center",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg('直发报价', "pengyouquanTweetPriceHover", ""),
                    dataIndex: "tweet_price",
                    cls: "grid_editableCol",
                    editable: isPriceEditable,
                    width: 52,
                    editHandler: editHandlers.tweetPrice,
                    formatter: formatters.price
                },
                {
                    align: "center",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg("分享报价", "pengyouquanRetweetPriceHover", ''),
                    dataIndex: "retweet_price",
                    cls: "grid_editableCol",
                    editable: isPriceEditable,
                    width: 52,
                    editHandler: editHandlers.retweetPrice,
                    formatter: formatters.price
                },
                {
                    align: "center",
                    text: "周订单",
                    dataIndex: "orders_weekly"

                },
                {
                    align: "center",
                    text: "月订单",
                    dataIndex: "orders_monthly"
                },
                {
                    align: "center",
                    text: "审核<br/>通过",
                    dataIndex: "is_verify",
                    formatter: function (data, row) {
                        if (data == "1" || data == '3') {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_review' data-accountId = " + row.cells.account_id + "></a>");
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
                    align: "center",
                    text: "接单<br/>状态",
                    dataIndex: "is_allow_order",
                    formatter: function (data, row) {
                        var div = $("<div></div>");
                        if (row.cells.is_verify == '3' || row.cells.is_verify == '1') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var a = $("<a id='js_show_allow_order' data-accountId = " + row.cells.account_id + "></a>");

                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append(a);
                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("是否", "shifoushangjia", '<br/>上架'),
                    dataIndex: "is_online",
                    formatter: function (data, row) {
                        if (row.cells.is_verify == '3' || row.cells.is_allow_order == '2') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_online' data-accountId = " + row.cells.account_id + "></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append('<br/>').append(a);

                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("暂不", "zanbujiedan", '<br/>接单'),
                    dataIndex: "leave",
                    formatter: function (data, row) {

                        var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";

                        if (row.cells.is_verify == 2) {
                            imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "'id='leaveDialogue' src='/resources/images/ico_set.gif' class='set_img' /></a>";
                        }

                        var div = $("<div></div>");
                        div.append(imgTip);

                        if (data == true) {
                            div.append('&nbsp;是');
                            return div;
                        } else {
                            div.append("&nbsp;否" + data);
                            return div;
                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("接单", "jiedanshangxian", '<br/>上限'),
                    dataIndex: "periodMax",
                    formatter: function (data, row) {
                        var div = $("<div></div>");
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";

                        if (!(row.cells.weibo_type == weibo_type_weixin)) {
                            if (row.cells.is_verify == 2) {
                                flag = row.cells.orderMax == '' ? false : true;
                                imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "' data_orderMax='" + row.cells.orderMax + "' data_radio = '" + flag + "' class = 'js_onlineDialogue set_img' src='/resources/images/ico_set.gif' /></a>"
                            }
                        }

                        if (row.cells.orderMax == '') {
                            return div.append(imgTip).append('&nbsp;无');
                        } else {
                            return div.append(imgTip).append('&nbsp;' + row.cells.orderMax);
                        }
                    }
                },
                {
                    align: "center",
                    text: '<span class="js_tour_detail">详情</span>',
                    dataIndex: "",
                    width:36,
                    formatter: formatters.detail
                }
            ];
        }else{
            columns = [
                {
                    align: "left",
                    text: "账号名",
                    dataIndex: "weibo_name",
                    cls: "t1",
                    // 使用模板前对数据预处理
                    tmplPreprocessor: tmplColWeiboNamePreprocessor,
                    tmplId: 'tmplColWeiboName'
                },
                {
                    align: "left",
                    text: "粉丝数",
                    dataIndex: "followers_count",
                    cls: "t2",
                    formatter: formatters.followers_count
                },
                {
                    align: "left",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg("硬广", "retweetPriceHover", '<br/>转发报价'),
                    dataIndex: "retweet_price",
                    cls: "t3",
                    editable: isPriceEditable,
                    editHandler: editHandlers.retweetPrice,
                    formatter: formatters.price
                },
                {
                    align: "left",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg('硬广', "tweetPriceHover", "<br/>直发报价"),
                    dataIndex: "tweet_price",
                    cls: "t4",
                    editable: isPriceEditable,
                    editHandler: editHandlers.tweetPrice,
                    formatter: formatters.price
                },
                {
                    align: "left",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg("软广", "softRetweetPriceHover", '<br/>转发报价'),
                    dataIndex: "soft_retweet_price",
                    cls: "t5",
                    editable: isPriceEditable,
                    editHandler: editHandlers.softRetweetPrice,
                    formatter: formatters.price
                },
                {
                    align: "left",
                    cellAttr: {
                        title: "不可编辑"
                    },
                    text: renderTitleMiddelImg("软广", "softTweetPriceHover", '<br/>直发报价'),
                    dataIndex: "soft_tweet_price",
                    cls: "t6",
                    editable: isPriceEditable,
                    editHandler: editHandlers.softTweetPrice,
                    formatter: formatters.price
                },
                // {
                    // align: "left",
                    // cellAttr: {
                        // title: "不可编辑"
                    // },
                    // text: renderTitle("带号价", "daihaojia"),
                    // dataIndex: "content_price",
                    // cls: "grid_editableCol",
                    // editable: isPriceEditable,
                    // width: 52,
                    // editHandler: editHandlers.contentPrice,
                    // formatter: formatters.price
                // },
                {
                    align: "left",
                    text: "周订单",
                    dataIndex: "orders_weekly",
                    cls: "t7",

                },
                {
                    align: "left",
                    text: "月订单",
                    dataIndex: "orders_monthly",
                    cls: "t8",
                },
                {
                    align: "left",
                    text: "审核<br/>通过",
                    dataIndex: "is_verify",
                    cls: "t9",
                    formatter: function (data, row) {
                        if (data == "1" || data == '3') {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_review' data-accountId = " + row.cells.account_id + " class='blue'></a>");
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
                    dataIndex: "is_allow_order",
                    cls: "t10",
                    formatter: function (data, row) {
                        var div = $("<div></div>");
                        if (row.cells.is_verify == '3' || row.cells.is_verify == '1') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var a = $("<a id='js_show_allow_order' data-accountId = " + row.cells.account_id + " class='blue'></a>");

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
                        if (row.cells.is_verify == '3' || row.cells.is_allow_order == '2') {
                            return '--';
                        } else if (data == "1") {
                            return "是";
                        } else {
                            var div = $("<div></div>");
                            var a = $("<a id='js_show_online' data-accountId = " + row.cells.account_id + "></a>");
                            a.attr({
                                href: "javascript: void(0)"
                            }).text("详情");

                            return div.append("否&nbsp;").append('<br/>').append(a);

                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("暂不", "zanbujiedan", '<br/>接单'),
                    dataIndex: "leave",
                    cls: "t11",
                    formatter: function (data, row) {

                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";

                        if (row.cells.is_verify == 2) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "'id='leaveDialogue' src='/resources/images/ico_set.gif' class='set_img' /></a>";
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "'id='leaveDialogue'></a>";
                        }

                        // var div = $("<div></div>");
                        // div.append(imgTip);
                        var div = $(imgTip);

                        if (data == true) {
                            // div.append('&nbsp;是');
                            div.append('<em class="yes">是</em>');
                            return div;
                        } else {
                            // div.append("&nbsp;否" + data);
                            div.append('<em class="no">否' + data + '</em>');
                            return div;
                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("是否", "shifouyingguang", '<br/>接硬广'),
                    dataIndex: "is_extend",
                    cls: "t11",
                    formatter: function (data, row) {

                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";
                        if (row.cells.is_verify == 2) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "' class = 'js_isHardDialogue set_img' src='/resources/images/ico_set.gif'/></a>"
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "'></a>"
                        }
                        if (data == "1") {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;是');
                            return $(imgTip).append('<em class="yes">是</em>');
                        } else {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;否');
                            return $(imgTip).append('<em class="no">是</em>');
                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("接单", "jiedanshangxian", '<br/>上限'),
                    dataIndex: "periodMax",
                    cls: "t11",
                    formatter: function (data, row) {
                        // var div = $("<div></div>");
                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";

                        if (!(row.cells.weibo_type == weibo_type_weixin)) {
                            if (row.cells.is_verify == 2) {
                                flag = row.cells.orderMax == '' ? false : true;
                                // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "' data_orderMax='" + row.cells.orderMax + "' data_radio = '" + flag + "' class = 'js_onlineDialogue set_img' src='/resources/images/ico_set.gif' /></a>"
                                imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "' data_orderMax='" + row.cells.orderMax + "' data_radio = '" + flag + "'></a>"
                            }
                        }

                        if (row.cells.orderMax == '') {
                            // return div.append(imgTip).append('&nbsp;无');
                            return $(imgTip).append('<em class="no">无</em>');
                        } else {
                            // return div.append(imgTip).append('&nbsp;' + row.cells.orderMax);
                            return $(imgTip).append('<em class="yes">' + row.cells.orderMax + '</em>');
                        }
                    }
                },
                {
                    align: "center",
                    text: renderTitleMiddelImg("订单是否", "isEnableMicroTask", '<br/>可带链接'),
                    dataIndex: "isEnableMicroTask",
                    cls: "t12",
                    formatter: function (data, row) {
                        return doT.template($('#tmplIsEnableMicroTaskColumns').html())(row.cells);
                    }
                },
                {
                    align: "center",
                    // text: '<div id="autoSendTips"><span>订单</span><img id="order_auto_send" src="/resources/images/icon_tips.jpg" title="点击查看详情"><span><br>托管</span></div>',
                    text: renderTitleMiddelImg("订单", "isEnableMicroTask", '<br/>托管'),
                    dataIndex: "is_auto_send",
                    cls: "t11",
                    id:"autoSendTips",
                    formatter: function (data, row) {
                        var authTip = '';
                        // var imgTip = "<a href='javascript:void(0)' title='不可设置'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                        var imgTip = "<a href='javascript:void(0)' title='不可设置'></a>";
                        if (row.cells.is_auth == 1 && (row.cells.weibo_type == 1 || row.cells.weibo_type == 2 )) {
                            // imgTip = "<a href='javascript:void(0)' title='设置'><img data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "' class = 'js_isAutoSendDialogue set_img' src='/resources/images/ico_set.gif'/></a>"
                            imgTip = "<a href='javascript:void(0)' title='设置' data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "'></a>"
                        } else if( row.cells.weibo_type == 1 || row.cells.weibo_type == 2 ){
                            // authTip = "<a href='javascript:void(0)' title='不可设置' style='color: #f00;'><img data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "' data_auth='"+ row.cells.is_auth+"' class = 'js_isAuthDialogue set_img' src = '/resources/images/alert.gif'/></a>";
                            authTip = "<a href='javascript:void(0)' title='不可设置' style='color: #f00;'><img data_account_id = '" + row.cells.account_id + "' data_radio = '" + data + "' data_auth='"+ row.cells.is_auth+"' class = 'js_isAuthDialogue set_img' src = '/App/Public/Media/images/myimg/alert.gif'/></a>";
                        } else {
                            // imgTip = "<a href='javascript:void(0)' title='平台不支持此操作'><img src='/resources/images/ico_set_disable.gif' class='set_disable_img' /></a>";
                            imgTip = "<a href='javascript:void(0)' title='平台不支持此操作'></a>";
                        }

                        if (data == "1") {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;是').append(authTip);
                            return $(imgTip).append('<em class="yes">是</em>');
                        } else {
                            // var div = $("<div></div>");
                            // return div.append(imgTip).append('&nbsp;否').append(authTip);
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
            ];
        }
        grid = exports.grid = new W.Grid({
            messages: {
                nodata: "很抱歉，没有找到合适的账号",
                noQueryResult: "很抱歉，没有找到合适的账号"
            },
            renderTo: "#list",
            // url: "/information/accountmanage/account?filters%5Bweibo_type%5D="+weiboType,
            url: '/Media/SocialAccount/getAccountList/?filters%5Btype%5D=' + weiboType,
            filters: filters,
            columns: columns,
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

                    if (row.cells.level != account_level_normal || row.cells.weibo_type == weibo_type_weixin) {
                        row.el.addClass('editable_reductiononly');
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
                        var orderMaxInput = $(".js_orderMaxInput").val();
                        var accountId = $(".js_periodAccountId").val();
                        var self = this;

                        if (isOnlineRadio == 1) {
                            if (!/^[1-9]{1}[0-9]?$/.test(orderMaxInput)) {
                                W.alert('请输入1-99之间的正整数！');
                                return false;
                            }
                        }

                        if (selectAllCheckbox) {
                            W.confirm("确认对全部账号进行接单上限的设置吗？", function (sure) {
                                if (!sure) {
                                    return false;
                                } else {
                                    var message = W.message("处理中", "loading", 1000);

                                    $.ajax({
                                        type: "POST",
                                        url: "/information/accountmanage/setperiod",
                                        data: "isOnlineRadio=" + isOnlineRadio + "&selectAllCheckbox=" + selectAllCheckbox + "&orderMaxInput=" + orderMaxInput + "&accountId=" + accountId,
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

                            });
                        } else {
                            var message = W.message("处理中", "loading", 1000);

                            $.ajax({
                                type: "POST",
                                url: "/information/accountmanage/setperiod",
                                data: "isOnlineRadio=" + isOnlineRadio + "&selectAllCheckbox=" + selectAllCheckbox + "&orderMaxInput=" + orderMaxInput + "&accountId=" + accountId,
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
                    var data_orderMax = this.getCurrentTarget().attr("data_orderMax");
                    $(".js_periodAccountId").val(data_account_id);
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

    function initIsHardAdTips() {
        new W.Tips({
            group: 'edit',
            target: ".js_isHardDialogue",
            autoHide: 0,
            width: 300,
            autoHide: false,
            type: "click",
            title: "是否接硬广",
            html: '<div class="policy_modifications"><table border="0" cellspacing="0" cellpadding="0"><tr><td><label class="policy_modifications_yes js_appendExtend"><input class="js_extendAccountId" type="text" style="display:none" value="" /><input type="radio" name="isExtendRadio" class="js_extendRadioTrue" value="1"/><span>是</span></label><label><input type="radio" class="js_extendRadioFalse" name="isExtendRadio" value="0"/><span>否</span></label></td></tr><tr><td class="modifications_select_all"><label><input type="checkbox"  name="selectAllExtendCheckbox" /><span>本次操作应用于所有账号。</span></label></td></tr></table></div>',

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

                    } else {
                        $(".js_extendRadioTrue").prop('checked', true);
                    }
                    $("input[name='selectAllExtendCheckbox']").attr("checked", false);
                }
            }
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
                        var $type = $("input[name='type']:checked").val();
                        if ($leave == 1) {
                            if (!$type) {
                                W.alert("暂离类型不能为空，请选择");
                                return false;
                            }
                            switch ($type) {
                                case "every_day":
                                    var $lefttimehours = $("#every_day_lefttimehours").val();
                                    var $lefttimemin = $("#every_day_lefttimemin").val();
                                    var $backtimehours = $("#every_day_backtimehours").val();
                                    var $backtimemin = $("#every_day_backtimemin").val();
                                    if ($lefttimehours == $backtimehours && $lefttimemin == $backtimemin) {
                                        W.alert('离开日期不能与返回日期相同');
                                        return false;
                                    }

                                    if ($lefttimehours > $backtimehours) {
                                        W.alert('返回日期不能早于离开日期');
                                        return false;
                                    }

                                    if ($lefttimehours >= $backtimehours && $lefttimemin > $backtimemin) {
                                        W.alert('返回日期不能早于离开日期');
                                        return false;
                                    }
                                    break;
                                case "every_week":
                                    var $num = $("input[name='weeks[]']:checked").length;
                                    if (!$num) {
                                        W.alert("星期是必选项，不能为空！");
                                        return false;
                                    }
                                    var $lefttimehours = $("#every_week_lefttimehours").val();
                                    var $lefttimemin = $("#every_week_lefttimemin").val();
                                    var $backtimehours = $("#every_week_backtimehours").val();
                                    var $backtimemin = $("#every_week_backtimemin").val();
                                    if ($lefttimehours == $backtimehours && $lefttimemin == $backtimemin) {
                                        W.alert('离开日期不能与返回日期相同');
                                        return false;
                                    }

                                    if ($lefttimehours > $backtimehours) {
                                        W.alert('返回日期不能早于离开日期');
                                        return false;
                                    }

                                    if ($lefttimehours >= $backtimehours && $lefttimemin > $backtimemin) {
                                        W.alert('返回日期不能早于离开日期');
                                        return false;
                                    }
                                    break;
                                case "other":
                                    var backTime = $.trim($('#backTime').val());
                                    var leftTime = $.trim($('#leftTime').val());
                                    if (backTime == '' || leftTime == '') {
                                        W.alert('离开日期和返回日期不能为空');
                                        return false;
                                    }
                                    if (backTime == leftTime) {
                                        W.alert('离开日期不能与返回日期相同');
                                        return false;
                                    }

                                    var bTime = W.util.parseDate(backTime);
                                    var lTime = W.util.parseDate(leftTime);
                                    if (bTime < lTime) {
                                        W.alert('返回日期不能早于离开日期');
                                        return false;
                                    }


                                    if ((bTime.getTime() - lTime.getTime()) > 180 * 24 * 3600 * 1000) {
                                        W.alert('暂离时间最大为180天，请重新设置');
                                        return false;
                                    }
                                    break;
                                default:
                                    W.alert("暂离类型有误，请重新选择！");
                                    return false;
                                    break;
                            }
                        }

                        var processall = $("input[name='processall']").attr("checked");
                        if (processall) {
                            W.confirm("确认对全部账号进行暂离的设置吗？", function (sure) {
                                if (!sure) {
                                    return false;
                                } else {
                                    var message = W.message("处理中", "loading", 1000);
                                    return $.ajax({
                                        url: "/information/accountmanage/setleave",
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
                            });
                        } else {
                            var message = W.message("处理中", "loading", 1000);
                            return $.ajax({
                                url: "/information/accountmanage/setleave",
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
                        url: "/information/accountmanage/leavedetails",
                        success: function (data) {
                            this.setHtml(data);
                        },
                        data: {
                            accountId: id
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
                    tips.setLoader({
                        url: '/Media/SocialAccount/notallow',
                        dataType: "json",
                        data: {
                            account_id: id
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
                    t.setLoader({
                        url: '/information/accountmanage/review',
                        dataType: "json",
                        data: {
                            account_id: id
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
                target: "#pengyouquanAccountPhoneHover",
                title: "账号手机号",
                floor: 'high',
                text: "账号手机号是指当前微信账号拥有者，且可联系到拥有者本人的手机号码。"
            },
            {
                target: "#retweetPriceHover",
                title: "硬广转发价",
                text: "是指当前账号转发1条其他账号的硬广内容所获得的利益。"
            },
            {
                target: "#pengyouquanRetweetPriceHover",
                title: "分享报价",
                text: "分享报价是指按照客户要求，使用当前微信账号将指定内容分享到微信朋友圈后，可获得的报酬。"
            },
            {
                target: "#softRetweetPriceHover",
                title: "软广转发价",
                text: "是指当前账号转发1条其他账号的软广内容所获得的利益。"
            },
            {
                target: "#tweetPriceHover",
                title: "硬广直发价",
                text: "是指当前账号发布1条指定的硬广内容所获得的收益。"
            },
            {
                target: "#pengyouquanTweetPriceHover",
                title: "直发报价",
                text: "直发报价是指按照客户要求，使用当前微信账号在微信朋友圈中成功发布一条消息后，可获得的报酬。"
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
                html: "用户可以给每个账号设置是否可以接带链接的订单，选项包括“是”、“否”和“特殊活动”。<br/>--&nbsp;选择“是”，则当前账号能接收到所有订单（包括带链接或不带链接）；<br/>--&nbsp;选择“否”，则当前账号仅能接收到不带链接的订单；<br/>--&nbsp;选择“特殊活动”，则表示当前账号不能发送带链接的订单，但用户自己可通过其他渠道成功执行。"
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
                        var isEnableMicroTask = this.el.find('input[name=dataRadio]:checked').val();
                        var isSelectAll = (this.el.find('input[name=selectAllData]:checked').length === 1);

                        var confirmText = isSelectAll ? '亲！确定要将全部账号应用此项设置吗？' : '亲！确定要将账号应用此项设置吗？';

                        W.confirm(confirmText, function (cflag) {
                            if (cflag) {
                                var message = W.message("处理中", "loading", 1000);
                                return $.ajax({
                                    type: "POST",
                                    url: "/information/accountmanage/setisenablemicrotask",
                                    data: {
                                        accountId : iAccountId,
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
            initIsHardAdTips();
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