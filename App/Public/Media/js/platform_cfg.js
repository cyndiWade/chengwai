define(function(require) {
    return [
        {
            pid:1,
            platform_name:'新浪',
            platform_icon:'sina.jpg',
            regex_url:[/^http:\/\/weibo\.com\/((u{0,1}\/[0-9]+)|([a-z0-9]+))$/i],
            regex_weibo_id:[/^\d{6,13}$/],
            range_weibo_name_length:{min:4, max:30},
            range_follower_count:{min:500, max:null}
        },
        {
            pid:2,
            platform_name:'腾讯',
            platform_icon:'tencent.jpg',
            regex_url:[/^http:\/\/t\.qq\.com\/[a-z]{1}[0-9a-z_-]+$/i],
            regex_weibo_id:[/^[a-z]{1}[a-zA-Z_0-9-]{3,21}$/i],
            range_weibo_name_length:{min:4, max:30},
            range_follower_count:{min:500, max:null}
        },
        {
            pid:3,
            platform_name:'微信',
            platform_icon:'weixin.jpg',
            url_prefix:'weixin://profile/',
            regex_url:[/^(weixin){1}:\/\/(profile){1}\/[a-zA-Z]+[a-zA-Z_0-9-]*$/i],
            regex_weibo_id:[/^[a-zA-Z]{1,}[a-zA-Z_0-9-]{5,19}$/],
            range_weibo_name_length:{min:2, max:16},
            range_follower_count:{min:500, max:null}
        },
        {
            pid:4,
            platform_name:'新闻媒体',
            platform_icon:'weixin.jpg',
            regex_url:[/^(weixin){1}:\/\/(profile){1}\/[a-zA-Z]+[a-zA-Z_0-9-]*$/i],
            regex_weibo_id:[/^[a-zA-Z]{1,}[a-zA-Z_0-9-]{5,19}$/],
            range_weibo_name_length:{min:2, max:16}
        }
    ];
});
