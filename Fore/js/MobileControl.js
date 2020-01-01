// function JumpToIndex() {
//     $("#Main").css("z-index", "1000");
// }
function NotLoginKick() {
    alert("您的登录已经失效或不可用，请重新登录");
    window.location.href = "LoginPage.html";
}

function JumpWindow(Window) {
    const Main = $("#Main");
    const MessagesWindow = $("#MessagesWindow");
    switch (Window) {
        case "Main":
            Main.show();
            MessagesWindow.hide();
            break;
        case "MessagesWindow":
            Main.hide();
            MessagesWindow.show();
    }
}

function SwitchInitialize() {
    $("#MainSettingsContainer").hide();
    $("#MessagesWindow").hide();
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
    const MessageBlockTemplate = "<div class=\"MessageBlock\" onclick=\"JumpWindow('MessagesWindow')\">\n" +
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
        // jsonp: "callback",
        //crossDomain: true,
        success: function (result) {
            MessageList.empty();
            const jsons = eval(result);
            if (jsons === -1) NotLoginKick();
            for (let i = 1; i <= jsons['rows']; i++) {
                let MessageBlock = MessageBlockTemplate;
                MessageBlock = MessageBlock.replace("RP-Tittle", jsons['messages'][i]['user_2']);
                MessageBlock = MessageBlock.replace("RP-Summary", jsons['messages'][i]['latestMessage']);
                if (jsons['messages'][i]['unread'] === 0) MessageBlock = MessageBlock.replace("<div class=\"RedDot\">RP-Unread</div>\n", '');
                else MessageBlock = MessageBlock.replace("RP-Unread", jsons['messages'][i]['unread']);
                MessageList.append(MessageBlock);
            }
            MessageList.append(MessageEnd);
            setTimeout("PollListData()", 5000);
        },
        error: function () {

        }
    })
}