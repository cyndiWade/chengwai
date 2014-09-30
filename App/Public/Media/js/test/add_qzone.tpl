<script type="text/javascript" src ="/js/weixin.js?v=1361433539870"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.js?v=1358853974931"></script>
<script type="text/javascript" src="/js/platformhandlers.js?v=1358853974931"></script>
<div class="cBox">
  <div class="add_account">
	  <form method="post" action="information/account/submitaccount" name="accountRegistor" id="accountRegistor">
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	      <tr>
	        <th height="45" align="right" valign="top"><em>*</em>ID：</th>
	        <td valign="top"><div class="add_account_divTxt">
                <input data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id" type="text" class="input_txt" name="weibo_id"/>
	          </div>
                <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip" id="weiboId_error"></p></td>
	          <td valign="middle"></td>
              <td width="34%" valign="middle">
                  <div style="position:relative">
                      <div class="add_account_pic add_account_pic_2">
                          <img data-bind="attr: {src: platform_id_img}" style="width:167px;height:84px;float:left;">
                      </div>
                  </div>
              </td>
	      </tr>
	      <tr>
	        <th height="45" align="right" valign="top"><em>*</em>硬广直发价：</th>
	        <td valign="top"><div class="add_account_divTxt">
                <input data-bind="placeholder: '请输入正整数', validThreeState: tweet_price, value: tweet_price" type="text" class="input_txt" name="tweet_price" maxlength="10"/>
            </div>
                <p data-bind="html: tweet_price.notice_text, css: tweet_price.notice_css" class="cBox_tip"></p></td>
	        <td valign="middle" class="cBox_tip"></td>
	      </tr>
	      <tr>
	        <th height="45" align="right" valign="top"><em>*</em>硬广转发价：</th>
	        <td valign="top"><div class="add_account_divTxt">
                <input data-bind="value: retweet_price, placeholder: '请输入正整数', validThreeState: retweet_price" type="text" class="input_txt" name="retweet_price" style="color:#666;" />
            </div>
                <p data-bind="html: retweet_price.notice_text, css: retweet_price.notice_css" class="cBox_tip" id="reTweetPrice_error" style="padding-right:47px;"></p></td>
	        <td valign="middle" class="cBox_tip"></td>
	      </tr>
	    </table>
    </form>
  </div>
</div>