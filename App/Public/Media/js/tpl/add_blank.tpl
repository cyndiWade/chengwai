<div class="cBox mid-batch pr l tl">
    <form action="" method="post" name="addBlank" id="addBlank">
    <table class="tab01-inten l">
      <tr>
        <td class="t1"><span><i>*</i><strong> 真实姓名：</strong></span></td>
        <td class="t2">
            <input data-bind="validThreeState: truename, value: truename" type="text" class="text text-error" name="truename"/>
            <p data-bind="html: truename.notice_text, css: truename.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>帐号类型：</strong></span></td>
        <td class="t2">
            <input data-bind="checked: cardtype" type="radio" value="0" name="cardtype"/>支付宝
            <input data-bind="checked: cardtype" type="radio" value="1" name="cardtype"/>银行卡
            <p data-bind="text: cardtype.error(), css: cardtype.error() ? 'error' : 'correct'" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>账号：</strong></span></td>
        <td class="t2">
            <input data-bind="validThreeState: account, value: account" type="text" class="text text-error" name="account"/>
            <p data-bind="html: account.notice_text, css: account.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr class="blanktype" style="display:none">
        <td class="t1"><span><i>*</i><strong>开户行：</strong></span></td>
        <td class="t2">
            <input data-bind="validThreeState: bank, value: bank" type="text" class="text text-error" name="bank"/>
            <p data-bind="html: bank.notice_text, css: bank.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
    </table>
    </form>
</div>