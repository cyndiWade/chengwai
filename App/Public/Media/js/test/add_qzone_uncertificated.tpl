<div class="cBox">
    <div class="add_account">
        <form method="post" name="accountRegistor" id="accountRegistor">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>ID：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input type="text" class="input_txt" name="weibo_id" data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id"/>
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
                    <th height="45" align="right" valign="top"></th>
                    <td valign="top"></td>
                    <td valign="middle"></td>
                </tr>
            </table>
        </form>
    </div>
</div>