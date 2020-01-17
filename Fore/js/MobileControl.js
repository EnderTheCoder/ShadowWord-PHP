// function JumpToIndex() {
//     $("#Main").css("z-index", "1000");
// }

let NowChat = [];
NowChat['username'] = null;
NowChat['messages'] = [];
NowChat['messages']['total'] = 0;
NowChat['MaxID'] = 0;
let NowWindow = "Main";

function NotLoginKick() {
    alert("登录已经失效或不可用，请重新登录");
    window.location.href = "LoginPage.html";
}

function JumpWindow() {
    const Window = arguments[0];
    const Main = $("#Main");
    const MessagesWindow = $("#MessagesWindow");
    const TopUserName = $('#TopUserName');
    switch (Window) {
        case "Main":
            Main.show();
            MessagesWindow.hide();
            NowWindow = "Main";
            break;
        case "MessagesWindow":
            NowWindow = "MessagesWindow";
            NowChat['username'] = arguments[1];
            NowChat['messages']['total'] = 0;
            if (NowChat['username'].length > 13) TopUserName.html(NowChat['username'].substr(0, 9) + '...');
            TopUserName.html(NowChat['username']);
            Main.hide();
            MessagesWindow.show();
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
}

function SwitchInitialize() {
    $("#MainSettingsContainer").hide();
    $("#MessagesWindow").hide();
    // $("#Main").hide();
}

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

function PollMessagesData() {
    let FirstAvoid = false;
    const MessageBlockTemplate = '<div class="MessageTime">RP-Time</div>\n' +
        '        <div class="Message RP-Color">\n' +
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
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            if (jsons === -2 && FirstAvoid && NowWindow === "MessagesWindow") {
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
                MessageArea.append(MessageBlock);
            }
            setTimeout("PollMessagesData()", 1000);
        },
        error: function () {
            PollMessagesData();
        }
    });
    if (NowWindow === "MessagesWindow")
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
            if (jsons === -2 && NowWindow === "MessagesWindow") {
                alert("你与对方并非好友，会话将被关闭");
                JumpWindow('Main');
            }
            if (jsons === 1) {
                NowChat['messages']['total']++;
                NowChat['messages'][NowChat['messages']['total']] = [];
                NowChat['messages'][NowChat['messages']['total']]['message'] = UserText.val();
                NowChat['messages'][NowChat['messages']['total']]['type'] = 'send';
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
