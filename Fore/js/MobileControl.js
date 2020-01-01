function JumpToIndex() {
    $("#Main").css("z-index", "1000");
}

function JumpWindow(PageId) {
    $("#Main").css("z-index", "0");
    $("#test1").css("z-index",PageId);
}