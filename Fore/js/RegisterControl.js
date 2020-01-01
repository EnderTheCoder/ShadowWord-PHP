// jquery ajax and input-validation by Ender
function register() {
    // $("#captcha-img").attr('src', '../Back/captcha.php');
    if($("#password").val() !== $("#password2").val()) {
        alert("两次输入的密码不一致，请检查后重试");
        return;
    }
    $.ajax({
        type: "POST",
        dataType: "jsonp",
        url: "../Back/registerMake.php",
        data: $('#formRegister').serialize() + BadLock(),
        success: function (result) {
            switch (result) {
                case 1:
                    alert("注册成功，请到邮箱查收验证邮件以完成注册");
                    // window.location.href = "MainPage.html";
                    break;
                case -1:
                    alert("输入存在空，请检查必填项后重试");
                    break;
                case -2:
                    alert("验证码错误，请检查验证码后重试");
                    break;
                case -3:
                    alert("该用户名已被注册");
                    break;
                default:
                    alert("DEBUG");
            }
        },
        error: function () {
            alert("服务器异常！");
        }
    });
}