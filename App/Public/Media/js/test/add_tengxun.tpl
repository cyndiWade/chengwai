<div class="cBox cBox_tecent">
  <div class="add_account">
      <form method="post" name="accountRegistor" id="accountRegistor">
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	      <tr>
	        <th width="98" height="45" align="right" valign="top"><em>*</em>微博ID：</th>
	        <td width="302" valign="top"><div class="add_account_divTxt">
                <input type="text" class="input_txt" name="weibo_id" data-bind="validThreeState: weibo_id, value: weibo_id"/>
	          </div>
                <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip" id="weiboId_error"></p></td>
	        <td width="35%" valign="middle">
	        	<div style="position:relative;">
	            	<div class="add_account_pic addAccountPic">
	            		<img data-bind="attr: {src: platform_id_img}" style="width:167px;height:84px;float:left;">
	            	 </div>
	          	</div>
	         </td>
	      </tr>
	      <tr>
	        <th height="45" align="right" valign="top"><em>*</em>硬广转发价：</th>
	        <td valign="top"><div class="add_account_divTxt add_account_divTxt02">
                <input data-bind="value: retweet_price, placeholder: '请输入正整数', validThreeState: retweet_price" type="text" class="input_txt" name="retweet_price" style="color:#666;" />
	          </div>
                <p data-bind="html: retweet_price.notice_text, css: retweet_price.notice_css" class="cBox_tip" id="reTweetPrice_error" style="padding-right:47px;"></p></td>
	        <td valign="middle"></td>
	      </tr>
	    </table>
    </form>
  </div>
</div>