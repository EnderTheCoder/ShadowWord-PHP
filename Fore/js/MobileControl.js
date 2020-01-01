// function JumpToIndex() {
//     $("#Main").css("z-index", "1000");
// }

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
    switch (Page){
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