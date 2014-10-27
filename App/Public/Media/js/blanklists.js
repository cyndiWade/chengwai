define(function(require, exports) {
    var blankWin = require('showAddBlankWin.js');
    var grid;
    function initGrid(filters,weiboType) {
        grid = exports.grid = new W.Grid({
            messages: {
                nodata: "很抱歉，没有找到合适的账号",
                noQueryResult: "很抱歉，没有找到合适的账号"
            },
            renderTo: "#list",
            // url: '/Media/SocialAccount/getAccountList/',
            url: '/Media/Account/blankLists/',
            params: filters,
            columns: [
                {
                    align: "left",
                    text: "帐号类型",
                    cls: "t2 tc",
                    width: "256px",
                    dataIndex: "typename"
                },
                {
                    align: "left",
                    text: '卡号',
                    width: "256px",
                    dataIndex: "card",
                    cls: "t3 tc"
                },
                {
                    align: "left",
                    text: "真实姓名",
                    cls: "t4 tc",
                    width: "81px",
                    dataIndex: "truename"
                },
                {
                    align: "right",
                    text: '<span class="js_tour_detail" style="margin:auto">操作</span>',
                    dataIndex: "",
                    cls: "last tc",
                    formatter: function (data, row) {
                        var div = $('<div></div>');
                        var edit = $("<a href='javascript:void(0)' class='yel'></a>");
                        var del = $("<a href='javascript:void(0)' class='yel'></a>");
                        edit.click(function () {
                            blankWin.showAddBlankWin(row.cells);
                        });
                        edit.append('编辑');
                        del.click(function () {
                            W.confirm('确认删除该数据吗？', function (cflag) {
                                if (cflag) {
                                    var message = W.message("处理中", "loading", 1000);
                                    return $.ajax({
                                        type: "POST",
                                        url: "/Media/Account/delBlank",
                                        data: {id: row.cells.id},
                                        dataType: "json",
                                        success: function (msg) {
                                            message.close();
                                            location.reload();
                                        }
                                    });
                                } else {
                                    return false;
                                }
                            });
                        });
                        del.append('删除');
                        return div.append(edit).append(' | ').append(del);
                    }
                }
            ],
            listeners: {
                //列表加载完成后要触发方法
                afterrefresh: function () {
                },
                // 列表插入一行的事件
                afterinsertRowBefore: function (state, row, index) {
                }
            }
        });
    }

    return {
        initGrid: initGrid,
        init: function () {
        },
        getGrid: function() {
            return grid;
        }
    }

});