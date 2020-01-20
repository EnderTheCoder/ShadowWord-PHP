$("button").click(function () {
   login();
});
function login() {
    // $("#captcha-img").attr('src', '../Back/captcha.php');
    $.ajax({
        //几个参数需要注意一下
        type: "POST",//方法类型
        dataType: "jsonp",//预期服务器返回的数据类型
        url: "../Back/loginMake.php",//url
        data: $('#formLogin').serialize(),
        success: function (result) {
            //alert(result);打印服务端返回的数据(调试用)
            switch (result) {
                case 1:
                    alert("登录成功，点击确认后自动跳转");
                    window.location.href = "MainPage.html";
                    break;
                case -1:
                    alert("输入存在空，请检查必填项后重试");
                    break;
                case -2:
                    alert("验证码错误，请检查验证码后重试");
                    break;
                case -3:
                    alert("用户名或密码错误");
                    break;
                case -4:
                    alert("数据库发生故障，请联系管理员");
                    break;
                case -5:
                    alert("您属于临时用户，请转至临时用户登录地址，确认后自动跳转");
                    window.location.href = "#";
                    break;
                case -6:
                    alert("您输入的账号目前未完成邮箱验证，请进入邮箱进行检查");
                    break;
                case -7:
                    alert("您输入的账号目前已被冻结或封禁，请联系管理员解封");
                    break;
            }
        },
        error: function () {
            alert("服务器异常！");
        }
    });
}