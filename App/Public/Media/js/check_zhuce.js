function phone(num)
{
	var patten = /^1[3,5,7,8]\d{9}$/;
	if(patten.test(num)){
    	return true;
    }else{
    	return false;
    }
}
$('.errortip').hide();
$('.oktip').hide();
$('input[name="account"]').focusout(function(){
	check_val($(this));
});
$('input[name="password"]').focusout(function(){
	check_val($(this));
});
$('input[name="phone_verify"]').focusout(function(){
	check_val($(this));
});
$('input[name="password_check"]').focusout(function(){
	var pass = $('input[name="password"]').val();
	var _this = $(this)
	if(pass==_this.val())
	{
		if(_this.val()!='')
		{
			_this.attr('class','text');
			_this.next().show();
			_this.next().next().html('两次密码不一致').hide();
		}else{
			_this.attr('class','text text-error');
			_this.next().hide();
			_this.next().next().html('重复密码不能为空').show();
		}
	}else{
		_this.attr('class','text text-error');
		_this.next().hide();
		_this.next().next().show();
	}
});
function check_val(_this)
{
	if(_this.val()!='')
	{
		_this.attr('class','text');
		_this.next().show();
		_this.next().next().hide();
	}else{
		_this.attr('class','text text-error');
		_this.next().hide();
		_this.next().next().show();
	}
}
function all_submit(){
	if($('.check:checked').val()!='on')
	{
		alert('请先同意服务协议!');
	}else{
		var str_array = [$('input[name="account"]'),$('input[name="password"]'),$('input[name="password_check"]'),$('input[name="iphone"]'),$('input[name="phone_verify"]')];
		var strBool = new Array();
		for(var i= 0 ; i < str_array.length ; i++)
		{
			var _this = str_array[i];
			if(_this.val()!='')
			{
				_this.attr('class','text');
				_this.next().show();
				_this.next().next().hide();
				strBool.push(1);
			}else{
				_this.attr('class','text text-error');
				_this.next().hide();
				_this.next().next().show();
				strBool.push(0);
			}
		}
		if(strBool.indexOf(0)=='-1' )
		{
			return true;
		}else{
			return false;
		}
	}
}