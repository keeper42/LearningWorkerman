/**
 * Author:幻音丶小涛
 * WebSite:http://www.acgxt.com
 * Date:2017/3/2
 * Time:6:51
 */
var serverStatus = 0;
$(function(){
    msg5("正在尝试连接服务器");
});
ws = new WebSocket("ws://127.0.0.1:10266");

ws.onopen = function() {
    serverStatus = 1;
    xt.toast("连接聊天服务器成功",4);
    msg3("连接服务器成功");
};
ws.onerror = function(){
    if(serverStatus==1){
        xt.toast("聊天服务器连接被中断...",2);
        msg4("聊天服务器连接被中断...");
    }else{
        xt.toast("连接聊天服务器失败...",2);
        msg4("连接服务器失败...");
    }
    serverStatus = 0;
};

$("#sendMessage").click(function(){
    sendMessage();
});
function sendMessage(){
    var val = $("#chat_input").val();
    val = val.replace(/[]/g,"");
    val = val.replace("\n","");
    val = val.replace("\r","");
    if(val==""||val.length==0){
        xt.toast("聊天发送消息不能为空",2);
        $("#chat_input").focus();
        return;
    }
    send({
        type:'public',
        msg:val
    });
    $("#chat_input").val("");
}
$("#chat_input").keypress(function(e){
    if(e.keyCode==13){
        e.preventDefault();
        sendMessage();
    }
});
ws.onmessage = function(e){
    var data = JSON.parse(e.data);
    if(data.type=='self'){
        msg2(data.msg);
        $(".chatMessageWarp")[0].scrollTop = $(".chatMessageWarp")[0].scrollHeight - $(".chatMessageWarp")[0].clientHeight;
    }else if(data.type=='other'){
        msg1(data.msg);
        $(".chatMessageWarp")[0].scrollTop = $(".chatMessageWarp")[0].scrollHeight - $(".chatMessageWarp")[0].clientHeight;
    }else if(data.type=='join'){
        changeOnlineCount(data.count);
        msg5(data.name+"加入了聊天室");
    }else if(data.type=='exit'){
        changeOnlineCount(data.count);
        msg5(data.name+"退出了聊天室");
    }

};
function changeOnlineCount(count){
    $("#onlineCount").text(count);
}
function msg1(msg){
    var html = "<li class=\"message1\">"+
        "<img src=\"Ui/images/avatar.jpg\" alt=\"头像\">"+
        "<p>"+msg+"</p>"+
        "</li>";
    $(".chatMessageWarp").append(html);
}
function msg2(msg){
    var html = "<li class=\"message2\">"+
        "<img src=\"Ui/images/avatar.jpg\" alt=\"头像\">"+
        "<p>"+msg+"</p>"+
        "</li>";
    $(".chatMessageWarp").append(html);
}
function msg3(msg){
    var html = "<li class=\"message3\"><span>"+msg+"</span></li>";
    $(".chatMessageWarp").append(html);
}
function msg4(msg){
    var html = "<li class=\"message4\"><span>"+msg+"</span></li>";
    $(".chatMessageWarp").append(html);
}
function msg5(msg){
    var html = "<li class=\"message5\"><span>"+msg+"</span></li>";
    $(".chatMessageWarp").append(html);
}
function send(jsonData){
    ws.send(JSON.stringify(jsonData));
}
var xt = {
    move: function (top) {
        var delay = 500;
        var docTop = document.documentElement.scrollTop;
        if (docTop == top) {
            return false;
        } else {
            if (docTop > top) {
                delay = (docTop / 2) / 2;
            } else {
                delay = (top - docTop) / 2;
            }
        }
        isReturn = true;
        $('body,html').animate({
            scrollTop: top
        }, delay);
        var e = $(this);
        setTimeout(function () {
            isReturn = false;
        }, delay);
    },
    ajax: function (type, url, dataType, data, success, error) {
        $.ajax({
            type: type,
            url: URLS + "/" + url,
            dataType: dataType,
            data: data,
            success: success,
            error: error
        });
    },
    toastFlag: false,
    toast: function (message, type, delay, fadeTime) {
        type = isNaN(type) ? 1 : type;
        fadeTime = isNaN(fadeTime) ? 300 : fadeTime;
        delay = isNaN(delay) ? 1000 : delay;
        $("#xt_toast #xt_toast_acgxt").text(message);
        if (this.toastFlag)return;
        this.toastFlag = true;
        var bgColor = "rgba(90,170,224,0.9)";
        switch (type) {
            case 2:
                bgColor = "rgba(224, 90, 90, 0.9)";
                break;
            case 3:
                bgColor = "rgba(249, 210, 117, 0.9)";
                break;
            case 4:
                bgColor = "rgba(101, 213, 79, 0.9)";
                break;
        }
        $("#xt_toast").css("backgroundColor", bgColor);
        $("#xt_toast").animate({top: '0'}, fadeTime);
        if (xt.toastFlag === false)return;
        setTimeout(function () {
            if (xt.toastFlag === true) {
                xt.toastFlag = false;
                $("#xt_toast").animate({top: '-60px'}, fadeTime);
            }
        }, delay);
    },
    toastOpen: function (message, type, fadeTime) {
        fadeTime = isNaN(fadeTime) ? 300 : fadeTime;
        $("#xt_toast #xt_toast_acgxt").text(message);
        if (this.toastFlag)return;
        this.toastFlag = true;
        var bgColor = "rgba(90,170,224,0.9)";
        switch (type) {
            case 2:
                bgColor = "rgba(224, 90, 90, 0.9)";
                break;
            case 3:
                bgColor = "rgba(249, 210, 117, 0.9)";
                break;
            case 4:
                bgColor = "rgba(101, 213, 79, 0.9)";
                break;
        }
        $("#xt_toast").css("backgroundColor", bgColor);
        $("#xt_toast").animate({top: '0'}, fadeTime);
        this.toastFlag = false;
    },
    toastClose: function (fadeTime) {
        fadeTime = isNaN(fadeTime) ? 300 : fadeTime;
        xt.toastFlag = false;
        $("#xt_toast").animate({top: '-60px'}, fadeTime);
    },
    confirm: function (message, bindFunction, closeButtonName, successButtonName) {
        $("#xt_confirm").fadeIn(0);
        if (closeButtonName != null) {
            $("#xt_confirm #close").text(closeButtonName);
        } else {
            $("#xt_confirm #close").text("取消");
        }
        if (successButtonName != null) {
            $("#xt_confirm #success").text(successButtonName);
        } else {
            $("#xt_confirm #success").text("确定");
        }
        $("#xt_confirm #xt_acg_confirm .acg_xt_content").text(message);
        $("#xt_confirm #xt_acg_confirm").addClass("show");
        if (typeof(bindFunction) == 'function') {
            $("#xt_acg_confirm .acg_xt_action #success").one("click", bindFunction);
        }
    },
    confirm_hide: function () {
        $("#xt_confirm #xt_acg_confirm").removeClass("show");
        $("#xt_confirm").fadeOut(300);
    },
    events: function () {
        $("#xt_acg_confirm .acg_xt_action #success").click(function () {
            xt.confirm_hide();
        });
        $("#xt_toast").click(function () {
            if (xt.toastFlag === false)return;
            xt.toastFlag = false;
            $("#xt_toast").animate({top: '-60px'}, 200);
        });

        $(".xt_slide .xt_slide_btn a").click(function () {
            var index = $(this).index();
            if (index == xt.slideIndex) {
                return;
            }
            xt.slide(slideData, index);
        });
    }
}
xt.events();