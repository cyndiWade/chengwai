/**
 * 排序项
 * 从页面中提取出来的
 */
define(function() {
    var group, callback;
    var current = 0;
    var desc = true;
    function init(groupel, callbackfunc) {
        group = groupel;
        callback = callbackfunc;

        group.on("click", "a", function() {
            if ($(this).hasClass("btn_mini_disabled")) {
                return;
            }
            var items = group.find("a").removeClass("btn_mini_normal").addClass("btn_mini_disabled");
            var index = items.index(this);
            if (index == current) {
                desc = !desc;
            }
            else {
                desc = true;
                current = index;
            }
            setOrder($(this), desc, callback);
            //alert( $(".qqqq").attr('checked','') );
            if (typeof callback === "function") {
                var ret = callback($(this).attr("ordertype"), desc);
                if (W._isDeferred(ret)) {
                    ret.always(function() {
                        items.addClass("btn_mini_normal").removeClass("btn_mini_disabled");
                    });
                }
            }
        });
    }

    function setOrder(el, desc) {

        var items = group.find("a");
        items.removeClass("btnSort_current_down").removeClass("btnSort_current_up");

        if (!el.length) {
            clear();
            return;
        }

        var cls = desc ? "btnSort_current_down" : "btnSort_current_up";
        el.addClass(cls);

    }

    function clear() {
        current = -1;
        desc = true;
        //clear style
    }

    return {
        init: init,
        reset: clear,
        setOrder: setOrder
    }
});