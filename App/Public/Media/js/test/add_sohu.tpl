<script type="text/javascript" src="/js/weixin.js?v=1361433539870"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?v=1358853974931"></script>
<script type="text/javascript" src="/js/platformhandlers.js?v=1358853974931"></script>
<div class="cBox">
    <div class="add_account">
        <form method="post" action="information/account/submitaccount" name="accountRegistor" id="accountRegistor">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="14%" height="45" align="right" valign="top"><em>*</em>账号名：</th>
                    <td width="52%" valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="noticeImg: platform_account_name_img, validThreeState: weibo_name, value: weibo_name"
                                   type="text"
                                   class="input_txt" name="weibo_name"/>
                        </div>
                        <p data-bind="html: weibo_name.notice_text, css: weibo_name.notice_css" class="cBox_tip"></p>
                    </td>
                    <td width="34%" valign="middle">
                        <div style="position:relative">
                            <div class="add_account_pic">
                                <img data-bind="attr: {src: notice_img() ? notice_img : platform_account_name_img}" style="width:235px;height:143px;float:left;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>ID：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id"
                                   type="text" class="input_txt"
                                   name="weibo_id"/>
                        </div>
                        <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip"
                           id="weiboId_error"></p></td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>粉丝数：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="validThreeState: followers_count, value: followers_count" type="text"
                                   class="input_txt" name="followers_count" style="color:#666;"/>
                        </div>
                        <p data-bind="html: followers_count.notice_text, css: followers_count.notice_css"
                           class="cBox_tip"></p></td>
                    <td valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>硬广直发价：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="placeholder: '请输入正整数', validThreeState: tweet_price, value: tweet_price"
                                   type="text" class="input_txt" name="tweet_price" maxlength="10"/>
                        </div>
                        <p data-bind="html: tweet_price.notice_text, css: tweet_price.notice_css" class="cBox_tip"></p>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>硬广转发价：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="value: retweet_price, placeholder: '请输入正整数', validThreeState: retweet_price"
                                   type="text" class="input_txt" name="retweet_price" style="color:#666;"/>
                        </div>
                        <p data-bind="html: retweet_price.notice_text, css: retweet_price.notice_css" class="cBox_tip"
                           id="reTweetPrice_error" style="padding-right:47px;"></p></td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
            </table>
        </form>
    </div>
</div>