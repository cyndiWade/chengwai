define(function(require) {
    var avatarDesc = require('./tpl/add_weixin_avatar_desc.tpl'),
        QRCodeDesc = require('./tpl/add_weixin_QR_code_desc.tpl'),
        followersDesc = require('./tpl/add_weixin_followers_desc.tpl');


    new W.Tips({
        target: '.js_avatar_desc',
        html: avatarDesc,
        title: '查看图像图示',
        group: 'weixin_info',
        autoHide: false,
        floor: 'high'
    });

    new W.Tips({
        target: '.js_QR_code_desc',
        html: QRCodeDesc,
        title: '查看二维码图示',
        group: 'weixin_info',
        autoHide: false,
        floor: 'high'
    });

    new W.Tips({
        target: '.js_followers_desc',
        html: followersDesc,
        width: 560,
        title: '查看粉丝截图图示',
        group: 'weixin_info',
        autoHide: false,
        floor: 'high'
    });
});