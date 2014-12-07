<div class="cBox">
    <div class="add_account">
        <form method="post" action="information/account/submitaccount" name="accountRegistor" id="accountRegistor">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="14%" height="45" align="right" valign="top"><em>*</em>账号名：</th>
                    <td width="52%" valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="noticeImg: platform_account_name_img, validThreeState: weibo_name, value: weibo_name" type="text"
                                   class="input_txt" name="weibo_name"/>
                        </div>
                        <p data-bind="html: weibo_name.notice_text, css: weibo_name.notice_css" class="cBox_tip"></p>
                    </td>
                    <td width="34%" valign="middle">
                        <div style="position:relative">
                            <div class="add_account_pic">
                                <img data-bind="attr: {src: notice_img() ? notice_img : platform_url_img}" style="width:235px;height:143px;float:left;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>微信号：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id" type="text" class="input_txt"
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
                    <th height="45" align="right" valign="top">性别分布：</th>
                    <td valign="top">
                        <div class="">
                            男 <input type="text" data-bind="value: gender_distribution_male" name="gender_distribution_male"  class="inputSmall" value=""/>% /
                            女 <input type="text" data-bind="value: gender_distribution_female" name="gender_distribution_female"  class="inputSmall" value=""/>%
                        </div>
                        <p data-bind="css: gender_distribution_male.error() || gender_distribution_female.error() ? 'error' : 'correct'" class="cBox_tip">
                            <span data-bind="text: gender_distribution_male.error() || gender_distribution_female.error()"></span>
                            <a href="/help/search/index/key/sex_range" target="_blank">如何查看微信公众账号的“性别分布”？</a></p></td>
                    <td valign="middle">&nbsp;</td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>单图文价格：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="validThreeState: single_graphic_price, value: single_graphic_price"
                                   type="text" class="input_txt" name="single_graphic_price" maxlength="10"/>
                            <input type="hidden"  name="single_graphic_hard_price" data-bind="value:single_graphic_price"/>
                        </div>
                        <p data-bind="html: single_graphic_price.notice_text, css: single_graphic_price.notice_css" class="cBox_tip"></p>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em></em>多图文第一条价格：</th>
                    <td valign="top">
                        <span data-bind="text: multi_graphic_top_price" style="color: red"></span> 元（可在“账号管理”页面修改）
                        <input type="hidden"  name="multi_graphic_top_price" data-bind="value:multi_graphic_top_price"/>
                        <input type="hidden"  name="multi_graphic_hard_top_price" data-bind="value:multi_graphic_top_price"/>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em></em>多图文第二条价格：</th>
                    <td valign="top">
                        <span data-bind="text: multi_graphic_second_price" style="color: red"></span> 元（可在“账号管理”页面修改）
                        <input type="hidden"  name="multi_graphic_second_price" data-bind="value:multi_graphic_second_price"/>
                        <input type="hidden"  name="multi_graphic_hard_second_price" data-bind="value:multi_graphic_second_price"/>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em></em>多图文其他位置价格：</th>
                    <td valign="top">
                        <span data-bind="text: multi_graphic_other_price" style="color: red"></span> 元（可在“账号管理”页面修改）
                        <input type="hidden"  name="multi_graphic_other_price" data-bind="value:multi_graphic_other_price"/>
                        <input type="hidden"  name="multi_graphic_hard_other_price" data-bind="value:multi_graphic_other_price"/>
                        <!--内容价现在在这儿-->
                        <input type="hidden"  name="content_price" data-bind="value:content_price"/>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th valign="top" align="right"><em>*</em>头像：</th>
                    <td align="left" colspan="2">
                        <div class="fl uploadTxt" id="uploadTouXiang" data-bind="plupload: screen_portrait">
                            <div class="fl">
                                <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                            </div>
                            <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_avatar_desc">图例&nbsp;</a>
                            <ul class="js_list">
                            </ul>
                            <input type="hidden" data-bind="value: screen_portrait" name="uploadImgAvatar">
                        </div>
                        <p data-bind="css: screen_portrait.error() ? 'error' : 'correct', text: screen_portrait.error()" class="cBox_tip">
                    </td>
                </tr>
                <tr>
                    <th valign="top" align="right"><em>*</em>二维码：</th>
                    <td align="left" colspan="2">
                        <div class="fl uploadTxt" data-bind="plupload: screen_shot_qr_code">
                            <div class="fl">
                                <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                            </div>
                            <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_QR_code_desc">图例&nbsp;</a>
                            <ul class="js_list">
                            </ul>
                            <input type="hidden" data-bind="value: screen_shot_qr_code" name="uploadImgQrCode">
                        </div>
                        <p data-bind="css: screen_shot_qr_code.error() ? 'error' : 'correct', text: screen_shot_qr_code.error()" class="cBox_tip">
                    </td>
                </tr>
                <tr>
                    <th valign="top" align="right"><em>*</em>粉丝截图：</th>
                    <td align="left" colspan="2">
                        <div class="fl uploadTxt" data-bind="plupload: screen_shot_followers">
                            <div class="fl">
                                <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                            </div>
                            <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_followers_desc">图例&nbsp;</a>
                            <ul class="js_list">
                            </ul>
                            <input type="hidden" data-bind="value: screen_shot_followers" name="uploadImgFollowers">
                        </div>
                        <p data-bind="css: screen_shot_followers.error() ? 'error' : 'correct', text: screen_shot_followers.error()" class="cBox_tip">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>