define(function (require) {
    var ko = require('knockout');
    require('knockout_validation');

    ko.validation.rules['minCharLength'] = {
        validator: function (val, minLength) {
            return ko.validation.utils.isEmptyVal(val) || W.util.getCharCount(val) >= minLength;
        },
        message: 'Sorry Chief, {0} this is not Valid'
    };
    ko.validation.rules['maxCharLength'] = {
        validator: function (val, maxLength) {
            return ko.validation.utils.isEmptyVal(val) || W.util.getCharCount(val) <= maxLength;
        },
        message: 'Sorry Chief, {0} this is not Valid'
    };
    ko.validation.registerExtenders();

    /**
     * 对应用户的model数据
     * @constructor
     */
    function AccountValidationModel(accountModel, data) {
        var self = accountModel;

        self.weixin_image_option = {url : $.pluploadOpts.url, viewBasePath : $.pluploadOpts.viewBasePath};
        self.open_rate_upload_option = {
            url : $.pluploadOpts.url,
            viewBasePath : $.pluploadOpts.viewBasePath,
            type: 'attachments',
            filters : [{title: "Custom files", extensions: "csv"}],
            responseParser: function(res) {
                if (res && res.success) {
                    self.open_count(res.open_count);
                    return {
                        path: res.filename,
                        id: res.file_id
                    }
                } else {
                    return {
                        error: res.error || res.msg || '文件上传失败'
                    }
                }
            },
            onitemappend: function(up, $list, $item, filepath) {
            }
        };
        data && data.open_rate_upload_option && $.extend(self.open_rate_upload_option, data.open_rate_upload_option);
        data && data.weixin_image_option && $.extend(self.weixin_image_option, data.weixin_image_option);

        //三态提示框，用以处理提示语
        ko.bindingHandlers.validThreeState = {
            init: function (element, valueAccessor) {
                var value = valueAccessor(),
                    $element = $(element);

                //展示默认值
                value.notice_css = ko.observable('prompt');
                value.notice_text = ko.observable(value.init_prompt);

                //获取焦点时候的设置
                $element.focus(function () {
                    value.notice_css('prompt');
                    value.notice_text(value.focus_prompt);
                });

                //丢失焦点时候的设置
                $element.blur(function () {
                    if (value.error()) {
                        value.notice_css('error');
                        value.notice_text(value.error());
                    } else {
                        value.notice_css('correct');
                        value.notice_text('');
                    }
                });
            }
        };

        //用以处理notice事件
        ko.bindingHandlers.noticeImg = {
            init: function (element, valueAccessor) {
                var value = valueAccessor(),
                    $element = $(element);

                //获取焦点时候的设置
                $element.focus(function () {
                    self.notice_img(value());
                });
            }
        };

        self.weibo_id = self.weibo_id.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 1) {
                //新浪
                return {
                    required: {message: '账号名不能为空'},
                    minCharLength: {params: 4, message: '4-30个字符，支持中英文、数字、“_”和减号'},
                    maxCharLength: {params: 30, message: '4-30个字符，支持中英文、数字、“_”和减号'},
                    pattern: {
                        message: '4-30个字符，支持中英文、数字、“_”和减号',
                        params: '^[\u4E00-\u9FA5a-zA-Z0-9_\-]+$'
                    }
                };
            } else if (weibo_type == 2) {
                //腾讯
                return {
                    required: {message: '账号名不能为空'},
                    minCharLength: {params: 4, message: '4-22个字符，支持中英文、数字、“_”和减号'},
                    maxCharLength: {params: 22, message: '4-22个字符，支持中英文、数字、“_”和减号'},
                    pattern: {
                        message: '4-22个字符，支持中英文、数字、“_”和减号',
                        params: '^[a-zA-Z]{1}[a-zA-Z_0-9-]{3,21}$'
                    }
                };
            }else if(weibo_type == 3){
                //微信
                return {
                    required: {message: '微信号不能为空！'},
                    pattern: {
                        message: '格式不正确^[a-zA-Z]{1,}[a-zA-Z_0-9-]{1,}$',
                        params: '^[a-zA-Z]{1,}[a-zA-Z_0-9-]{1,}$'
                    },
                    minCharLength: {params: 6, message: '6-20个字符'},
                    maxCharLength: {params: 20, message: '6-20个字符'}
                };
            }else if(weibo_type == 4){
                //新闻媒体
                return {
                    required: {message: '账号名不能为空！'},
                    pattern: {
                        message: '4-30个字符，支持中英文、数字、“_”和减号',
                        params: '^[\u4E00-\u9FA5a-zA-Z0-9_\-]+$'
                    },
                    minCharLength: {params: 4, message: '4-30个字符，支持中英文、数字、“_”和减号'},
                    maxCharLength: {params: 30, message: '4-30个字符，支持中英文、数字、“_”和减号'}
                };
            }else{
                //其他
                return {
                    required: {message: 'ID不能为空！'},
                    pattern: {
                        message: '格式不正确^[a-zA-Z]{1,}[a-zA-Z_0-9-]{1,}$',
                        params: '^[0-9A-Za-z_-]+$'
                    },
                    minCharLength: {params: 1, message: '1-32个字符'},
                    maxCharLength: {params: 32, message: '1-32个字符'}
                };
            }
        }());
        self.weibo_id.init_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 1) {
                return "4-30个字符，支持中英文、数字、“_”和减号";
            } else if (weibo_type == 2) {
                return '4-22个字符，支持中英文、数字、“_”和减号';
            } else if (weibo_type == 4) {
                return '4-30个字符，支持中英文、数字、“_”和减号';
            } else if (weibo_type == 19) {
                return '请填写空间的qq号码,如右侧图示.'
            } else if (weibo_type == 17) {
                return '请输入正确的ID,只能是数字。';
            }else if (weibo_type == 23 || weibo_type == 3) {
                return '请输入正确的微信号！';
            }else{
                return '请输入正确的ID！';
            }
        }();
        self.weibo_id.focus_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 1) {
                return "4-30个字符，支持中英文、数字、“_”和减号";
            } else if (weibo_type == 2) {
                return '4-22个字符，支持中英文、数字、“_”和减号';
            } else if (weibo_type == 4) {
                return '4-30个字符，支持中英文、数字、“_”和减号';
            } else if (weibo_type == 19) {
                return '请填写空间的qq号码,如右侧图示.'
            } else if (weibo_type == 17) {
                return '请输入正确的ID,只能是数字。';
            } else if (weibo_type == 23 || weibo_type == 3) {
                return '请输入正确的微信号！';
            }else{
                return '请输入正确的ID！';
            }
        }();

        self.url = self.url.extend(function () {
            var weibo_type = self.weibo_type();
            /* if(weibo_type == 4){
                //网易
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '格式不正确/^http:\/\/t\.163\.com\/(([0-9]+)|([a-z]+)|([a-z0-9_]+))$/i',
                        params: /^http:\/\/t\.163\.com\/(([0-9]+)|([a-z]+)|([a-z0-9_]+))$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            }else if(weibo_type == 6){
                //美丽说
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '格式不正确/^http:\/\/www\.meilishuo\.com\/((person\/u\/[0-9]+)|(group\/[0-9]+))$/i',
                        params: /^http:\/\/www\.meilishuo\.com\/((person\/u\/[0-9]+)|(group\/[0-9]+))$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            }else if(weibo_type == 7){
                //蘑菇街
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '格式不正确/^http:\/\/www\.mogujie\.com\/cover\/u\/[a-zA-Z0-9]+$/i',
                        params: /^http:\/\/www\.mogujie\.com\/cover\/u\/[a-zA-Z0-9]+$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            }else if(weibo_type == 8){
                //人人网
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '格式不正确/^http:\/\/((www\.renren\.com\/[0-9]+)|(page\.renren\.com\/[0-9]+)|(zhan\.renren\.com\/[a-zA-Z0-9]+))$/i',
                        params: /^http:\/\/((www\.renren\.com\/[0-9]+)|(page\.renren\.com\/[0-9]+)|(zhan\.renren\.com\/[a-zA-Z0-9]+))$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            }else if(weibo_type != 1 && weibo_type != 2 && weibo_type != 3 && weibo_type != 17 && weibo_type != 19 && weibo_type != 5 && weibo_type != 23 && weibo_type != 9){
                //其他
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '必须是个合法的url',
                        params: /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            } */
            if (weibo_type == 4) {
                // 新闻媒体
                return {
                    required: {message: '链接不能为空！'},
                    pattern: {
                        message: '必须是个合法的url',
                        params: /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i
                    },
                    minCharLength: {params: 13, message: '1-13个字符'},
                    maxCharLength: {params: 255, message: '1-255个字符'}
                };
            }
        }());
        self.url.init_prompt = '请输入正确的账号链接！';
        self.url.focus_prompt = '请输入正确的账号链接！';

        self.weibo_name = self.weibo_name.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required: {message: '此处为必填项！'},
                    minCharLength: {params: 2, message: '微信名字格式有误！'},
                    maxCharLength: {params: 32, message: '微信名字格式有误！'}
                };
            }else if(weibo_type == 3){
                //微信
                return {
                    required: {message: '账号名不能为空！'},
                    minCharLength: {params: 4, message: '长度2-16个字，请输入正确的长度！'},
                    maxCharLength: {params: 32, message: '长度2-16个字，请输入正确的长度！'}
                };
            }else if(weibo_type != 1 && weibo_type != 2 && weibo_type != 4 && weibo_type != 17 && weibo_type != 5 && weibo_type != 19){
                return {
                    required: {message: '账号名不能为空！'},
                    minCharLength: {params: 1, message: '长度1-60个字符，请输入正确的长度！'},
                    maxCharLength: {params: 60, message: '长度1-60个字符，请输入正确的长度！'}
                };
            }
            return {};
        }());
        self.weibo_name.init_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return '请输入正确的微信名字！';
            }else {
                return '请输入正确的账号名！';
            }
        }();
        self.weibo_name.focus_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return '请输入正确的微信名字！';
            // }else if(weibo_type == 5){
                // return '仅支持认证用户！';
            }else{
                return '请输入正确的账号名！';
            }
        }();

        self.followers_count = self.followers_count.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required: {message: '此处为必填项！'},
                    digit: {message: '必须为整数！'},
                    min: {params:500, message: '好友数必须大于等于500！'}
                };
            }else if (weibo_type == 3) {
                //微信
                return {
                    required: {message: '此处为必填项！'},
                    digit: {message: '必须为整数！'},
                    min: {params: 500, message: '粉丝数必须大于等于500！'}
                };
            }else if(weibo_type != 1 && weibo_type != 2 && weibo_type != 4 && weibo_type != 17 && weibo_type != 5 && weibo_type != 19){
                //其他
                return {
                    required: {message: '粉丝数不能为空！'},
                    digit: {message: '请输入正确的粉丝数！'},
                    min: {params: 2000, message: '粉丝数必须大于等于2000！'}
                };
            }
        }());
        self.followers_count.init_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return '请输入正确的好友数！';
            }else{
                return '请输入正确的粉丝数！';
            }
        }();
        self.followers_count.focus_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return '请输入正确的好友数！';
            }else{
                return '请输入正确的粉丝数！';
            }
        }();

        self.retweet_price = self.retweet_price.extend(function () {
            var weibo_type = self.weibo_type();
            //QZone，微信，微淘，朋友圈
            if (weibo_type != 19 && weibo_type != 3 && weibo_type != 17 && weibo_type != 23) {
                return {
                    required: {message: '价格不能为空！'},
                    digit: {message: '请输入正整数！'},
                    min: {params: 1, message: '价格必须大于零'}
                }
            }
            return {};
        }());
        self.retweet_price.init_prompt = function(){
            var weibo_type = self.weibo_type();
            if(weibo_type == 1 || weibo_type == 2){
                return "硬广直发价等同于硬广转发价";
            } else if (weibo_type == 4) {
                return "发布该新闻媒体所需价格";
            } else {
                return "硬广转发价是指转发一条指定链接内容的价格";
            }
        }();
        self.retweet_price.focus_prompt = function(){
            var weibo_type = self.weibo_type();
            if(weibo_type == 1 || weibo_type == 2){
                return "硬广转发价：是指博主的微博账号成功转发一条指定微博链接后所赚取的金额！</br>如何定硬广转发价：博主自己可根据微博账号的粉丝数量、微博内容质量等因素酌情定价，低价格有利于吸引更多客户选择您的账号。"
            } else if (weibo_type == 4) {
                return "请填写正确的价格!";
            }else{
                return "硬广转发价是指转发一条指定链接内容的价格";
            }
        }();

        self.tweet_price = self.tweet_price.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type != 19 && weibo_type != 1 && weibo_type != 2 && weibo_type != 3 && weibo_type != 4) {
                return {
                    required: {message: '硬广直发价格不能为空！'},
                    digit: {message: '请输入正整数！'},
                    min: {params: 1, message: '价格必须大于零'}
                }
            }
            return {};
        }());
        self.tweet_price.init_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 17) {
                return "微淘硬广直发价，指通过微淘管理后台成功发送<br/>一次内容/消息的价格。";
            }else if(weibo_type == 23){
                return "指您在朋友圈发送一次信息得到的收入！";
            }else if(weibo_type == 3){
                return "微信硬广直发价指从微信公众平台成功发送一次<br/>内容/消息的价格！"
            }else{
                return '硬广直发价是指自己发布一条指定内容的价格';
            }
        }();
        self.tweet_price.focus_prompt = function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 17) {
                return "微淘硬广直发价，指通过微淘管理后台成功发送<br/>一次内容/消息的价格。";
            }else if(weibo_type == 23){
                return "指您在朋友圈发送一次信息得到的收入！";
            }else if(weibo_type == 3){
                return "微信硬广直发价指从微信公众平台成功发送一次<br/>内容/消息的价格！"
            }else{
                return '硬广直发价是指自己发布一条指定内容的价格';
            }
        }();


        self.weitao_taobao_id = self.weitao_taobao_id.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 17) {
                //微淘
                return {
                    required: {message: '淘宝账号不能为空！'},
                    minCharLength: {params: 5, message: '5-22个字符，请输入正确的长度'},
                    maxCharLength: {params: 25, message: '5-22个字符，请输入正确的长度'},
                    pattern: {
                        message: '格式错误！^[\u4e00-\u9fa5a-zA-Z0-9]+[\u4e00-\u9fa5a-zA-Z0-9_]*[\u4e00-\u9fa5a-zA-Z0-9]+$',
                        params: '^[\u4e00-\u9fa5a-zA-Z0-9]+[\u4e00-\u9fa5a-zA-Z0-9_]*[\u4e00-\u9fa5a-zA-Z0-9]+$'
                    }
                };
            }
            return {};
        }());
        self.weitao_taobao_id.init_prompt = '请输入您登陆微淘平台后显示的淘宝会员名。<br/>5-25个字符，一个汉字为两个字符。<br/><a href="/auth/index/help#6,4" target="_blank">什么是淘宝会员名？</a>';
        self.weitao_taobao_id.focus_prompt = '请输入您登陆微淘平台后显示的淘宝会员名。<br/>5-25个字符，一个汉字为两个字符。<br/><a href="/auth/index/help#6,4" target="_blank">什么是淘宝会员名？</a>';

        self.age = self.age.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required:{
                        message: '此项不能为空'
                    },
                    pattern: {
                        message: '0-100岁',
                        params: '^[0-9]{1,2}$'
                    }
                };
            }
            return {};
        }());
        self.age.init_prompt = "请输入您的真实年龄！";
        self.age.focus_prompt = "请输入您的真实年龄！";

        self.gender = self.gender.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required: {message: '此项为必填项！'}
                };
            }
            return {};
        }());

        self.profession_type.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required: {message: '请输入准确的职业信息！'}
                };
            }
            return {};
        }());

        self.profession_other_info.extend(function(){
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required: {message: '请输入准确的职业信息！', onlyIf: function(){
                        console.log('only if');
                        return self.profession_type() == 7;
                    }}
                };
            }
            return {};
        }());

        self.account_phone.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                //朋友圈
                return {
                    required:{
                        message: '此项不能为空!'
                    },
                    pattern: {
                        message: '格式不正确！',
                        params: /^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|14[5|7])\d{8}$/
                    }
                };
            }
            return {};
        }());
        self.friend_desc = self.friend_desc.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return {
                    required: {message: '此处为必填项！'},
                    maxCharLength: {params: 600, message: '超过300字！'}
                };
            }
            return {};
        }());
        self.friend_desc.init_prompt = '最多输入300字！';
        self.friend_desc.focus_prompt = '最多输入300字！';
        self.true_name = self.true_name.extend(function(){
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return {
                    required: {message: '此处为必填项！'},
                    pattern: {
                        message: '请输入正确的姓名！',
                        params: '^[\u4e00-\u9fa5]{2,6}$'
                    }
                };
            }
            return {};
        }());
        self.account_advantage.init_prompt = '最多输入300字！';
        self.account_advantage.focus_prompt = '最多输入300字！';
        self.account_advantage = self.account_advantage.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return {
                    maxCharLength: {params: 600, message: '超过300字！'}
                };
            }
            return {};
        }());
        self.edu_degree.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return {
                    required: {message: '此为必填项！'}
                };
            }
            return {};
        }());
        //账号头像
        self.account_avatar.extend({weixin_image : self.weixin_image_option});
        self.account_avatar.extend(function(){
            var weibo_type = self.weibo_type();
            if (weibo_type == 23) {
                return {
                    required: {message: '此处为必填项！'}
                };
            }
            return {};
        }()
        );
//        真人头像
        self.person_avatar.extend({
                weixin_image : self.weixin_image_option
            }
        );
//        发布历史
        var tempR = $.extend({},self.weixin_image_option,{upload_limit:10});
        self.release_history.extend({
                weixin_image : tempR
            }
        );

        //粉丝数
        self.screen_shot_followers.extend({
                weixin_image : self.weixin_image_option
            }
        ).extend(function () {
                var weibo_type = self.weibo_type();
                if (weibo_type == 23) {
                    //朋友圈
                    return {required: {message: "请上传好友数截图！"}};
                }
                if(weibo_type == 3){
                    return {required: {message: "请上传粉丝截图！"}};
                }
                return {};
            }());

        //头像截图
        self.screen_portrait.extend({
                weixin_image : self.weixin_image_option
            }
        ).extend(function () {
                var weibo_type = self.weibo_type();
                if (weibo_type == 3) {
                    //微信
                    return {
                        required: {message : "请上传头像截图！"}
                    };
                }
                return {};
        }());
        self.weibo_link.extend(function(){
            var weibo_type = self.weibo_type();
            if(weibo_type == 23){
                return {
                    pattern: {
                        message: '请输入正确的微博链接！',
                        params: /^(http:\/\/t\.qq\.com\/[a-z]{1}[0-9a-z_-]+)|((http:\/\/([a-z]+\.|)weibo.com))/i
                    }
                };
            }
            return {};
        }());
        //二维码
        self.screen_shot_qr_code.extend({
                weixin_image : self.weixin_image_option
            }
        ).extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 3) {
                //微信
                return {
                    required: {message : "请上传二维码截图！"}
                };
            }
            return {};
        }());
        //粉丝截图
        self.screen_shot_info.extend({
                weixin_image : self.weixin_image_option
            }
        );

        //图文打开率文件路径
        self.open_rate_path.extend({
                weixin_image : self.open_rate_upload_option
            }
        ).extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 3) {
                //微信
                return {
                    required: {message : "请上传图文页阅读数据的文档！", onlyIf: function(){
                        return parseInt(self.open_rate_identified()) == 1;
                    }}
                };
            }
            return {};
        }());

        //地域的验证信息
        self.area_id.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 23 || weibo_type == 4) {
                //朋友圈23 新闻媒体4
                return {
                    required: {message: '请输入准确的地域信息！'}
                };
            }
            return {};
        }()).extend({
                area:{
                    url : '/Media/SocialAccount/getlowerarea'
                }
        });

        self.gender_distribution_male.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 3) {
                //微信
                return {
                    required: {message: '请填写男性的分布比例！', onlyIf: function(){
                        return self.gender_distribution_identified() == 1;
                    }},
                    number : {message: '格式不正确'},
                    min:{params: 0, message: '分布比例必须大于0'}
                };
            }
            return {};
        }());

        self.gender_distribution_female.extend(function () {
            var weibo_type = self.weibo_type();
            if (weibo_type == 3) {
                //微信
                return {
                    required: {message: '请填写女性的分布比例！', onlyIf: function(){
                        return self.gender_distribution_identified() == 1;
                    }},
                    number : {message: '格式不正确'},
                    min:{params: 0, message: '分布比例必须大于0'}
                };
            }
            return {};
        }());
        //验证性别男和性别女
        ko.computed(function(){
            var male = parseFloat(self.gender_distribution_male());
            var female = parseFloat(self.gender_distribution_female());
            if(male > 0 || female >0){
                var msg = male + female > 100 ? '您输入的男女比例和不能大于100！' : '';
                self.gender_distribution_male.error(msg);
                self.gender_distribution_female.error(msg);
            }
        });
        
        
        var weibo_type = self.weibo_type();
        if (weibo_type == 3) {
            // 周平均阅读数
            self.weekly_read_avg = self.weekly_read_avg.extend(function () {
                return {
                    required: {message: '周平均阅读数不能为空！'},
                    pattern: {
                        message: '每周平均被浏览数量(0-99999999)',
                        params: '^[0-9]{1,8}$'
                    }
                };
            }());
        }
        
        if (weibo_type == 4) {
            // 频道
            self.channel_name = self.channel_name.extend(function () {
                return {
                    required: {message: '频道不能为空！'},
                    minCharLength: {params: 1, message: '长度1-50个字符，请输入正确的长度！'},
                    maxCharLength: {params: 32, message: '长度1-50个字符，请输入正确的长度！'}
                };
            }());
            self.channel_name.init_prompt = function () {
                return '请输入正确的频道名！';
            }();
            self.channel_name.focus_prompt = function () {
                return '请输入正确的频道名！';
            }();
            // 标题
            self.account_title = self.account_title.extend(function () {
                return {
                    required: {message: '标题不能为空！'},
                    minCharLength: {params: 1, message: '长度1-50个字符，请输入正确的长度！'},
                    maxCharLength: {params: 32, message: '长度1-50个字符，请输入正确的长度！'}
                };
            }());
            self.account_title.init_prompt = function () {
                return '请输入正确的标题！';
            }();
            self.account_title.focus_prompt = function () {
                return '请输入正确的标题！';
            }();
            // 入口
            self.account_entry = self.account_entry.extend(function () {
                return {
                    required: {message: '入口不能为空！'},
                    minCharLength: {params: 1, message: '长度1-50个字符，请输入正确的长度！'},
                    maxCharLength: {params: 32, message: '长度1-50个字符，请输入正确的长度！'}
                };
            }());
            self.account_entry.init_prompt = function () {
                return '请输入正确的入口！';
            }();
            self.account_entry.focus_prompt = function () {
                return '请输入正确的入口！';
            }();
            // 是否新闻源
            self.is_news_source = self.is_news_source.extend(function () {
                return {
                    required: {message: '是否新闻源'}
                };
            }());
            // 网址收录
            self.is_web_site_included = self.is_web_site_included.extend(function () {
                return {
                    required: {message: '请选择网址收录类型'}
                };
            }());
            // 是否需要来源
            self.is_need_source = self.is_need_source.extend(function () {
                return {
                    required: {message: '是否需要来源'}
                };
            }());
            // 周末能否发稿
            self.is_press_weekly = self.is_press_weekly.extend(function () {
                return {
                    required: {message: '周末能否发稿'}
                };
            }());
            // 文本链接
            self.is_text_link = self.is_text_link.extend(function () {
                return {
                    required: {message: '请选择文本链接类型'}
                };
            }());
            // 门户类型
            self.type_of_portal = self.type_of_portal.extend(function () {
                return {
                    required: {message: '请选择门户类型'}
                };
            }());
            // 媒体截图
            self.screen_shot_media.extend({
                weixin_image : self.weixin_image_option
            }).extend(function () {
                return {
                    required: {message: "请上传媒体截图！"}
                };
            }());
        }

        return self;
    }

    return AccountValidationModel;
});