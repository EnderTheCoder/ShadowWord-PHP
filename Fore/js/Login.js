function login() {
    $.ajax({
        //几个参数需要注意一下
        type: "POST",//方法类型
        dataType: "jsonp",//预期服务器返回的数据类型
        url: "../Back/loginMake.php" ,//url
        data: $('#formLogin').serialize(),
        success: function (result) {
            console.log(result);//打印服务端返回的数据(调试用)
            if (result.resultCode == 200)
            {
                alert("SUCCESS");
            };
        },
        error : function ale() {
            alert("异常！");
        }
    });
}