<div class="cBox mid-batch pr l tl" style="width:100%;">
    <div class="ewm pa">
        <img data-bind="attr: {src: notice_img() ? notice_img : platform_url_img}">
    </div>
    <form method="post" action="information/account/submitaccount" name="accountRegistor" id="accountRegistor">
    <table class="tab01-inten l add_account">
      <tr>
        <td class="t1"><span><i>*</i><strong>账号名：</strong></span></td>
        <td class="t2">
            <input name="weibo_name" data-bind="noticeImg: platform_account_name_img, validThreeState: weibo_name, value: weibo_name" type="text" class="text text-error" />
            <p data-bind="html: weibo_name.notice_text, css: weibo_name.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>微信号：</strong></span></td>
        <td class="t2">
            <input name="weibo_id" data-bind="noticeImg: platform_id_img, validThreeState: weibo_id, value: weibo_id" type="text" placeholder="请输入正确的微信号！" class="text text-error" />
            <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip" id="weiboId_error"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>粉丝数：</strong></span></td>
        <td class="t2">
            <input name="followers_count" data-bind="validThreeState: followers_count, value: followers_count" type="text" class="text" />
            <p data-bind="html: followers_count.notice_text, css: followers_count.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>周平均阅读数：</strong></span></td>
        <td class="t2">
            <input name="weekly_read_avg" data-bind="validThreeState: weekly_read_avg, value: weekly_read_avg" placeholder="请输入正整数" type="text"  class="text text-error" />
            <p data-bind="html: weekly_read_avg.notice_text, css: weekly_read_avg.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span>性别分布：</span></td>
        <td class="t2">
            <div class="sexbox fl">
                <em>男</em><input data-bind="value: gender_distribution_male" name="gender_distribution_male" type="text" class="text" /><i>%</i>
                <strong class="fl">/</strong>
                <em>女</em><input data-bind="value: gender_distribution_female" name="gender_distribution_female" type="text" class="text" /><i>%</i>
            </div>
            <p data-bind="css: gender_distribution_male.error() || gender_distribution_female.error() ? 'error' : 'correct'" class="cBox_tip">
                <span data-bind="text: gender_distribution_male.error() || gender_distribution_female.error()"></span>
                </p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>单图文价格：</strong></span></td>
        <td class="t2">
            <input name="single_graphic_price" maxlength="10" data-bind="validThreeState: single_graphic_price, value: single_graphic_price" type="text" class="text" />
            <input type="hidden"  name="single_graphic_hard_price" data-bind="value:single_graphic_price"/>
            <p data-bind="html: single_graphic_price.notice_text, css: single_graphic_price.notice_css" class="cBox_tip"></p>
        </td>
      </tr>
      <tr class="trtxt">
        <td class="t1">多图文第一条价格：</td>
        <td class="t2 fl">
            <span data-bind="text: multi_graphic_top_price" style="color: red"></span> 元（可在“账号管理”页面修改）
            <input type="hidden"  name="multi_graphic_top_price" data-bind="value:multi_graphic_top_price"/>
            <input type="hidden"  name="multi_graphic_hard_top_price" data-bind="value:multi_graphic_top_price"/>
        </td>
      </tr>
      <tr class="trtxt">
        <td class="t1">多图文第二条价格：</td>
        <td class="t2 fl">
            <span data-bind="text: multi_graphic_second_price" style="color: red"></span> 元（可在“账号管理”页面修改）
            <input type="hidden"  name="multi_graphic_second_price" data-bind="value:multi_graphic_second_price"/>
            <input type="hidden"  name="multi_graphic_hard_second_price" data-bind="value:multi_graphic_second_price"/>
        </td>
      </tr>
      <tr class="trtxt">
        <td class="t1">多图文其他位置价格：</td>
        <td class="t2 fl">
            <span data-bind="text: multi_graphic_other_price" style="color: red"></span> 元（可在“账号管理”页面修改）
            <input type="hidden"  name="multi_graphic_other_price" data-bind="value:multi_graphic_other_price"/>
            <input type="hidden"  name="multi_graphic_hard_other_price" data-bind="value:multi_graphic_other_price"/>
            <!--内容价现在在这儿-->
            <input type="hidden"  name="content_price" data-bind="value:content_price"/>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>头像：</strong></span></td>
        <td class="t2">
            <div class="fl uploadTxt" id="uploadTouXiang" data-bind="plupload: screen_portrait">
                <div class="fl">
                    <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                </div>
                <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_avatar_desc">图例&nbsp;</a>
                <ul class="js_list">
                </ul>
                <input type="hidden" data-bind="value: screen_portrait" name="uploadImgAvatar">
            </div>
            <p data-bind="css: screen_portrait.error() ? 'error' : 'correct', text: screen_portrait.error()" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>二维码：</strong></span></td>
        <td class="t2">
            <div class="fl uploadTxt" data-bind="plupload: screen_shot_qr_code">
                <div class="fl">
                    <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                </div>
                <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_QR_code_desc">图例&nbsp;</a>
                <ul class="js_list">
                </ul>
                <input type="hidden" data-bind="value: screen_shot_qr_code" name="uploadImgQrCode">
            </div>
            <p data-bind="css: screen_shot_qr_code.error() ? 'error' : 'correct', text: screen_shot_qr_code.error()" class="cBox_tip"></p>
        </td>
      </tr>
      <tr>
        <td class="t1"><span><i>*</i><strong>粉丝截图：</strong></span></td>
        <td class="t2">
            <div class="fl uploadTxt" data-bind="plupload: screen_shot_followers">
                <div class="fl">
                    <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                </div>
                <a href="javascript:;" title="点击查看详情" class="btn_right_side_desc js_followers_desc">图例&nbsp;</a>
                <ul class="js_list">
                </ul>
                <input type="hidden" data-bind="value: screen_shot_followers" name="uploadImgFollowers">
            </div>
            <p data-bind="css: screen_shot_followers.error() ? 'error' : 'correct', text: screen_shot_followers.error()" class="cBox_tip"></p>
        </td>
      </tr>
	  
          <tr>
            <td class="t1"><span><i>*</i><strong>帐号类型：</strong></span></td>
            <td class="t2">
				<input data-bind="checked: accountType" type="radio" value="1" name="accountType"/>名人
				<input data-bind="checked: accountType" type="radio" value="2" name="accountType"/>草根
				<p data-bind="text: accountType.error(), css: accountType.error() ? 'error' : 'correct'" class="cBox_tip"></p>
          </tr>
		  <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>名人职业：</strong></span></td>
            <td class="t2">
                <select name="occupation" data-bind="getTags: occupation, options: occupation.occupation_option, optionsValue: 'val', optionsText: 'name', value: occupation"></select>
				<p data-bind="text: occupation.notice_text, css: occupation.notice_css" class="cBox_tip" id="occupation_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>媒体领域：</strong></span></td>
            <td class="t2">
                <select name="field" data-bind="getTags: field, options: field.field_option, optionsValue: 'val', optionsText: 'name', value: field"></select>
				<p data-bind="html: field.notice_text, css: field.notice_css" class="cBox_tip" id="field_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>地方名人/媒体：</strong></span></td>
            <td class="t2">
                <select name="cirymedia" data-bind="getTags: cirymedia, options: cirymedia.cirymedia_option, optionsValue: 'val', optionsText: 'name', value: cirymedia"></select>
				<p data-bind="html: cirymedia.notice_text, css: cirymedia.notice_css" class="cBox_tip" id="cirymedia_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>兴趣标签：</strong></span></td>
            <td class="t2">
                <select name="interest" data-bind="getTags: interest, options: interest.interest_option, optionsValue: 'id', optionsText: 'name', value: interest"></select>
				<p data-bind="html: interest.notice_text, css: interest.notice_css" class="cBox_tip" id="interest_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>配合度：</strong></span></td>
            <td class="t2">
                <select name="coordination" data-bind="options: coordination_option, optionsValue: 'id', optionsText: 'name', value: coordination"></select>
				<p data-bind="html: coordination.notice_text, css: coordination.notice_css" class="cBox_tip" id="coordination_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="celeprityindex" style="display:none">
            <td class="t1"><span><i>*</i><strong>是否支持原创：</strong></span></td>
            <td class="t2">
                <select name="originality" data-bind="options: originality_option, optionsValue: 'id', optionsText: 'name', value: originality"></select>
				<p data-bind="html: originality.notice_text, css: originality.notice_css" class="cBox_tip" id="originality_error" style="padding-right:47px;"></p>
            </td>
          </tr>
		  
          <tr class="grassroots" style="display:none">
            <td class="t1"><span><i>*</i><strong>常见分类：</strong></span></td>
            <td class="t2">
                <select name="common" data-bind="getTags: common, options: common.common_option, optionsValue: 'val', optionsText: 'name', value: common"></select>
				<p data-bind="html: common.notice_text, css: common.notice_css" class="cBox_tip" id="common_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="grassroots" style="display:none">
            <td class="t1"><span><i>*</i><strong>地区：</strong></span></td>
            <td class="t2">
                <select name="cirymedia" data-bind="getTags: cirymedia, options: cirymedia.cirymedia_option, optionsValue: 'val', optionsText: 'name', value: cirymedia"></select>
				<p data-bind="html: cirymedia.notice_text, css: cirymedia.notice_css" class="cBox_tip" id="cirymedia_error" style="padding-right:47px;"></p>
            </td>
          </tr>
          <tr class="grassroots" style="display:none">
            <td class="t1"><span><i>*</i><strong>粉丝性别：</strong></span></td>
            <td class="t2">
                <select name="sex" data-bind="getTags: sex, options: sex.sex_option, optionsValue: 'val', optionsText: 'name', value: sex"></select>
				<p data-bind="html: sex.notice_text, css: sex.notice_css" class="cBox_tip" id="sex_error" style="padding-right:47px;"></p>
            </td>
          </tr>
    </table>
    </form>
</div>