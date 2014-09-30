define(function(require) {
    var ko = require('knockout');
    var platformCfg = require('./platform_cfg.js');
    var PlatformModel = require('./platform_model.js');
    var ImageModel = require('./image_model.js');
    var RateModel = require('./rate_model.js');
    require('./area.js');
    require('./knockout/knockout_utility.js');

    function object2Array(obj) {
        return obj ? $.map(obj, function(v, k) {return {id: k, name: v}}) : {};
    }

    function keepPrecision(decimal, precision){
        var enlarger = precision ? Math.pow(10, precision) : null;
        return (enlarger && decimal) ? Math.round(decimal * enlarger)/enlarger : 0;
    }

    function changeTwoDecimal(x)
    {
        var f_x = Math.round(x*100)/100;
        var s_x = f_x.toString();
        var pos_decimal = s_x.indexOf('.');
        if (pos_decimal < 0)
        {
            pos_decimal = s_x.length;
            s_x += '.';
        }
        while (s_x.length <= pos_decimal + 2)
        {
            s_x += '0';
        }
        return s_x;
    }

    //计算服务费之后的价格
    function calcPriceWithServiceFees(amount, rate, divingLine){
        var preg = /^[0-9]*(\.[0-9]{1,2})?$/;
        rate = parseFloat(rate,10);

        if(amount && !preg.test(amount)){
            return '不能超过2位小数';
        }
        if(amount > divingLine){
            return changeTwoDecimal(amount * (1-rate));
        }
        return amount;
    }

    function getOptionName(id, options, defaultName){
        var name = defaultName || '数据不足';
        $.each(options, function(i, e){
            if(e.id == id){
                name = e.name;
            }
        });
        return name;
    }

    function convertPhpTimestamp(iPhpTimeStamp, sFormat){
        var format = sFormat || 'Y-m-d H:i';
        return W.util.formatDate(new Date(iPhpTimeStamp * 1000), format);
    }

    /**
     * 对应用户的model数据
     * @constructor
     */
    function AccountModel(data) {
        var self = this;
        self.service_fees_cfg = {rate: '0.05', diving_line: 100};
        self.true_name = ko.observable(data.true_name);
        self.account_avatar = ko.observable(data.account_avatar).extend({weixin_image:''});
        self.account_avatar_be_identified = ko.observable(data.account_avatar_be_identified);
        self.account_avatar_last_updated_time =
            ko.observable(data.account_avatar_last_updated_time).extend({phpTsToDate:''});
        self.account_avatar_update_time = ko.observable(data.account_avatar_update_time).extend({phpTsToDate:''});
        self.friend_desc = ko.observable(data.friend_desc);
        self.weibo_link = ko.observable(data.weibo_link);
        self.account_advantage = ko.observable(data.account_advantage);
        self.release_history = ko.observable(data.release_history).extend({weixin_image:''});
        self.release_history_update_time = ko.observable(data.release_history_update_time).extend({phpTsToDate:''});
        self.release_history_be_identified = ko.observable(data.release_history_be_identified);
        self.release_history_last_updated_time =
            ko.observable(data.release_history_last_updated_time).extend({phpTsToDate:''});
        self.person_avatar = ko.observable(data.person_avatar).extend({weixin_image:''});
        self.person_avatar_update_time = ko.observable(data.person_avatar_update_time).extend({phpTsToDate:''});
        self.person_avatar_be_identified = ko.observable(data.person_avatar_be_identified);
        self.person_avatar_last_updated_time =
            ko.observable(data.person_avatar_last_updated_time).extend({phpTsToDate:''});
        self.edu_error_display = ko.observable(data.edu_error_display);
        //通用的是否选择框
        self.yes_no_options = ko.observableArray([
            {id: 1, name:'是'},
            {id: 0, name:'否'}
        ]);
        //通用的是否未知选择框
        self.yes_no_three_options = ko.observableArray([
            {id: 1, name:'是'},
            {id: 0, name:'否'},
            {id: 3, name:'未知'}
        ]);
        //通用的是否数据不足选择框，采集类
        self.yes_no_lack_data_options = ko.observableArray([
            {id: -1, name:'数据不足'},
            {id: 1, name:'是'},
            {id: 0, name:'否'}
        ]);

        //粉丝认证情况
        self.follower_options =  ko.observableArray([
            {id: 1, name:'是'},
            {id: 0, name:'否'},
            {id: 10, name:'拒绝认证'}
        ]);

        //地域认证情况
        self.area_options =  ko.observableArray([
            {id: 1, name:'是'},
            {id: 0, name:'否'},
            {id: 10, name:'拒绝认证'}
        ]);

        /***
         * 账号类型改变，联动利润率
         */
        self.refreshProfitGrade = function(profit_grade){
            if(null == profit_grade){
                $.restPost('/account/manager/getdefaultprofitgradeid',
                    {
                        'weibo_type': self.weibo_type(),
                        'account_type': self.type()
                    },
                    function(res, data){
                        self.profit_grade_value(data.profit_grade);
                    }
                );
            }
            else{
                self.profit_grade_value(profit_grade);
            }
        };

        /**
         * B端创建用户
         * @param formElement
         */
        self.create = function(formElement){
            var submitValues = {'user_id':self.user_id()};
            $.map(formElement, function(e) {
                    if(e.name && e.value){
                        submitValues[e.name] = e.value;
                    }
                }
            );
            console.log(submitValues);
            $.restPost('/account/manager/addbeta', submitValues, function(res, data){
                console.log(res,data);
            });
        };

        //自动抓取信息
        self.autoCatchAttributes = function(){
            $.post('/account/manager/autocatch',
                {
                    'weibo_type': self.weibo_type(),
                    'weibo_id': self.weibo_id()
                },
                function(data){
                    var jsonData = $.parseJSON(data);
                    if(jsonData.status){
                        self.weibo_name(jsonData.weibo_name);//微博名
                        self.followers_count(jsonData.followers_count);//粉丝数
                        self.url(jsonData.url);//微博链接
                        self.face_url(jsonData.face_url);//头像
                        self.weibo_id(jsonData.weibo_id);
                    }else{
                        /**
                         * TODO - 不直接提示错误信息
                         */
                        alert(jsonData.msg);
                    }
                }
            );
        };

        //账号ID
        self.account_id = data.account_id;
        //更新的链接地址
        self.update_link = ko.computed(function(){
            return '/account/manager/update/account_id/' + self.account_id;
        });
        //预付的链接地址
        self.billapply_link = ko.computed(function(){
            return '/bill/billapply/index/account_id/' + self.account_id;
        });
        //策略的链接地址
        self.tactics_link = ko.computed(function(){
            return '/account/manager/tactics/account_id/' + self.account_id;
        });
        //包月的链接地址
        self.monthlylist_link = ko.computed(function(){
            return '/monthly/manager/monthlylist/accountId/' + self.account_id;
        });

        //用户ID
        self.user_id = ko.observable(data.user_id);
        //用户头像
        self.face_url = ko.observable(data.face_url);

        //微博平台
        self.weibo_type = ko.observable(parseInt(data.weibo_type));
        self.platform_model = ko.computed(function(){
            var weiboType = self.weibo_type();
            var res = {};
            $.each(platformCfg, function(e, f){
                if(f.pid == weiboType){
                    res = new PlatformModel(f);
                }
            });
            return res;
        });

        //认证名称展示的函数
        self.verify_title = ko.computed(function (){
            var weibo_type = self.weibo_type();
            var verify_data = {};
            if(weibo_type == 23){
              return {'status_title':"好友数视频认证",'time_title':"好友数视频认证更新时间"};
            }
            return {'status_title':"粉丝数目认证",'time_title':"粉丝截图更新时间"};
        });


        //微博Id
        self.weibo_id = ko.observable(data.weibo_id || '');
        self.weibo_id_name = ko.computed(function(){
            switch (self.weibo_type()){
                case 3:
                    return '微博ID（微信号）';
                default :
                    return '微博ID';
            }
        });

        //微博名称
        self.weibo_name = ko.observable(data.weibo_name);

        if(data.last_active_time ){
            self.last_active_time = ko.observable(data.last_active_time).extend({phpTsToDate:''});
        }else{
            self.last_active_time = ko.observable(data.last_active_time);
        }
        //类型
        self.type = ko.observable(data.type);
        self.type_options = ko.observableArray([
            {id: 1, name: '草根'},
            {id: 2, name: '个人'},
            {id: 4, name: '媒体'},
            {id: 5, name: '名人'},
            {id: 3, name: '未知'}
        ]);
        self.type_name = function(){
            return getOptionName(self.type(), self.type_options());
        }();

        //账号类型
        self.account_type = ko.observable(data.type);
        self.account_type_options = ko.observableArray([
            {id: 1, name: '草根'},
            {id: 2, name: '红人'},
            {id: 4, name: '媒体'}
        ]);
        self.account_type_name = function(){
            return getOptionName(self.account_type(), self.account_type_options());
        }();

        //微信原始id
        self.weixin_original_id = ko.observable(data.weixin_original_id);

        //微淘会员名
        self.weitao_taobao_id = ko.observable(data.weitao_taobao_id);

        //粉丝数
        self.followers_count = ko.observable(data.followers_count);
        //真粉率
        self.audience_realfans_ratio = function(){
            return (data.audience_realfans_ratio!=""&&data.audience_realfans_ratio>=0)?((data.audience_realfans_ratio*1).toFixed(2)):'--';
        }();

        self.url = ko.observable(data.url);

        //节点手机号
        self.account_phone = ko.observable(data.account_phone);

        //粉丝数是否被认证
        self.follower_be_identified = ko.observable(data.follower_be_identified);

        //粉丝数认证展示
        self.follower_be_identified_display = function (){
            var follower_be_identified = self.follower_be_identified();
            var options = {'1':'是','2':'否','10':'拒绝认证'}
            return options[follower_be_identified];
        }();

        //粉丝认证时间
        self.follower_be_identified_time = ko.observable(data.follower_be_identified_time).extend({phpTsToDate:''});
        //粉丝认证时间展示
        self.follower_be_identified_time_display = function(){
            if(self.follower_be_identified() == 1){
                return convertPhpTimestamp(self.follower_be_identified_time());
            }
            return '';
        }();

        //是否审核
        self.is_verify = ko.observable();
        self.is_verify_options = ko.observableArray(function(){
                var res = [
                    {id:1, name:'待审核'},
                    {id:2, name:'审核通过'},
                    {id:3, name:'审核失败'}
                ];
                //审核失败不能修正成待审核，审核成功后将不能再修改成其他
                if(self.is_verify()  == 3){
                    res.shift();
                }else if(self.is_verify() == 2){
                    res.shift();
                    res.pop();
                }
                return res;
            }()
         );
        self.is_verify(data.is_verify);
        self.is_verify_name = function(){
            return getOptionName(self.is_verify(), self.is_verify_options());
        }();

        //审核失败原因
        self.account_regist_reason = ko.observable(data.account_regist_reason);

        //是否公开
        self.is_open = ko.observable(data.is_open);
        self.is_open_name = function(){
            return getOptionName(self.is_open(), self.yes_no_options());
        }();
        //是否独家
        self.is_exclusive = ko.observable(data.is_exclusive);
        self.is_exclusive_name = function(){
            return getOptionName(self.is_exclusive(), self.yes_no_options());
        }();

        //已联系
        self.is_contacted = ko.observable(data.is_contacted || 1);

        //利润等级
        self.profit_grade_value = ko.observable('未指定');
        self.profit_rate = data.profit_rate;
        self.profit_rate_display = function(){
            return self.profit_rate * 100 + "%";
        }();

        //是否流单过多下架
        self.is_flowunit = ko.observable(data.is_flowunit);
        self.is_flowunit_name = function(){
            return getOptionName(self.is_flowunit(), self.yes_no_options());
        }();

        //流单过多下架原因
        self.flowunit_reason = ko.observable(data.flowunit_reason);

        //博主年龄
        self.age = ko.observable(data.age);

        //博主性别
        self.gender = ko.observable(data.gender);
        self.gender_options = ko.observable(function(){
            //当是美丽说账号的时候博主性别只允许是女
            return self.weibo_type() == 6 ?
                [
                {id:2, name:'女'}
                ]:
                [
                    {id:1, name:'男'},
                    {id:2, name:'女'},
                    {id:3, name:'未知/其他'}
                ]

        }());
        self.gender_name = function(){
            return getOptionName(self.gender(), self.gender_options());
        }();

        //博主领域
        self.profession_type = ko.observable(data.profession_type);

        //此处定义一个map用来根据id取名
/*        self.profession_type_map = {
            '1':'白领',
            '2':'模特',
            '5':'学生',
            '8':'电商',
            '9':'企业家',
            '10':'自媒体',
            '11':'网络红人',
            '12':'个体商户',
            '13':'自由职业',
            '14':'未知',
            '7':'其他'
        }*/
        //此处应该注意，以下的职业必须参照babysitter_enum_account_profession_type表格，flag为1表示可用。
        self.profession_type_options = ko.observable([
            {id:1, name:'白领'},
            {id:2, name:'模特'},
            {id:5, name:'学生'},
            {id:8,name:'电商'},
            {id:9,name:'企业家'},
            {id:10,name:'自媒体'},
            {id:11,name:'网络红人'},
            {id:12,name:'个体商户'},
            {id:13,name:'自由职业'},
            {id:15,name:'微商推广'},
            {id:14,name:'未知'},
            {id:7, name:'其他'}
        ]);

        self.profession_type_name = function(){
            return getOptionName(self.profession_type(), self.profession_type_options());
        }();

        self.edu_degree = ko.observable(data.edu_degree);

/*        //定义职业map
        self.edu_degree_map = {
            '1':'博士',
            '2':'硕士',
            '3':'本科',
            '4':'大专',
            '5':'其他'
        };*/

        self.edu_degree_options = ko.observable([
            {id:1, name:'博士'},
            {id:2, name:'硕士'},
            {id:3, name:'本科'},
            {id:4, name:'大专'},
            {id:5, name:'其他'}
        ]);

        self.edu_degree_name = function(){
            return getOptionName(self.edu_degree(), self.edu_degree_options());
        }();
        //博主领域其他信息
        self.profession_other_info = ko.observable(data.profession_other_info);
        self.edu_degree_name=ko.computed(function(){return getOptionName(self.edu_degree(),self.edu_degree_options())},this);
        self.gender_name=ko.computed(function(){return getOptionName(self.gender(),self.gender_options())},this);
        self.profession_name=ko.computed(function(){return getOptionName(self.profession_type(),self.profession_type_options())},this);
        self.areaFont=ko.observable(data.areaFont);
        //受众性别
        self.audience_gender = ko.observable(data.audience_gender || -1);

        //性别map
/*        self.gender_map = {
            '1':'男',
            '2':'女',
            '3':'泛',
            '-1':'数据不足'
        }*/

        self.audience_gender_options = ko.observable([
            {id:1, name:'男'},
            {id:2, name:'女'},
            {id:3, name:'泛'},
            {id:-1, name:'数据不足'}
        ]);
        self.audience_gender_name = function(){
            return getOptionName(self.audience_gender(), self.audience_gender_options());
        }();
        //受众女性性别
        self.audience_female_ratio = data.audience_female_ratio;
        self.audience_female_ratio_display = function(){
            var ratio = self.audience_female_ratio;
            return (!ratio || ratio==-1.00) ? "数据不足" : keepPrecision(ratio * 100, 2)+"%";
        }();

        //是否达人
        self.is_daren = ko.observable(data.is_daren || -1);
        self.is_daren_name = function(){
            return getOptionName(self.is_daren(), self.yes_no_lack_data_options());
        }();
        //是否加V
        self.is_vip = ko.observable(data.is_vip || -1);
        self.is_vip_name = function(){
            return getOptionName(self.is_vip(), self.yes_no_lack_data_options());
        }();

        //是否蓝V
        self.is_bluevip = ko.observable(data.is_bluevip || -1);
        self.is_bluevip_name = function(){
            return getOptionName(self.is_bluevip(), self.yes_no_lack_data_options());
        }();
        //是否屏蔽
        self.is_shield = ko.observable(data.is_shield);
        self.is_shield_name = function(){
            return getOptionName(self.is_shield(), self.yes_no_options());
        }();

        //是否开启私信
        self.is_private_message_enabled = ko.observable(data.is_private_message_enabled);
        self.is_private_message_enabled_name = function(){
            return getOptionName(self.is_private_message_enabled(), self.yes_no_options())
        }();

        //是够可发淘宝和天猫链接
        self.is_enable_micro_task = ko.observable(data.is_enable_micro_task || 1);
        self.is_enable_micro_task_options = ko.observable([
            {id:1, name:'是'},
            {id:0, name:'否'},
            {id:3, name:'屏蔽可申报'}
        ]);

        //是否屏蔽
        self.is_closed = ko.observable(data.is_closed);
        self.is_closed_name = function(){
            return getOptionName(self.is_closed(), self.yes_no_options());
        }();

        //是否签约
        self.is_sign = ko.observable(data.is_sign);
        //签约备注
        self.sign_note = ko.observable(data.sign_note);

        //是否可接单图文
        self.order_info_weixin_allow_dantuwen = ko.observable(data.order_info_weixin_allow_dantuwen || 1);
        self.order_settings = data.order_settings;
        self.weixin_is_refuse_dantuwen = function(){
            if(self.weibo_type() == 3){
                return parseInt(self.order_settings)&1 ? '否' : '是';
            }
            return '-';
        }();

        //是否接单
        self.is_allow_order = ko.observable(data.is_allow_order || 2);
        self.is_alloworder_name = function(){
            return getOptionName(self.is_allow_order(), self.yes_no_options());
        }();
        //不接单原因
        self.not_allow_order_reason = ko.observable(data.not_allow_order_reason || '未知原因');

        //是否上架
        self.is_online = ko.observable(data.is_online || 2);
        self.is_online_name = function(){
            return getOptionName(self.is_online(), self.yes_no_options());
        }();
        //不上架原因
        self.not_online_reason = ko.observable(data.not_online_reason || '未知原因');

        //是否名人
        self.is_famous = ko.observable(data.is_famous);

        function calcPriceWithFollowerCount(price, followerCount, weibo_type){
            if(!price){
                return 0;
            }
            var res = 0;
            if(!followerCount || followerCount<1){
                if (weibo_type == 23) {
                    res = price / 1000;
                } else {
                    res = price / 10000;
                }
            }else{
                if (weibo_type == 23) {
                    res = price / followerCount;
                } else {
                    res = price * 10000 / followerCount;
                }
            }
            return keepPrecision(res, 2);
        }

        //硬广转发价
        self.retweet_price = ko.observable(data.retweet_price).extend({numeric: 2});;
        self.retweet_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.retweet_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        //硬广转发单价
        self.retweet_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.retweet_price(), self.followers_count(), self.weibo_type());
        });

        //硬广直发价
        self.tweet_price = ko.observable(data.tweet_price).extend({numeric: 2});;
        self.tweet_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.tweet_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.tweet_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.tweet_price(), self.followers_count(), self.weibo_type());
        });

        //软广转发价
        self.soft_retweet_price = ko.observable(data.soft_retweet_price).extend({numeric: 2});;
        self.soft_retweet_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.soft_retweet_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.soft_retweet_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.soft_retweet_price(), self.followers_count());
        });

        //软广直发价
        self.soft_tweet_price = ko.observable(data.soft_tweet_price).extend({numeric: 2});
        self.soft_tweet_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.soft_tweet_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.soft_tweet_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.soft_tweet_price(), self.followers_count());
        });

        //内容软广价格
        self.content_price = ko.observable(data.content_price).extend({numeric: 2});
        self.content_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.content_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });

        self.content_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.content_price(), self.followers_count());
        });

        //内容硬广价格
        self.content_hard_price = ko.observable(data.content_hard_price).extend({numeric: 2});
        self.content_hard_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.content_hard_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });

        self.content_hard_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.content_hard_price(), self.followers_count());
        });
        //原创直发报价
        self.original_zhifa_price = ko.observable(data.original_zhifa_price).extend({numeric: 2});
        self.original_zhifa_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.original_zhifa_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.original_zhifa_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.original_zhifa_price(), self.followers_count());
        });

        //对外报价
        self.quoted_price = ko.observable(data.quoted_price).extend({numeric: 2});

        //分享报价
        self.quoted_tweet_price = ko.observable(data.quoted_tweet_price).extend({numeric: 2})

        //三转值（新）
        self.effect_score_new = ko.observable(data.effect_score_new || -1).extend({numeric: 2});

        //媒介备注
        self.content = ko.observable(data.content);
        //手工标签
        self.hand_tags = ko.observable(data.hand_tags);
        self.is_hand_tags_required = ko.computed(function(){
            var type = self.type();
            var weiboType = self.weibo_type();
            if(weiboType == 23)
                return false;
            return type == 2 || type == 3 || (type == 1 && weiboType == 6 ) || (type != "" && weiboType == 5 );
        });

        //内容标签
        self.content_tags = ko.observable(data.content_tags);
        //关键字
        self.keywords = ko.observable(data.keywords);
        //只接单类型
        self.only_accept = ko.observable(data.only_accept);

        //电话
        self.telephone = ko.observable(data.telephone);
        //手机号
        self.cell_phone = ko.observable(data.cell_phone);
        //qq
        self.qq = ko.observable(data.qq);
        //msn
        self.msn = ko.observable(data.msn);
        //weixin_contact
        self.weixin_contact = ko.observable(data.weixin_contact);
        //邮箱
        self.email = ko.observable(data.email);
        //个人简介
        self.introduction = ko.observable(data.introduction);
        //更新时间
        self.updated_time = ko.observable(data.updated_time);
        //粉丝截图
        self.screen_shot_followers = ko.observable(data.screen_shot_followers).extend({weixin_image:''});

        //粉丝截图更新时间
        self.screen_shot_followers_last_updated_time =
            ko.observable(data.screen_shot_followers_last_updated_time).extend({phpTsToDate:''});
        self.screen_shot_followers_last_updated_time_display = function(){
            if(self.follower_be_identified() == 1){
                return convertPhpTimestamp(self.screen_shot_followers_last_updated_time());
            }
            return '';
        }();

        //头像截图
        self.screen_portrait = ko.observable(data.screen_portrait).extend({weixin_image:''});
        self.screen_portrait_update_time = ko.observable(data.screen_portrait_update_time).extend({phpTsToDate:''});
        //二维码截图
        self.screen_shot_qr_code = ko.observable(data.screen_shot_qr_code).extend({weixin_image:''});
        self.screen_shot_qr_code_update_time = ko.observable(data.screen_shot_qr_code_update_time).extend({phpTsToDate:''});
        //数据截图
        self.screen_shot_info = ko.observable(data.screen_shot_info).extend({weixin_image:''});
        self.screen_shot_info_update_time = ko.observable(data.screen_shot_info_update_time).extend({phpTsToDate:''});

        //朋友圈的地域认证和地域认证时间
        self.area_be_identified = ko.observable(data.area_be_identified);
        self.area_be_identified_time =  ko.observable(data.area_be_identified_time).extend({phpTsToDate:''});
        self.area_be_identified_time_display =  function(){
            if(self.area_be_identified() == 1){
                return convertPhpTimestamp(self.area_be_identified_time());
            }
            return '';
        }();

       //评分模块
        self.rating = new RateModel(data);
        //是否假号
        self.is_fake = data.is_fake;
        self.is_fake_name = function(){
            return getOptionName(self.is_fake, self.yes_no_options());
        }();
        //账号级别
        self.level = data.level;
        self.level_options = [
            {id:1, name:'普通'},
            {id:2, name:'高潜'},
            {id:3, name:'核心'},
            {id:4, name:'大号'}
        ];
        self.level_name = function(){
            return getOptionName(self.level, self.level_options);
        }();
        //月订单量
        self.orders_monthly = data.orders_monthly;
        //周订单量
        self.orders_weekly = data.orders_weekly;
        //周订单金额
        self.orders_weekly_amount = data.orders_weekly_amount;
        //月订单金额
        self.orders_monthly_amount = data.orders_monthly_amount;
        //是否合作
        self.is_sensitive = data.is_sensitive;
        self.is_sensitive_name = function(){
            return self.is_sensitive==1 ? "暂不合作" : "继续合作";
        }();
        //是否自助添加
        self.self_register = ko.observable(data.self_register);
        self.self_register_name = function(){
            return getOptionName(self.self_register(), self.yes_no_options());
        }();
        //是否设置了订单托管
        self.is_auto_send = ko.observable(data.is_auto_send);
        self.is_auto_send_name = function(){
            return getOptionName(self.is_auto_send(), self.yes_no_options());
        }();
        //订单授权状态
        self.is_auth = ko.observable(data.is_auth);
        self.is_auth_options = [
            {id:1, name:'授权正常'},
            {id:2, name:'未授权'},
            {id:3, name:'即将过期'},
            {id:4, name:'授权过期'}
        ];
        self.is_auth_name = function(){
            return getOptionName(self.is_auth(), self.is_auth_options);
        }();
        self.created_time = data.created_time;
        self.created_time_display = function(){
            return convertPhpTimestamp(self.created_time);
        }();
        //主账号标识
        self.identity_name = data.identity_name;
        //管理员标识
        self.admin_user_name = data.admin_user_name;
        //管理员真实姓名
        self.admin_real_name = data.admin_real_name;
        //管理员id
        self.owner_admin_id = data.owner_admin_id;
        //用户组id - 标识能否登录C端
        self.user_group_id = data.user_group_id;
        //是否C端注册
        self.is_cregister = data.is_cregister;
        self.is_cregister_name = function(){
            return getOptionName(self.is_cregister, self.yes_no_options());
        }();
        //新三转
        self.effect_score_new = data.effect_score_new;
        //系数
        self.effect_coefficient = data.effect_coefficient;
        //号力新
        self.account_strength_new = function(){
            return keepPrecision(
                self.effect_score_new * self.effect_coefficient * self.followers_count() / 10000 , 2);
        }();
        //地域id
        if(data.area_id==0) data.area_id = '';
        self.area_id = ko.observable(data.area_id);
        self.country_option = [{id:1, name: '中国'}];

        //派单员备注
        self.comment = data.comment;

        //地域名称
        self.area_name = data.area_name;
        //当日订单数其他页面用
        self.today_order_times = data.today_order_times || 0;
        //当日订单数派单页面用
        self.orders_daily = data.orders_daily || 0;
        //是否接软硬广
        self.tactics_tuan = data.tactics_tuan;
        self.tactics_tuan_display = function(){
            return (!self.tactics_tuan || self.tactics_tuan.Tuan) ? '是' : '否';
        }();
        //接单上限
        self.tactics_period_max = data.tactics_period_max;
        self.tactics_period_max_display = function(){
            return self.tactics_period_max ? '日/' + self.tactics_period_max.orderMax : '无';
        }();
        //暂离
        self.tactics_leave = data.tactics_leave;
        self.tactics_leave_display = function(){
            var leave = self.tactics_leave;
            if(leave){
                if(leave.type == 'other'){
                    return convertPhpTimestamp(leave.leftTime) + '/' + convertPhpTimestamp(leave.backTime) + '/' + leave.type;
                }else if(leave.type == 'every_day'){
                    return leave.leftTime + '/' + leave.backTime + '/' + leave.type;
                }else if(leave.type == 'every_week'){
                    return leave.leftTime + '/' + leave.backTime + '/' + leave.type + '/' + leave.weeks;
                }
            }
            return '无';
        }();
        //管理员id
        self.owner_admin_id = data.owner_admin_id;
        //被收藏数目
        self.collection_num = data.collection_num || 0;
        //公司拉黑数目
        self.company_black_num = data.company_black_num;
        //月合格率
        self.order_yield = data.order_yield;
        //月拒单率
        self.pass_order_monthly = data.pass_order_monthly;
        //月流单率
        self.deny_order_monthly = data.deny_order_monthly;
        //分类
        self.content_categories = data.content_categories;
        //好评率
        self.active_score = data.active_score;

        //性别分布男
        self.gender_distribution_male = ko.observable(data.gender_distribution_male).extend({numeric: 2});
        //性别分布女
        self.gender_distribution_female = ko.observable(data.gender_distribution_female).extend({numeric: 2});
        //未知
        self.gender_distribution_unknown = ko.computed(function(){
            var res = 100 - self.gender_distribution_male() - self.gender_distribution_female();
            return res >= 100 ? 0 : res;
        });

        //性别分布是否被认证
        self.gender_distribution_identified = ko.observable(data.gender_distribution_identified).extend({options:''});
        //性别分布被认证时间
        self.gender_distribution_identified_time = ko.observable(data.gender_distribution_identified_time).extend({phpTsToDate:''});
        //性别分布更新时间
        self.gender_distribution_update_time = ko.observable(data.gender_distribution_update_time).extend({phpTsToDate:''});

        //图文打开率文件路径
        self.open_rate_path = ko.observable(data.open_rate_path);
        //图文打开数
        self.open_count = ko.observable(data.open_count);
        //图文打开率
        self.open_rate = ko.computed(function(){
            return self.followers_count() && self.open_count() ?
                keepPrecision(self.open_count() * 100 / self.followers_count(), 2) : 0;
        });

        //图文打开率是否被认证
        self.open_rate_identified = ko.observable(data.open_rate_identified).extend({options:''});
        //图文打开率被认证时间
        self.open_rate_identified_time = ko.observable(data.open_rate_identified_time).extend({phpTsToDate:''});
        //图文打开率更新时间
        self.open_rate_update_time = ko.observable(data.open_rate_update_time).extend({phpTsToDate:''});

        //单图文软广报价
        self.single_graphic_price = ko.observable(data.single_graphic_price).extend({numeric: 2});
        //self.single_graphic_price = ko.observable(data.single_graphic_price);
        self.single_graphic_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.single_graphic_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        //单图文软广报价万粉丝单价
        self.single_graphic_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.single_graphic_price(), self.followers_count());
        });

        //单图文硬广报价
        self.single_graphic_hard_price = ko.observable(data.single_graphic_hard_price).extend({numeric: 2});
        self.single_graphic_hard_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.single_graphic_hard_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        //单图文硬广报价万粉丝单价
        self.single_graphic_hard_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.single_graphic_hard_price(), self.followers_count());
        });

        //多图文第一条软广报价
        self.multi_graphic_top_price = ko.observable(data.multi_graphic_top_price).extend({numeric: 2});
        self.multi_graphic_top_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_top_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_top_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_top_price(), self.followers_count());
        });

        //多图文第一条硬广报价
        self.multi_graphic_hard_top_price = ko.observable(data.multi_graphic_hard_top_price).extend({numeric: 2});
        self.multi_graphic_hard_top_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_hard_top_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_hard_top_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_hard_top_price(), self.followers_count());
        });

        //多图文第二条软广报价
        self.multi_graphic_second_price = ko.observable(data.multi_graphic_second_price).extend({numeric: 2});
        self.multi_graphic_second_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_second_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_second_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_second_price(), self.followers_count());
        });

        //多图文第二条硬广报价
        self.multi_graphic_hard_second_price = ko.observable(data.multi_graphic_hard_second_price).extend({numeric: 2});
        self.multi_graphic_hard_second_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_hard_second_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_hard_second_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_hard_second_price(), self.followers_count());
        });

        //多图文其他软广报价
        self.multi_graphic_other_price = ko.observable(data.multi_graphic_other_price).extend({numeric: 2});
        self.multi_graphic_other_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_other_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_other_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_other_price(), self.followers_count());
        });

        //多图文其他硬广报价
        self.multi_graphic_hard_other_price = ko.observable(data.multi_graphic_hard_other_price).extend({numeric: 2});
        self.multi_graphic_hard_other_price_fees = ko.computed(function(){
            return calcPriceWithServiceFees(
                self.multi_graphic_hard_other_price(), self.service_fees_cfg.rate, self.service_fees_cfg.diving_line);
        });
        self.multi_graphic_hard_other_price_avg = ko.computed(function(){
            return calcPriceWithFollowerCount(self.multi_graphic_hard_other_price(), self.followers_count());
        });

        self.checkAccountExists = function() {
            var weiboId = $.trim(self.weibo_id());
            if (weiboId) {
                $.restGet('/Media/SocialAccount/accountExists',
                    {weibo_id: weiboId, weibo_type: self.weibo_type()}
                ).done(function(msg, data) {
                    if (data && data.length === 1 && data[0] === false) {
                        W.message("账号不存在，可以添加");
                    } else {
                        W.alert("微信号已存在！<br/>若该账号确实属于您，请联系媒介经理");
                    }
                }).fail(function(msg, data) {
                    W.alert(msg);
                });
            }
        };
        
        
        // 周平均阅读数
        self.weekly_read_avg = data.collection_num || 0;
        // 频道
        self.channel_name = ko.observable(data.channel_name);
        // 标题
        self.account_title = ko.observable(data.account_title);
        // 入口
        self.account_entry = ko.observable(data.account_entry);
        // 是否新闻源
        self.is_news_source = ko.observable(data.is_news_source);
        self.is_news_source_name = function(){
            return getOptionName(self.is_news_source(), self.yes_no_lack_data_options());
        }();
        // 是否网址收录
        self.is_web_site_included = ko.observable(data.is_web_site_included);
        self.is_web_site_included_name = function(){
            return getOptionName(self.is_web_site_included(), self.yes_no_lack_data_options());
        }();
        // 是否需要来源
        self.is_need_source = ko.observable(data.is_need_source);
        self.is_need_source_name = function(){
            return getOptionName(self.is_need_source(), self.yes_no_lack_data_options());
        }();
        // 周末能否发稿
        self.is_press_weekly = ko.observable(data.is_press_weekly);
        self.is_press_weekly_name = function(){
            return getOptionName(self.is_press_weekly(), self.yes_no_lack_data_options());
        }();
        // 能否带文本链接
        self.is_text_link = ko.observable(data.is_text_link);
        self.is_text_link_name = function(){
            return getOptionName(self.is_text_link(), self.yes_no_lack_data_options());
        }();
        // 媒体截图
        self.screen_shot_media = ko.observable(data.screen_shot_media).extend({weixin_image:''});
        self.screen_shot_media_update_time = ko.observable(data.screen_shot_media_update_time).extend({phpTsToDate:''});
    }

    return AccountModel;
});