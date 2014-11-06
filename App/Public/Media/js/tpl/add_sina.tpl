<div class="cBox mid-batch pr l tl" style="width:100%;">
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
          <tr>
            <td class="t1"><span><i>*</i><strong>名人职业：</strong></span></td>
            <td class="t2">
                <select name="occupation" data-bind="getTags: occupation, options: occupation.occupation_option, optionsValue: 'val', optionsText: 'name', value: occupation"></select>
				<p data-bind="text: occupation.notice_text, css: occupation.notice_css" class="cBox_tip" id="occupation_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>媒体领域：</strong></span></td>
            <td class="t2">
                <select name="field" data-bind="getTags: field, options: field.field_option, optionsValue: 'val', optionsText: 'name', value: field"></select>
				<p data-bind="html: field.notice_text, css: field.notice_css" class="cBox_tip" id="field_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>地方名人/媒体：</strong></span></td>
            <td class="t2">
                <select name="cirymedia" data-bind="getTags: cirymedia, options: cirymedia.cirymedia_option, optionsValue: 'val', optionsText: 'name', value: cirymedia"></select>
				<p data-bind="html: cirymedia.notice_text, css: cirymedia.notice_css" class="cBox_tip" id="cirymedia_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>兴趣标签：</strong></span></td>
            <td class="t2">
                <select name="interest" data-bind="getTags: interest, options: interest.interest_option, optionsValue: 'id', optionsText: 'name', value: interest"></select>
				<p data-bind="html: interest.notice_text, css: interest.notice_css" class="cBox_tip" id="interest_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>配合度：</strong></span></td>
            <td class="t2">
                <select name="coordination" data-bind="options: coordination_option, optionsValue: 'id', optionsText: 'name', value: coordination"></select>
				<p data-bind="html: coordination.notice_text, css: coordination.notice_css" class="cBox_tip" id="coordination_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr>
            <td class="t1"><span><i>*</i><strong>是否支持原创：</strong></span></td>
            <td class="t2">
                <select name="originality" data-bind="options: originality_option, optionsValue: 'id', optionsText: 'name', value: originality"></select>
				<p data-bind="html: originality.notice_text, css: originality.notice_css" class="cBox_tip" id="originality_error" style="padding-right:47px;"></p>
            </td>
          </tr>
        </table>
    </form>
</div>