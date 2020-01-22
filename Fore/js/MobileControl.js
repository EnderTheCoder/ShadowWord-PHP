// function JumpToIndex() {
//     $("#Main").css("z-index", "1000");
// }

//变量初始化
let NowChat = [];
NowChat['username'] = null;
NowChat['messages'] = [];
NowChat['messages']['total'] = 0;
NowChat['MaxID'] = 0;
NowChat['UsrTmp'] = null;
let WindowStack = [];
let WindowStack_Pointer = 0;
WindowStack[0] = "Main";
let AddFocus = null;

//开始执行函数
WindowInitialize();
ClickInitialize();
PollListData();
PollMessagesData();
GetRequestsList();

//登录失效踢出
function NotLoginKick() {
    alert("登录已经失效或不可用，请重新登录");
    window.location.href = "LoginPage.html";
}

//切换窗口
function JumpWindow() {
    const Window = arguments[0];
    const TopUserName = $('#TopUserName');
    WindowStack_Pointer++;
    WindowStack[WindowStack_Pointer] = Window;
    WindowControl(Window);
    switch (Window) {
        case "Main":
            break;
        case "MessagesWindow":
            if (arguments[1] === "验证消息") {
                WindowStack[WindowStack_Pointer] = "RequestsListWindow";
                WindowControl("RequestsListWindow");
                break;
            }
            NowChat['username'] = arguments[1];
            NowChat['messages']['total'] = 0;
            if (NowChat['username'].length > 13) TopUserName.html(NowChat['username'].substr(0, 9) + '...');
            TopUserName.html(NowChat['username']);
            $.ajax({
                url: "../Back/unreadCheck.php",
                type: "POST",
                dataType: 'jsonp',
                async: true,
                timeout: 5000,
                data: {'username': NowChat['username'],},
                success: function (result) {
                    if (result === -1) NotLoginKick();
                },
                error: function () {
                }
            });
            break;
        case "AddFriendWindow":
            break;
        case "UserInfWindow":
            GetUserInf(arguments[1]);
            break;
        case "SendRequestWindow":
            AddFocus = arguments[1];
            $("#SendRequestWindow textarea").empty();
            break;
        case "AddSuccessWindow":
            break;
        case "RequestsListWindow":
            GetRequestsList();
            break;
    }
}

function WindowControl(target) {
    const total = 7;
    let Window = [];
    Window[0] = "Main";
    Window[1] = "MessagesWindow";
    Window[2] = "AddFriendWindow";
    Window[3] = "UserInfWindow";
    Window[4] = "SendRequestWindow";
    Window[5] = "AddSuccessWindow";
    Window[6] = "RequestsListWindow";
    for (let i = 0; i < total; i++) {
        if (Window[i] !== target) $("#" + Window[i]).hide();
        else $("#" + Window[i]).show();
    }
}

function Back() {
    $("#" + WindowStack[WindowStack_Pointer]).hide();
    $("#" + WindowStack[--WindowStack_Pointer]).show();
}

//初始化函数
function WindowInitialize() {
    //初始化内容遮盖关系
    $("#Main").show();
    $("#MainSettingsContainer").hide();
    $("#MessagesWindow").hide();
    $("#GrayMask").hide();
    $("#AddBlock").hide();
    $("#AddFriendWindow").hide();
    $("#UserInfWindow").hide();
    $("#SendRequestWindow").hide();
    $("#AddSuccessWindow").hide();
    $("#RequestsListWindow").hide();
}

function ClickInitialize() {
    //初始化点击触发器
    $("#SubmitForText").click(function () {
        SubmitMessage();
    });
    $(".ReturnIcon").click(function () {
        Back();
    });
    $("#BottomMessages").click(function () {
        SwitchIndex('Messages');
    });
    $("#BottomSettings").click(function () {
        SwitchIndex('Settings');
    });
    $("#add").click(function () {
        AddBlockControl("show");
    });
    $("#GrayMask").click(function () {
        AddBlockControl("hide");
    });
    $("#AddFriend").click(function () {
        JumpWindow("AddFriendWindow");
        AddBlockControl("hide")
    });
    $("#FriendSearchSubmit").click(function () {
        SearchFriends();
    });
    $("#UserInfReturn").click(function () {
        JumpWindow("AddFriendWindow");
    });
    $("#AddSuccessWindow").click(function () {
        Back();
        Back();
    });
    $("#SendRequest").click(function () {
        RequestToAddFriend();
    });
    // $("#Main").hide();
}

function AddBlockControl(action) {
    const Main = $("#Main");
    if (action === "show") {
        $("#GrayMask").show();
        $("#AddBlock").show();
        Main.css("filter", "alpha(Opacity=80)");
        Main.css("-moz-opacity", "0.5");
        Main.css("opacity", "0.5");
    } else {
        $("#GrayMask").hide();
        $("#AddBlock").hide();
        Main.css("filter", "");
        Main.css("-moz-opacity", "");
        Main.css("opacity", "");
    }
}

//控制主页下方选项卡的切换
function SwitchIndex(Page) {
    const Messages = $("#MainMessagesContainer");
    const Settings = $("#MainSettingsContainer");
    const MessagesIcon = $("#BottomMessages");
    const SettingsIcon = $("#BottomSettings");
    switch (Page) {
        case "Messages":
            Messages.show();
            Settings.hide();
            MessagesIcon.attr('src', "img/messages_%231296DB.png");
            SettingsIcon.attr('src', "img/settings_%23A9A9A9.png");
            break;
        case "Settings":
            Messages.hide();
            Settings.show();
            MessagesIcon.attr('src', "img/messages_%23A9A9A9.png");
            SettingsIcon.attr('src', "img/settings_%231296DB.png");
            break;
    }
}

//轮询列表信息
function PollListData() {
    const MessageList = $("#MainMessageList");
    const MessageEnd = '<div id="FixBlock"></div>';
    const MessageBlockTemplate = "<div class=\"MessageBlock\" onclick=\"JumpWindow('MessagesWindow', 'RP-Username')\" id='RP-Name'>\n" +
        "                <img src=\"img/TestHead.jpeg\" alt=\"portrait\" class=\"rounded-circle portrait\">\n" +
        "                <div class=\"MessageLeft\">\n" +
        "                    <div class=\"MessageTittle\">RP-Tittle</div>\n" +
        "                    <div class=\"MessageSummary\">RP-Summary</div>\n" +
        "                </div>\n" +
        "                <div class=\"MessageRight\">\n" +
        "                    <div class=\"MessageDate\">RP-Date</div>\n" +
        "                    <div class=\"RedDot\">RP-Unread</div>\n" +
        "                </div>\n" +
        "            </div>";
    $.ajax({
        url: "../Back/listCheck.php",
        type: "GET",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        success: function (result) {
            MessageList.empty();
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            for (let i = 1; i <= jsons['rows']; i++) {
                if (jsons['messages'][i]['user_2'] === "验证消息" && jsons['messages'][i]['unread'] !== 0) GetRequestsList();
                let MessageBlock = MessageBlockTemplate;
                MessageBlock = MessageBlock.replace("RP-Tittle", jsons['messages'][i]['user_2']);
                MessageBlock = MessageBlock.replace("RP-Summary", jsons['messages'][i]['latestMessage']);
                MessageBlock = MessageBlock.replace("RP-Username", jsons['messages'][i]['user_2']);
                MessageBlock = MessageBlock.replace("RP-Name", jsons['messages'][i]['user_2']);
                if (jsons['messages'][i]['unread'] === 0) MessageBlock = MessageBlock.replace("<div class=\"RedDot\">RP-Unread</div>\n", '');
                else if (jsons['messages'][i]['unread'] > 99)
                    MessageBlock = MessageBlock.replace("RP-Unread", '99+');
                else
                    MessageBlock = MessageBlock.replace("RP-Unread", jsons['messages'][i]['unread']);
                MessageList.append(MessageBlock);
            }
            MessageList.append(MessageEnd);
            setTimeout("PollListData()", 2000);
        },
        error: function () {
            PollListData()
        }
    })
}

//轮询消息数据
function PollMessagesData() {
    let FirstAvoid = false;
    const MessageBlockTemplate = '<div class="MessageTime">RP-Time</div>\n' +
        '        <div class="Message RP-Color" id="RP-ID">\n' +
        '            <div class="MessageTop">\n' +
        '                <div class="MessageName">RP-Name</div>\n' +
        '            </div>\n' +
        '            <div class="MessageBottom">\n' +
        '                <pre class="MessageContent">RP-Content</pre>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>';
    const MessageArea = $('#MessagesArea');
    $.ajax({
        url: "../Back/messagesCheck.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'username': NowChat['username'],
            'LastMessageID': NowChat['MaxID'],
        },
        success: function (result) {
            if (NowChat['username'] === null) {
                setTimeout("PollMessagesData()", 5000);
                return;
            }
            if (NowChat['username'] !== NowChat['UsrTmp']) {
                MessageArea.empty();
                NowChat['UsrTmp'] = NowChat['username'];
            }
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            if (jsons === -2 && FirstAvoid && WindowStack[WindowStack_Pointer] === "MessagesWindow") {
                alert("你与对方并非好友，会话将被关闭");
                JumpWindow('Main');
            }
            if (jsons === -2 && FirstAvoid && NowChat['username'] != null) FirstAvoid = true;

            for (let i = 1; i <= jsons['rows']; i++) {
                let MessageBlock = MessageBlockTemplate;
                NowChat['messages']['total']++;
                NowChat['messages'][NowChat['messages']['total']] = [];
                NowChat['messages'][NowChat['messages']['total']]['id'] = jsons[i]['id'];
                NowChat['messages'][NowChat['messages']['total']]['sender'] = jsons[i]['sender'];
                NowChat['messages'][NowChat['messages']['total']]['content'] = jsons[i]['message'];
                NowChat['messages'][NowChat['messages']['total']]['time'] = jsons[i]['sendTime'];
                NowChat['messages'][NowChat['messages']['total']]['state'] = jsons[i]['state'];
                if (NowChat['MaxID'] < NowChat['messages'][NowChat['messages']['total']]['id']) NowChat['MaxID'] = NowChat['messages'][NowChat['messages']['total']]['id'];
                MessageBlock = MessageBlock.replace('RP-Time', getDate(NowChat['messages'][NowChat['messages']['total']]['time'], 'yyyy-MM-dd hh:mm:ss'));
                let color = null;
                NowChat['messages'][NowChat['messages']['total']]['sender'] === NowChat['username'] ? color = 'OrangeMessage' : color = 'BlueMessage';
                MessageBlock = MessageBlock.replace('RP-Color', color);
                MessageBlock = MessageBlock.replace('RP-Name', NowChat['messages'][NowChat['messages']['total']]['sender']);
                MessageBlock = MessageBlock.replace('RP-Content', NowChat['messages'][NowChat['messages']['total']]['content']);
                MessageBlock = MessageBlock.replace('RP-ID', NowChat['messages'][NowChat['messages']['total']]['id']);
                MessageArea.append(MessageBlock);
            }
            setTimeout("PollMessagesData()", 1000);
        },
        error: function () {
            PollMessagesData();
        }
    });
    if (WindowStack[WindowStack_Pointer] === "MessagesWindow")
        $.ajax({
            url: "../Back/unreadCheck.php",
            type: "POST",
            dataType: 'jsonp',
            async: true,
            timeout: 5000,
            data: {'username': NowChat['username'],},
            success: function (result) {
                if (result === -1) NotLoginKick();
            },
            error: function () {
            }
        });
}

//发送消息
function SubmitMessage() {
    const UserText = $('#UserText');
    if (UserText.val() === '') return;
    $.ajax({
        url: "../Back/messageSend.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'receiver': NowChat['username'],
            'message': UserText.val(),
        },
        success: function (result) {
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            if (jsons === -2 && WindowStack[WindowStack_Pointer] === "MessagesWindow") {
                alert("你与对方并非好友，会话将被关闭");
                JumpWindow('Main');
            }
            if (jsons === 1) {
                // NowChat['messages']['total']++;
                // NowChat['messages'][NowChat['messages']['total']] = [];
                // NowChat['messages'][NowChat['messages']['total']]['content'] = UserText.val();
                // NowChat['messages'][NowChat['messages']['total']]['type'] = 'send';
            }
        },
        error: function () {

        }
    });
    let summary = UserText.val();
    if (UserText.val().length > 33) summary = UserText.val().substr(0, 30) + '...';
    $('#' + NowChat['username'] + ' .MessageSummary').html(summary);
    UserText.val('');
}

//搜索用户列表
function SearchFriends() {
    //用户列表模板
    const template = '<div class="MessageBlock" onclick="JumpWindow(\'UserInfWindow\',\'RP-Username\')">\n' +
        '                <img src="img/TestHead.jpeg" alt="portrait" class="rounded-circle portrait">\n' +
        '                <div class="MessageLeft">\n' +
        '                    <div class="MessageTittle">RP-Username</div>\n' +
        '                    <div class="MessageSummary">RP-Email</div>\n' +
        '                </div>\n' +
        '                <div class="MessageRight">\n' +
        '                    <img class="AddFriendIcon" src="img/add_%231296DB.png" alt="AddIcon">\n' +
        '                </div>\n' +
        '            </div>';
    const FriendSearchInput = $("#FriendSearchInput");
    const FriendListArea = $("#FriendListArea");
    if (FriendSearchInput.val() === '') return;
    $.ajax({
        url: "../Back/searchFriends.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'input': FriendSearchInput.val(),
        },
        success: function (result) {
            FriendListArea.empty();
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            for (let i = 1; i <= jsons['rows']; i++) {
                if (jsons[i] === null) continue;
                let output = template.replace("RP-Username", jsons[i]['username']);
                output = output.replace("RP-Username", jsons[i]['username']);
                output = output.replace("RP-Email", jsons[i]['email']);
                FriendListArea.append(output);
            }
        },
        error: function () {

        }
    });
}

//获取用户卡片信息
function GetUserInf(username) {
    //用户卡片信息模板
    const template = "<div class=\"Inf-List-Item\">\n" +
        "            <div class=\"Inf-Item-Top\">RP-Top</div>\n" +
        "            <div class=\"Inf-Item-Bottom\">RP-Bottom</div>\n" +
        "        </div>";
    //添加好友按钮模板
    const button = "<div id=\"Add-Friend-Button\" onclick='JumpWindow(\"SendRequestWindow\",\"RP-Username\")'>添加好友</div>";
    const area = $("#UserInfList");
    $.ajax({
        url: "../Back/getUserInf.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'username': username,
        },
        success: function (result) {
            area.empty();
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            let block = template.replace('RP-Top', '用户名');
            block = block.replace('RP-Bottom', jsons['username']);
            area.append(block);
            block = template.replace("RP-Top", "等级");
            block = block.replace('RP-Bottom', 'lvl-' + jsons['lvl']);
            area.append(block);
            block = template.replace("RP-Top", "邮箱");
            block = block.replace('RP-Bottom', jsons['email']);
            area.append(block);
            if (jsons['site'] != null) {
                block = template.replace("RP-Top", "个人网站");
                block = block.replace('RP-Bottom', jsons['site']);
                area.append(block);
            }
            block = button.replace("RP-Username", jsons['username']);
            area.append(block);
        },
        error: function () {

        }
    });
}

//发送添加好友请求
function RequestToAddFriend() {
    const input = $("#SendRequestWindow textarea");
    if (AddFocus === null) return;
    $.ajax({
        url: "../Back/requestChat.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'username': AddFocus,
            'requestMessage': input.val(),
        },
        success: function (result) {
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            if (jsons === -2) {
                alert("添加用户不满足条件");
                return;
            }
            if (jsons === -3) {
                alert("你与该用户已是好友");
                return;
            }
            if (jsons === -4) {
                alert("验证消息不可为空");
                return;
            }
            if (jsons === -5) {
                alert("你已经向该用户发送请求，请耐心等待对方通过请求");
                return;
            }
            JumpWindow("AddSuccessWindow");
        },
        error: function () {

        }
    });
}

//获取验证消息列表
function GetRequestsList() {
    //验证申请模板
    const template = '<div class="RequestBlock" id="Request-RP-Username">\n' +
        '            <div class="RequestType">好友申请</div>\n' +
        '            <div class="RequestContent">\n' +
        '                <img src="img/TestHead.jpeg" class="portrait-70px rounded-circle" alt="portrait">\n' +
        '                <div class="RequestRight">\n' +
        '                    <div class="Requester">RP-Username</div>\n' +
        '                    <br>\n' +
        '                    <div class="RequestText">RP-Msg</div>\n' +
        '                    <div class="AgreeArea">\n' +
        '                        <button type="button" class="btn btn-outline-success AgreeButton" onclick="AgreeRequest(\'RP-Username\')">同意</button>\n' +
        '                    </div>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '        </div>\n';
    //活动区域
    const Area = $("#RequestArea");
    $.ajax({
        url: "../Back/getRequestsList.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        success: function (result) {
            Area.empty();
            let jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            for (let i = 0; i < jsons['rows']; i++) {
                if (jsons[i]['requestMessage'].length > 24)
                    jsons[i]['requestMessage'] = jsons[i]['requestMessage'].substr(0, 21) + '...';
                let Block = template.replace("RP-Username", jsons[i]['sender']);
                Block = Block.replace("RP-Username", jsons[i]['sender']);
                Block = Block.replace("RP-Username", jsons[i]['sender']);
                if (jsons[i]['requestMessage'] == null) Block = Block.replace("RP-Msg", '申请加为好友');
                else Block = Block.replace("RP-Msg", jsons[i]['requestMessage']);
                Area.append(Block);
                if (jsons[i]['state'] === 1)
                    $("#Request-" + jsons[i]['sender'] + " button").replaceWith('<div class="AgreedText">已同意</div>');
                //<div class="AgreedText">已同意</div>
            }
        },
        error: function () {

        }
    });
}

function AgreeRequest(username) {
    $.ajax({
        url: "../Back/AgreeRequest.php",
        type: "POST",
        dataType: 'jsonp',
        async: true,
        timeout: 5000,
        data: {
            'username': username,
        },
        success: function (result) {
            let jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            if (jsons === 1) {
                $("#Request-" + username + " button").replaceWith('<div class="AgreedText">已同意</div>');
            }
        },
        error: function () {

        }
    });
}