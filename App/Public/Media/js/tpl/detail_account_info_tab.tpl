<div class="bloger_box_main fl tubiao01">
    <ul class="accountData_info zhgl tl">
            <li>
                <em class='qicon' id='d_zhuanpingzhi' title='点击查看详情'></em>
                转评值：<strong data-bind="text: active_score"></strong>
            </li>
            <li>
                <em class='qicon' id='d_yuehegelv' title='点击查看详情'></em>
                月合格率：<strong data-bind="text: order_yield>0 ? order_yield + '%' : '暂无数据'"></strong>
            </li>
            <li>
                <em class='qicon' id='d_beishoucangshu' title='点击查看详情'></em>
                被收藏数：<strong data-bind="text: collection_num"></strong>
            </li>
            <li>
                <em class='qicon' id='d_beilaheishu' title='点击查看详情'></em>
                被加黑名单数：<strong data-bind="text: company_black_num"></strong>
            </li>
            <li>
                <em class='qicon' id='d_yuejudanlv' title='点击查看详情'></em>
                月拒单率：<strong data-bind="text: pass_order_monthly>0 ? pass_order_monthly + '%' : 0"></strong>
            </li>
            <li>
                <em class='qicon' id='d_yueliudanlv' title='点击查看详情'></em>
                月流单率：<strong data-bind="text: deny_order_monthly>0 ? deny_order_monthly + '%' : 0"></strong>
            </li>
            <li>账号分类：<strong data-bind="text: content_categories"></strong></li>
            <li>账号标签：<strong data-bind="text: keywords() ? keywords : hand_tags"></strong></li>
            <li>
                是否支持预约：
                <strong class="bolger_table_" data-bind="text: is_famous() == 1 ? '是' : '否'">
                </strong>
            </li>

            <li>
                <em class='qicon' id='haopinglv' title='点击查看详情'></em>
                好评率：
                <strong class="bolger_table_">
                    <!-- ko if: rating.good_rating_rate -->
                    <a class='js_haopinglv' href="javascript:void(0)" data-bind="text: rating.good_rating_rate_display, attr:{data_account_id: account_id}"></a>
                    <!-- /ko -->
                    <!-- ko if: !rating.good_rating_rate -->
                    暂无数据
                    <!-- /ko -->
                </strong>
            </li>
        <!-- ko if: weibo_type()==3 -->
            <li>
                <em class='qicon' id='fensirenzheng' title='点击查看详情'></em>
                是否粉丝认证:
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
                <em class='qicon' id='weixin_gender_distribution' title='点击查看详情'></em>
                性别分布:
                <strong class="bolger_table_">
                    男：<span data-bind="text: gender_distribution_male"></span>%; 女：<span data-bind="text: gender_distribution_female"></span>%;
                    其他：<span data-bind="text: gender_distribution_unknown"></span>%
                </strong>
            </li>
            <li>
                <em class='qicon' id='weixin_gender_distribution_authenticate' title='点击查看详情'></em>
                性别分布认证:
                <strong>
                    <!-- ko if:gender_distribution_identified() == 1 -->
                    是 (认证时间:<span data-bind="text: gender_distribution_identified_time.date"></span>)
                    <!-- /ko -->
                    <!-- ko if:gender_distribution_identified() != 1 -->
                    否
                    <!-- /ko -->
                </strong>
            </li>
            <li>微信号:
                <strong data-bind="text: weibo_id"></strong>
            </li>
        <!-- /ko -->

        <input name="feedback_itemid" class="feedback_itemid" value="ACCOUNT_DETAIL" style="display:none"/>
        <input name="feedback_itemname" class="feedback_itemname" value="账号详情" style="display:none"/>
    </ul>




    <!--ko if:weibo_type()==3-->
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
            <li class="platform_imgs_follower_li">
                <span class="js_uploadQrCode" data-bind="attr:{id: account_id + '_qrCodeThumb'}"></span>
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
    </div>
    <!--/ko-->
</div>

<script type="text/html" id="followersUploadForm_html">
    <form action="/Media/SocialAccount/claimfollowers" id="followers_num_form_{{!it.accountId}}">
        <input type="hidden" name="js_followersUploaded" id="js_followersUploaded_{{!it.accountId}}" value="0" />
        <input type="hidden" name="account_id" value="{{!it.accountId}}" />
        <input type="hidden" name="account_type" value="{{!it.accountType}}" />
        <input type="hidden" name="uploadimg" value="" />
        <table class="followersReview tl" cellpadding="0" cellspacing="0">
            <tr class="">
                <td class="td_dt"><span class="icon_required">*</span><span>粉丝截图:</span></td>
                <td class="uploadFollowers">
                    <span class="js_uploadFollowers" id="js_uploadFollowers_{{!it.accountId}}"></span>
                    <a href="javascript:void;" target="_blank">查看正确的“粉丝截图”图示</a>
                </td>
            </tr>
            <tr>
                <td class="td_dt" style="width:80px;"></td>
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