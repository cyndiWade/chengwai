define(function(require) {
    var ko = require('knockout');
    ko.extenders.getTags = function(target, cfg) {

        target.occupation = ko.observable();
        target.occupation_option = ko.observableArray([{id:1, name: '中国'}]);
        target.field = ko.observable();
        target.field_option = ko.observableArray();
        target.cirymedia = ko.observable();
        target.cirymedia_option = ko.observableArray();
        target.interest = ko.observable();
        target.interest_option = ko.observableArray();

        option = {
            url : '',
        };

        $.extend(option, cfg);
		
		/* 
		// 名人职业
		$.restGet(option.url, {id : 21}, function(code, datas){
			target.occupation_option(datas);
		});
		target.occupation('');
        target.occupation_option({});
		// 媒体领域
		$.restGet(option.url, {id : 22}, function(code, datas){
			target.field_option(datas);
		});
		target.field('');
        target.field_option([]);
		// 地方名人/媒体
		$.restGet(option.url, {id : 106}, function(code, datas){
			target.cirymedia_option(datas);
		});
		target.cirymedia('');
        target.cirymedia_option([]);
		// 兴趣标签
		$.restGet(option.url, {id : 118}, function(code, datas){
			target.interest_option(datas);
		});
		target.interest('');
        target.interest_option([]);
		 */
		 
		$.restGet(option.url, {id : '21,22,106,118'}, function(code, datas){
			target.occupation_option(datas[21]);
			target.field_option(datas[22]);
			target.cirymedia_option(datas[106]);
			target.interest_option(datas[118]);
		});
		target.occupation('');
        target.occupation_option({});
		target.field('');
        target.field_option([]);
		target.cirymedia('');
        target.cirymedia_option([]);
		target.interest('');
        target.interest_option([]);
		
        return target;
    };
});