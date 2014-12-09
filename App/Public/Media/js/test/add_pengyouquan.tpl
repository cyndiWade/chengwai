<em style="color: red;">温馨提示：请务必填写真实、有效的信息，否则会影响您接单赚钱！</em>
<div class="cBox">
    <div class="add_account">
        <form method="post" name="accountRegistor" id="accountRegistor">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="14%" height="45" align="right" valign="top"><em>*</em>微信号：</th>
                    <td width="52%" valign="top">
                        <div class="add_account_divTxt add_account_divTxt_noBg">
                            <input type="text" class="inputLarge fl" name="weibo_id" data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id"/>
                            &nbsp;<a data-bind="click: checkAccountExists" class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">检查微信号是否已存在</span></a>
                        </div><br/>
                        <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip" id="weiboId_error"></p></td>
                    <td width="34%" valign="middle">
                        <div style="position:relative">
                            <div class="add_account_pic">
                                <img data-bind="attr: {src: notice_img() ? notice_img : platform_account_name_img}" style="width:235px;height:143px;float:left;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>微信名字：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="noticeImg: platform_account_name_img, validThreeState: weibo_name, value: weibo_name" type="text" class="input_txt" name="weibo_name"/>
                        </div><br/>
                        <p data-bind="html: weibo_name.notice_text, css: weibo_name.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle"></td>
                </tr>

                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>好友数：</th>
                    <td valign="top">
                        <input type="text" class="inputLarge fl" data-bind="validThreeState: followers_count, value: followers_count" name="followers_count" style="color:#666;"/>
                        <input type="hidden" data-bind="value: screen_shot_followers" name="uploadImgFollowers" />
                        <div class="fl uploadTxt" id="uploadFollowersImage" data-bind="plupload: screen_shot_followers">
                            &nbsp;<a class="btn_small_strong js_btn" href="javascript:;"><span
                                class="btn_wrap">上传好友数截图</span></a>
                            &nbsp;<a class="followExShowimg" href="javascript:void(0);">图例</a>
                            <ul class="js_list">

                            </ul>
                        </div>
                        <p data-bind="html: followers_count.notice_text, css: followers_count.notice_css" class="cBox_tip"></p></td>
                    </td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>好友描绘：</th>
                    <td valign="top">
                        <textarea placeholder="举例：我的好友女性居多；我的好友北京地区居多；我的好友职业是白领的居多。" style="width: 248px;" data-bind="noticeImg: platform_account_name_img, validThreeState: friend_desc, value: friend_desc" name="friend_desc"></textarea>
                        <p data-bind="html: friend_desc.notice_text, css: friend_desc.notice_css" class="cBox_tip"></p>
                    </td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>直发价格：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="placeholder: '请输入正整数', validThreeState: tweet_price, value: tweet_price" type="text" class="input_txt" name="tweet_price" maxlength="10"/>
                        </div>
                        <p data-bind="html: tweet_price.notice_text, css: tweet_price.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle"></td>
                </tr>

                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>分享价格：</th>
                    <td valign="top">
                        <div class="">
                            <span data-bind="text: tweet_price" style="color:red;">0.00</span> 元
                        </div>
                        <p class="prompt cBox_tip" id="tweetPrice_error">指您在朋友圈分享一次信息得到的收入！</p></td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>真实姓名：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input placeholder="请填写身份证上的姓名！" data-bind="noticeImg: platform_account_name_img, validThreeState: true_name, value: true_name" type="text" class="input_txt" name="true_name"/>
                        </div>
                        <p data-bind="html: true_name.notice_text, css: true_name.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>真实年龄：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="placeholder: '请输入正整数', validThreeState: age, value: age" type="text" class="input_txt" name="age"
                                   style="color:#666;" />
                        </div>
                        <p data-bind="html: age.notice_text, css: age.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>真实性别：</th>
                    <td valign="top">
                        <input data-bind="checked: gender" type="radio" value="1" name="gender"/>男
                        <input data-bind="checked: gender" type="radio" value="2" name="gender"/>女
                        <p data-bind="text: gender.error(), css: gender.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>真实职业：</th>
                    <td valign="top">
                        <select data-bind="options: profession_type_options, optionsText: 'name', optionsValue: 'id', value: profession_type, optionsCaption: '请选择'" name="profession_type"></select>
                        <input data-bind="visible: profession_type() == 7, placeholder:'请输入准确的职业信息', value: profession_other_info" name="profession_other_info" type="text"/>
                        <p data-bind="text: profession_error_display(), css: profession_error_display() ? 'error' : 'correct'" class="cBox_tip"></p>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>教育程度：</th>
                    <td valign="top">
                        <select data-bind="options: edu_degree_options, optionsText: 'name', optionsValue: 'id', value: edu_degree, optionsCaption: '请选择'" name="edu_degree"></select>
                        <p data-bind="text: edu_error_display(), css: edu_error_display() ? 'error' : 'correct'" class="cBox_tip"></p>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>真实地域：</th>
                    <td valign="top">
                        <div class="" data-bind="area: area_id">
                            <input type="hidden" data-bind="value: area_id" name="area_id"/>
                            <select data-bind="options: area_id.country_option, optionsValue: 'id', optionsText: 'name', value: area_id.country, optionsCaption: '请选择'"></select>
                            <select data-bind="options: area_id.province_option, value: area_id.province, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                            <select data-bind="options: area_id.city_option, value: area_id.city, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                            <select data-bind="options: area_id.district_option, value: area_id.district, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                        </div>
                        <p data-bind="text: area_id.error(), css: area_id.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                    </td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>账号手机号：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input data-bind="placeholder: '请输入手机号', validThreeState: account_phone, value: account_phone" type="text" class="input_txt" name="account_phone" />
                        </div>
                        <p data-bind="html: account_phone.notice_text, css: account_phone.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle" class="cBox_tip"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top">微博链接：</th>
                    <td valign="top">
                        <div class="add_account_divTxt">
                            <input placeholder="填写新浪或腾讯微博的主页地址" data-bind="noticeImg: platform_account_name_img, validThreeState: weibo_link, value: weibo_link" type="text" class="input_txt" name="weibo_link"/>
                        </div>
                        <p data-bind="html: weibo_link.notice_text, css: weibo_link.notice_css" class="cBox_tip"></p></td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top">账号优势：</th>
                    <td valign="top">
                        <textarea placeholder="举例：朋友圈互动性强；朋友圈影响力大；本人配合度较高。" style="width: 248px;" data-bind="noticeImg: platform_account_name_img, validThreeState: account_advantage, value: account_advantage" name="account_advantage"></textarea><br/>
                        <p data-bind="html: account_advantage.notice_text, css: account_advantage.notice_css" class="cBox_tip"></p>
                    </td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top"><em>*</em>账号头像：</th>
                    <td valign="top">
                        <input type="hidden" data-bind="value: account_avatar" name="uploadImgAccountAvatar" />
                        <div class="fl uploadTxt" id="uploadAccountAvatarImage" data-bind="plupload: account_avatar">
                            &nbsp;<a class="btn_small_strong js_btn" href="javascript:;"><span
                                        class="btn_wrap">上传</span></a>
                            <ul class="js_list">

                            </ul>
                        </div>
                        <p data-bind="html: account_avatar.notice_text, css: account_avatar.notice_css" class="cBox_tip"></p></td>
                    </td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top">真人头像：</th>
                    <td valign="top">
                        <input type="hidden" data-bind="value: person_avatar" name="uploadImgPersonAvatar" />
                        <div class="fl uploadTxt" id="uploadPersonAvatarImage" data-bind="plupload: person_avatar">
                            &nbsp;<a class="btn_small_strong js_btn" href="javascript:;"><span
                                        class="btn_wrap">上传</span></a>
                            <ul class="js_list">

                            </ul>
                        </div>
                        <p data-bind="html: person_avatar.notice_text, css: person_avatar.notice_css" class="cBox_tip"></p></td>
                    </td>
                    <td valign="middle"></td>
                </tr>
                <tr>
                    <th height="45" align="right" valign="top">发布历史：</th>
                    <td valign="top">
                        <input type="hidden" data-bind="value: release_history" name="uploadImgReleaseHistory" />
                        <div class="fl uploadTxt" id="uploadReleaseHistoryImage" data-bind="plupload: release_history">
                            &nbsp;<a class="btn_small_strong js_btn" href="javascript:;"><span
                                        class="btn_wrap">上传</span></a>
                            &nbsp;最多可上传10张发布历史截图（<a class="historyExShowimg" href="javascript:void(0);">图例</a>）
                            <ul class="js_list">

                            </ul>
                        </div>
                        <p data-bind="html: release_history.notice_text, css: release_history.notice_css" class="cBox_tip"></p></td>
                    </td>
                    <td valign="middle"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script>
    new W.Tips({type: 'mouseover', html: '<img width="250" src="/resources/images/model_shoe_weixinpengyouquan.jpg">', target: '.followExShowimg'});
    new W.Tips({type: 'mouseover', html: '<img width="250" src="/resources/images/model_history_weixinpengyouquan.jpg">', target: '.historyExShowimg'});
</script>