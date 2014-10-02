<div class="bloger_box_main fl ">
    <ul class="accountData_info">

            <li>
                <img id='d_zhuanpingzhi' src='/resources/images/icon_tips.jpg' title='点击查看详情'>转评值：<strong
                    data-bind="text: active_score"></strong>
            </li>
            <li>
                <img id='d_yuehegelv' src='/resources/images/icon_tips.jpg' title='点击查看详情'>月合格率：<strong
                    data-bind="text: order_yield>0 ? order_yield + '%' : '暂无数据'"></strong>
            </li>
            <li><img id='d_beishoucangshu' src='/resources/images/icon_tips.jpg'
                                                   title='点击查看详情'>被收藏数：<strong
                    data-bind="text: collection_num"></strong></li>
            <li><img id='d_beilaheishu' src='/resources/images/icon_tips.jpg'
                                       title='点击查看详情'>被加黑名单数：<strong data-bind="text: company_black_num"></strong>
            </li>
            <li><img id='d_yuejudanlv' src='/resources/images/icon_tips.jpg' title='点击查看详情'>
                月拒单率：<strong data-bind="text: pass_order_monthly>0 ? pass_order_monthly + '%' : 0"></strong>
            </li>
            <li><img id='d_yueliudanlv' src='/resources/images/icon_tips.jpg' title='点击查看详情'>
                月流单率：<strong data-bind="text: deny_order_monthly>0 ? deny_order_monthly + '%' : 0"></strong>
            </li>
            <li>账号分类：<strong data-bind="text: content_categories"></strong></li>
            <li>账号标签：<strong data-bind="text: keywords() ? keywords : hand_tags"></strong></li>
            <li>
                <img id='d_shifougongkai' src='/resources/images/icon_tips.jpg' title='点击查看详情'>是否公开：
                <strong class="bolger_table_" data-bind="text: is_open_name"></strong>
                <a data-bind="attr: {data_radio: is_open, data_account_id: account_id}" href="javascript:void(0)" class='bolger_table_editor js_setIsOpen'><img src='/resources/images/ico_pen_edit.gif'></a>
                </li>
            <li>
                是否支持预约：
                <strong class="bolger_table_" data-bind="text: is_famous() == 1 ? '是' : '否'">
                </strong>
            </li>

            <li>
                <img id='haopinglv' src='/resources/images/icon_tips.jpg' title='点击查看详情'>好评率：
                <strong class="bolger_table_">
                    <!-- ko if: rating.good_rating_rate -->
                    <a class='js_haopinglv' href="javascript:void(0)" data-bind="text: rating.good_rating_rate_display, attr:{data_account_id: account_id}"></a>
                    <!-- /ko -->
                    <!-- ko if: !rating.good_rating_rate -->
                    暂无数据
                    <!-- /ko -->
                </strong>
            </li>
        <!-- ko if: weibo_type()==9 -->
            <li>
                <img id='fensirenzheng' src='/resources/images/icon_tips.jpg' title='点击查看详情'>是否粉丝认证:
                <strong class="bolger_table_">
                    <!-- ko if:follower_be_identified() == 1 -->
                    是 (认证时间:<span data-bind="text: follower_be_identified_time"></span>)
                    <!-- /ko -->
                    <!-- ko if:follower_be_identified() != 1 -->
                    否
                    <!-- /ko -->
                </strong>
            </li>
            <li height="35">
                <img id='weixin_gender_distribution' src='/resources/images/icon_tips.jpg' title='点击查看详情'>性别分布:
                <strong class="bolger_table_">
                    男：<span data-bind="text: gender_distribution_male"></span>%; 女：<span data-bind="text: gender_distribution_female"></span>%;
                    其他：<span data-bind="text: gender_distribution_unknown"></span>%
                </strong>
            </li>
            <li><img id="weixin_gender_distribution_authenticate" src='/resources/images/icon_tips.jpg' title='点击查看详情'>性别分布认证:
                <strong>
                    <!-- ko if:gender_distribution_identified() == 1 -->
                    是 (认证时间:<span data-bind="text: gender_distribution_identified_time.date"></span>)
                    <!-- /ko -->
                    <!-- ko if:gender_distribution_identified() != 1 -->
                    否
                    <!-- /ko -->
                </strong></li>
            <li>微信号:
                <strong data-bind="text: weibo_id"></strong>
            </li>
        <!-- /ko -->
        <!-- ko if: weibo_type()==17 -->
            <li>
                淘宝会员名:
                <strong class="bolger_table_" data-bind="text: weitao_taobao_id"></strong>
            </li>

        <!-- /ko -->

        <input name="feedback_itemid" class="feedback_itemid" value="ACCOUNT_DETAIL" style="display:none"/>
        <input name="feedback_itemname" class="feedback_itemname" value="账号详情" style="display:none"/>
    </ul>




    <!--ko if:weibo_type()==9-->
    <div class="platformImgsDiv">
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">头像：</li>
            <li>
                <a data-bind="attr: {href: screen_portrait.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: screen_portrait.full_path}" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_uploadAvatar" data-bind="attr:{id: account_id + '_uploadAvatar'}"></span>
            </li>
        </ul>
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">二维码：</li>
            <li>
                <a data-bind="attr: {href: screen_shot_qr_code.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: screen_shot_qr_code.full_path}" class="platform_imgs_followerImg"/>
                </a>
            </li>
        </ul>
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">粉丝截图：</li>
            <li>
                <a data-bind="attr: {href: screen_shot_followers.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: screen_shot_followers.full_path}" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_showUploadFollowers" data-bind="attr:{id: 'showUploadFollowers_' + account_id}">修改</span>
            </li>
        </ul>
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">趋势截图：</li>
            <li>
                <a data-bind="attr: {href: screen_shot_info.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: screen_shot_info.full_path}" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_uploadInfo" data-bind="attr:{id: account_id + '_uploadInfo'}"></span>
            </li>
        </ul>
    </div>
    <!--/ko-->

    <!--ko if:weibo_type()==23-->
    <div class="platformImgsDiv">
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">账号头像：</li>
            <li>
                <a data-bind="attr: {href: account_avatar.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: account_avatar.full_path}" width="72px" height="72px" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_uploadAccountAvatar" data-bind="attr: {id: 'accountAvatar_' + account_id}">修改</span>
            </li>
        </ul>
    </div>
    <div class="platformImgsDiv">
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">真人头像：</li>
            <li>
                <a data-bind="attr: {href: person_avatar.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: person_avatar.full_path}" width="72px" height="72px" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_uploadPersonAvatar" data-bind="attr: {id: 'personAvatar_' + account_id}">修改</span>
            </li>
        </ul>
    </div>
    <div class="platformImgsDiv">
        <ul class="platform_imgs_follower_ul fl">
            <li class="platform_imgs_follower_li">好友数截图：</li>
            <li>
                <a data-bind="attr: {href: screen_shot_followers.full_path}" class="js_platform_imgs" target="_blank">
                    <img data-bind="attr: {src: screen_shot_followers.full_path}" width="72px" height="72px" class="platform_imgs_followerImg"/>
                </a>
            </li>
            <li class="platform_imgs_follower_li">
                <span class="js_showUploadFollowers" data-bind="attr: {id: 'showUploadFollowers_' + account_id}">修改</span>
            </li>
        </ul>
    </div>
    <div class="platformImgsDiv clear">
        <ul class="fl">
            <li class="platform_imgs_follower_li">发布历史截图（最多上传十张）：</li>
            <li>
                <input type="hidden" data-bind="value: release_history" name="uploadImgReleaseHistory" />
                <div class="fl uploadTxt uploadReleaseHistoryImage" data-bind="plupload: release_history">
                    &nbsp;<a class="btn_small_strong js_btn" href="javascript:;"><span
                                class="btn_wrap">上传</span></a>
                    &nbsp;<a class="btn_small_strong submitHistory" href="javascript:;"><span
                                class="btn_wrap">提交</span></a><em>&nbsp;&nbsp;&nbsp;温馨提示：必须点击“提交”才能成功上传截图哦！</em>
                    <ul class="js_list">

                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!--/ko-->
</div>

<script type="text/html" id="followersUploadForm_pengyouquan_html">
    <form action="/information/accountmanage/claimfollowers" id="followers_num_form_{{!it.accountId}}">
        <input type="hidden" name="js_followersUploaded" id="js_followersUploaded_{{!it.accountId}}" value="0" />
        <input type="hidden" name="ispengyouquan" value="1" />
        <input type="hidden" name="account_id" value="{{!it.accountId}}" />
        <input type="hidden" />
        <table class="followersReview" cellpadding="0" cellspacing="0">
            <tr class="">
                <td class="td_dt" style="white-space: nowrap;"><span class="icon_required">*</span><span>好友数截图:</span></td>
                <td class="uploadFollowers">
                    <span class="js_uploadFollowers" id="js_uploadFollowers_{{!it.accountId}}"></span>
                    <a href="javascript:void 0;" class="followExShowimg" >查看图示</a>
                </td>
            </tr>
            <tr>
                <td class="td_dt"></td>
                <td id="uploaded_img_td_{{!it.accountId}}" class="uploaded_img_td">
                    <p class="validateItem">
                        <label class="validateError" id="js_uploadFollowers_label_{{!it.accountId}}">必须上传“好友数截图”</label>
                    </p>
                </td>
            </tr>
            <tr class="followers_count_row">
                <td class="td_dt"><span class="icon_required">*</span><label for="followers_num">好友数:</label></td>
                <td>
                    <input type="text" name="followers_count" id="followers_count" class="inputSmall followers_count" />
                    <p class="validateItem">
                    </p>
                </td>
            </tr>
            <tr>
                <td class="td_dt"></td>
                <td class="buttons">
                    <a class="btn_small_important" href="javascript:;"><span class="btn_wrap">提交</span></a>
                    <a class="btn_small_normal" href="javascript:;"><span class="btn_wrap">取消</span></a>
                </td>
            </tr>
            {{? it.weiboType == 23}}
            <tr>
                <td colspan="2">
                    <p  class="followers_upload_notice"></p>
                    <p>
                        重要提醒：平台已支持
                        “<span class="warningColor">好友视频认证</span>”
                        和
                        “<span class="warningColor">地域认证</span>”，
                        请在上传截图后联系媒介经理（QQ：{{!it.admin_qq}}）。
                        <span class="warningColor">客户会优先给视频认证标记的账号派单哦。</span>
                    </p>
                </td>
            </tr>
            {{?}}
        </table>
    </form>
</script>

<script type="text/html" id="followersUploadForm_html">
    <form action="/information/accountmanage/claimfollowers" id="followers_num_form_{{!it.accountId}}">
        <input type="hidden" name="js_followersUploaded" id="js_followersUploaded_{{!it.accountId}}" value="0" />
        <input type="hidden" name="account_id" value="{{!it.accountId}}" />
        <input type="hidden" />
        <table class="followersReview" cellpadding="0" cellspacing="0">
            <tr class="">
                <td class="td_dt"><span class="icon_required">*</span><span>粉丝截图:</span></td>
                <td class="uploadFollowers">
                    <span class="js_uploadFollowers" id="js_uploadFollowers_{{!it.accountId}}"></span>
                    <a href="http://qudao.weiboyi.com/auth/index/help/type/5/opt/10#type5opt10" target="_blank">查看正确的“粉丝截图”图示</a>
                </td>
            </tr>
            <tr>
                <td class="td_dt"></td>
                <td id="uploaded_img_td_{{!it.accountId}}" class="uploaded_img_td">
                    <p class="validateItem">
                        <label class="validateError" id="js_uploadFollowers_label_{{!it.accountId}}">此处为必填</label>
                    </p>
                </td>
            </tr>
            <tr class="followers_count_row">
                <td class="td_dt"><span class="icon_required">*</span><label for="followers_num">粉丝数:</label></td>
                <td>
                    <input type="text" name="followers_count" id="followers_count" class="inputSmall followers_count" />
                    <p class="validateItem">
                        <label class="validateInfo hint" for="followers_count">请确保填写的粉丝数与粉丝截图中一致，否则不予通过！</label>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="td_dt"></td>
                <td class="buttons">
                    <a class="btn_small_important" href="javascript:;"><span class="btn_wrap">提交</span></a>
                    <a class="btn_small_normal" href="javascript:;"><span class="btn_wrap">取消</span></a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p class="followers_upload_notice">
                        重要提醒：凡申请“微信粉丝认证”的微信主，请在上传截图后联系媒介经理（{{!it.admin_qq}}）。客户会优先给已粉丝认证的账号派单。
                    </p>
                </td>
            </tr>
        </table>
    </form>
</script>