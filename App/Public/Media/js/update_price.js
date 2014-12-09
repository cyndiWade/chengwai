define(function(require, exports){

    function string2float(str){
        if(str === '') return '';
        str = parseFloat(str).toFixed(2);
        return str == 'NaN' ? '':str;
    }


    function initBind(grid){
        $('.priceBox:not(.notReduceOrRaise)').on('click',function(e){
            var priceEl = $(this);
            var priceEditEl = priceEl.next('textarea');
            var priceValue = string2float(priceEl.text());

            //准备编辑
            priceEditEl.val(priceValue);
            priceEl.css('display','none');
            priceEditEl.css({'display':'inline-block','height':'18px','verticalAlign':'middle'}).focus();
        })

        $('.pirceEditBox').on('blur',function(e){

            var priceEditEl = $(this);
            var priceEl = priceEditEl.prev('.priceBox');
            var priceValue = priceEditEl.val();
            var rowId = priceEl.closest('tr').data('rowid');
            var priceKey = priceEl.closest('.weixinPriceViewAndUpdate').data('price');
            var row = grid.getRow(rowId);

            //编辑事件
            editHandler(priceValue, row, priceKey, grid, priceEl, priceEditEl)
        });
    }

    function editHandler(valu, row, priceKey, grid, priceEl, priceEditEl){

        if(string2float(valu) == parseFloat(row.cells[priceKey]) || (row.cells[priceKey] == null && valu == '')){
            //回复原来的
            priceEl.css('display','inline-block');
            priceEditEl.css('display','none');
            return false;
        }

        if (isNaN(valu)) {
            W.alert('请输入合法的价格');
            priceEditEl.val('');
            return false;
        }
        var params = {}
        params['price_type'] = priceKey;
        params['price_value'] = valu;
        params['row'] = row.cells;
        var checkResult = true;
        var result = $.Deferred();
        $.ajax({
            type:'GET',
            url:'/information/accountmanage/checkprice',
            data:params,
            async:false,
            dataType:'json',
            success:function(data){
                if(!data.isEditAble){
                    W.alert(data.message);
                    result.reject();
                    grid.reload();
                }else{
                    W.confirm(data.message,function(sure){
                        if(sure){
                            $.ajax({
                                type:'GET',
                                url:'/information/accountmanage/updateprice',
                                data:params,
                                async:false,
                                dataType:'json',
                                success:function(data){
                                    if(!data.result){
                                        result.reject();
                                        W.alert(data.message);
                                    }else{
                                        W.alert(data.message, 'success');
                                        grid.reload();
                                    }
                                },
                                error:function (){
                                    W.alert("更新失败，联系管理员！");
                                    grid.reload();
                                }
                            })
                            result.resolve();
                        }else{
                            result.reject();
                        }

                    })
                }
            },
            error:function (){
                W.alert("操作失败！请联系管理员");
            }
        });
        return result;
    }

    return {initBind:initBind};
});