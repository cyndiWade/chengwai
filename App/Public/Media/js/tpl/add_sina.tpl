<div class="cBox mid-batch pr l" style="width:100%;">
	<form method="post" name="accountRegistor" id="accountRegistor">
        <table class="tab01-inten l">
          <tr>
            <td class="t1"><span><i>*</i><strong>微博昵称：</strong></span></td>
            <td class="t2">
                <input type="text" class="text text-error" name="weibo_id" data-bind="validThreeState: weibo_id, value: weibo_id" />
                <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip" id="weiboId_error"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>硬广转发价：</strong></span></td>
            <td class="t2">
                <input type="text" class="text" name="retweet_price" data-bind="value: retweet_price, placeholder: '请输入正整数', validThreeState: retweet_price" />
                <p data-bind="html: retweet_price.notice_text, css: retweet_price.notice_css" class="cBox_tip" id="reTweetPrice_error" style="padding-right:47px;"></p>
            </td>
          </tr>
        </table>
    </form>
</div>