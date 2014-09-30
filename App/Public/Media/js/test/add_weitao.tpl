<div class="cBox">
    <div class="add_account">
        <form method="post" name="accountRegistor" id="accountRegistor">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>微淘ID：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input type="text" class="input_txt" name="weibo_id" data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id"/>
                        </div>
                        <div class="weitaoTips">
                            <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip fl"
                               id="weiboId_error"></p>
                            <a target="_blank" href="/auth/index/help#6,2">如何获取正确ID？</a>
                        </div>
                    </td>
                    <td width="34%" valign="middle">
                        <div style="position:relative">
                            <div class="add_account_pic">
                                <img data-bind="attr: {src: platform_id_img}" style="width:167px;height:84px;float:left;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>淘宝会员名：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="validThreeState: weitao_taobao_id, value: weitao_taobao_id" type="text"
                                   class="input_txt" name="weitao_taobao_id" style="color:#666;"/>
                        </div>
                        <p data-bind="html: weitao_taobao_id.notice_text, css: weitao_taobao_id.notice_css"
                           class="cBox_tip">
                            </p></td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>硬广直发价：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="validThreeState: tweet_price, value: tweet_price" type="text"
                                   class="input_txt" name="tweet_price" style="color:#666;"/>
                        </div>
                        <p data-bind="html: tweet_price.notice_text, css: tweet_price.notice_css" class="cBox_tip"></p>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
            </table>
        </form>
    </div>
</div>