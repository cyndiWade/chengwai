define(function(require) {
    var ko = require('knockout');

    /**
     * 对应用户的model数据
     * @constructor
     */
    function PlatformModel(data) {
        var self = this;
        self.pid = data.pid;
        self.platform_name = data.platform_name;
        self.platform_icon = data.platform_icon;
        self.regex_url = data.regex_url || [/^(https?|ftp|mms):\/\/([A-Za-z0-9]+[_\-]?[A-Za-z0-9]*\.)*[A-Za-z0-9]+\-?[A-Za-z0-9]+\.[A-Za-z]{2,}(\/.*)*\/?$/i];
        self.regex_weibo_id = data.regex_weibo_id || [/^[0-9A-Za-z_-]+$/];
        self.range_weibo_name_length = data.range_weibo_name_length || {min:1, max:60};
        self.range_follower_count = data.range_follower_count || {min:2000, max:null};
        self.url_prefix = data.url_prefix;
    }

    return PlatformModel;
});