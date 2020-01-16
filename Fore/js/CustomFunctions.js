/**
 * @return {string}
 */
function GetQueryString(name) {
    const reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    const r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

/**
 * @return {boolean}
 */
function JudgeDevice() {
    //PC返回true，PE返回false
    const userAgentInfo = navigator.userAgent;
    const Agents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"];
    let flag = true;
    for (let v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}

/**
 * @return {string}
 */
function BadLock() {
    return '&BadLock=FUCK_HACKERS';
}

function JumpForDevice() {
    if (JudgeDevice()) {
        window.location.href = "PCPage.html";
    } else {
        window.location.href = "MobilePage.html";
    }
}
