function isReg(string) 
{
    return (/^[0-9a-z \u4e00-\u9fa5]{3,30}$/gi).test(string) !== false;
}
function checkPhone(string) 
{
    return (/^1[358]\d{9}$/).test(string) !== false;
}
function checkQQ(string) 
{
    return (/^([1-9][0-9]{4,16}$)/).test(string) !== false;
}
function checkEmail(string) 
{
    return (/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/).test(string) !== false;
}