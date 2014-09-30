<div class="cBox mid-batch pr l" style="width:100%;">
    <form method="post" action="/Media/SocialAccount/submitaccount" name="accountRegistor" id="accountRegistor">
        <table class="tab01-inten l table-xwmt">
            <tr>
                <td class="t1"><span><i>*</i><strong>账号名：</strong></span></td>
                <td class="t2">
                    <input name="weibo_id" data-bind="validThreeState: weibo_id, value: weibo_id" type="text" class="text text-error" />
                    <p data-bind="html: weibo_id.notice_text, css: weibo_id.notice_css" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>频道：</strong></span></td>
                <td class="t2">
                    <input name="channel_name" data-bind="validThreeState: channel_name, value: channel_name" type="text" class="text text-error" />
                    <p data-bind="html: channel_name.notice_text, css: channel_name.notice_css" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>价格：</strong></span></td>
                <td class="t2">
                    <input data-bind="placeholder: '请输入正整数', validThreeState: tweet_price, value: tweet_price" type="text" class="text text-error" name="tweet_price" maxlength="10"/>
                    <p data-bind="html: tweet_price.notice_text, css: tweet_price.notice_css" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>标题：</strong></span></td>
                <td class="t2">
                    <input name="account_title" data-bind="validThreeState: account_title, value: account_title" type="text" class="text text-error" />
                    <p data-bind="html: account_title.notice_text, css: account_title.notice_css" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>地区：</strong></span></td>
                <td class="t2 tl">
                    <div class="" data-bind="area: area_id">
                        <input type="hidden" data-bind="value: area_id" name="area_id"/>
                        <select data-bind="options: area_id.country_option, optionsValue: 'id', optionsText: 'name', value: area_id.country, optionsCaption: '请选择'"></select>
                        <select data-bind="options: area_id.province_option, value: area_id.province, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                        <select data-bind="options: area_id.city_option, value: area_id.city, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                        <select data-bind="options: area_id.district_option, value: area_id.district, optionsValue: 'id', optionsText: 'name', optionsCaption: '请选择'"></select>
                    </div>
                    <p data-bind="text: area_id.error(), css: area_id.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>入口：</strong></span></td>
                <td class="t2">
                    <input data-bind="validThreeState: account_entry, value: account_entry" type="text" class="text text-error" name="account_entry"/>
                    <p data-bind="html: account_entry.notice_text, css: account_entry.notice_css" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>新闻源：</strong></span></td>
                <td class="t2 t t15 tl">
                    <input data-bind="checked: is_news_source" type="radio" value="1" name="is_news_source"/>是
                    <input data-bind="checked: is_news_source" type="radio" value="2" name="is_news_source"/>否
                    <p data-bind="text: is_news_source.error(), css: is_news_source.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>网址收录：</strong></span></td>
                <td class="t2 t t15 tl">
                    <input data-bind="checked: is_web_site_included" type="radio" value="1" name="is_web_site_included"/>是
                    <input data-bind="checked: is_web_site_included" type="radio" value="2" name="is_web_site_included"/>否
                    <p data-bind="text: is_web_site_included.error(), css: is_web_site_included.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>是否需要来源：</strong></span></td>
                <td class="t2 t t15 tl">
                    <input data-bind="checked: is_need_source" type="radio" value="1" name="is_need_source"/>是
                    <input data-bind="checked: is_need_source" type="radio" value="2" name="is_need_source"/>否
                    <p data-bind="text: is_need_source.error(), css: is_need_source.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>案例地址：</strong></span></td>
                <td class="t2">
                    <input placeholder="填写案例地址" data-bind="noticeImg: platform_url_img, validThreeState: url, value: url" type="text" class="text text-error" name="url"/>
                    <p data-bind="html: url.notice_text, css: url.notice_css" class="cBox_tip" id="url"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>周末能否发稿：</strong></span></td>
                <td class="t2 t t15 tl">
                    <input data-bind="checked: is_press_weekly" type="radio" value="1" name="is_press_weekly"/>是
                    <input data-bind="checked: is_press_weekly" type="radio" value="2" name="is_press_weekly"/>否
                    <p data-bind="text: is_press_weekly.error(), css: is_press_weekly.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>能否带文本链接：</strong></span></td>
                <td class="t2 t t15 tl">
                    <input data-bind="checked: is_text_link" type="radio" value="1" name="is_text_link"/>是
                    <input data-bind="checked: is_text_link" type="radio" value="2" name="is_text_link"/>否
                    <p data-bind="text: is_text_link.error(), css: is_text_link.error() ? 'error' : 'correct'" class="cBox_tip"></p>
                </td>
            </tr>
            <tr>
                <td class="t1"><span><i>*</i><strong>上传媒体截图：</strong></span></td>
                <td class="t2">
                    <div class="fl uploadTxt" data-bind="plupload: screen_shot_media">
                        <div class="fl">
                            <a class="btn_small_strong js_btn" href="javascript:;"><span class="btn_wrap">上传</span></a>
                        </div>
                        <ul class="js_list"></ul>
                        <input type="hidden" data-bind="value: screen_shot_media" name="uploadImgMedia">
                    </div>
                    <p data-bind="css: screen_shot_media.error() ? 'error' : 'correct', text: screen_shot_media.error()" class="cBox_tip">
                </td>
            </tr>
        </table>
    </form>
</div>