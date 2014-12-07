define(function (require) {
    var ko = require('knockout');



    /**
     * 对应用户的model数据
     * @constructor
     */
    function RateModel(rate) {
        var self = this;
        self.rating_score = rate.rating_score || 0;
        self.rating_score_title = ko.computed(function(){
            return "累计积分：" + self.rating_score;
        });

        self.rating_level = function(){
            var levelUpperBounds = [50,200,500,1000,1500,3000,5000,7000,10000,
                15000,20000,25000,35000,50000,100000,150000,300000,500000,1000000];

            var score = self.rating_score;
            for(var index=0; index<levelUpperBounds.length;index++){
                if(score <= levelUpperBounds[index]){
                    return index + 1;
                }
            }
            return levelUpperBounds.length;
        }();
        self.rating_level_icon = function(){
            return "/img/level" + self.rating_level + ".gif";
        }();

        self.good_rating_rate = rate.good_rating_rate;
        self.good_rating_rate_display = function(){
            return self.good_rating_rate ? self.good_rating_rate * 100 + "%" : "暂无数据";
        }();
    }

    return RateModel;
});