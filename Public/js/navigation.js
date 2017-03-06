/**
 * navigation.js
 * 导航栏（顶部、底部）初始化
 * author: TwinstarQ
 */
//模块导入(element:导航栏部分)
layui.use(['element','util'],function () {
    var element = layui.element,
        util = layui.util;
    //右下角固定
    util.fixbar({top: '&#xe604;'
        ,bgcolor: '#8a8488'
        ,css:{bottom:160,right:20}
    });
});
//监视tms-ready class
$('.tms-ready').click(function () {
    dialog.toconfirm('搬砖工正在紧急基建，请耐心等待~');
    return false;
});
//初始化函数
$(document).ready(function () {
    initnavbar();
    getUnReadMsgNum();
});
window.onload = function () {
    badgecheck();
    fixfooter();
    fixfootersidebar();
};
//底部栏处理
function fixfooter() {
    if($('#wrapper').height() < ($(window).height() - 250)){
        $('#footer-nosidebar').addClass('footer-fix');
    }else {
        $('#footer-nosidebar').removeClass('footer-fix');
    }
}
function fixfootersidebar() {
    if($('#wrapper').height() < ($(window).height() - 250)){
        $('#footer-withsidebar').addClass('footer-sidebar-fix');
        $('#footer-withsidebar').css('width',($('#wrapper').width()-200)+"px");
    }else {
        $('#footer-withsidebar').removeClass('footer-sidebar-fix');
        $('#footer-withsidebar').css('width', null);
    }
}
//获取当前登录状态，初始化顶栏
function initnavbar() {
    var initnavbar = '/user/public/initnav';
    $.post(initnavbar, null, function (result) {
        if(result.status == 1){
            $('#navmenu,#mobile-logined,#navmsgnum,#mobile-navmsgnum').removeClass("layui-hide");
            $('#navlogin,#navregister,#mobile-nologin').hide();
            $('#username,#mobile-username').text(result.data['name']);
            var level = result.data['rootrank'];
            if(level == 1 || level == 2 || level == 3){
                $('#navadmin,#mobile-admin').removeClass("layui-hide");
            }
        }
        if(result.status == 0){
            $('#mobile-username').text('未登录');
        }
    },"JSON");
    $('nav').after('<div style="height:'+($('nav').height()+3)+'px"></div>');
}
//顶部栏滚动
$(window).scroll(function () {
    $t = $(window).scrollTop();
    //85%透明
    if($t > $('nav').height() + 30){
        $('nav').addClass('nav-transparent');
    } else {
        $('nav').removeClass('nav-transparent');
    }
    fixfooter();
    fixfootersidebar();
}).trigger('scroll');
//处理退出事务(id:navloginout)
$("#navloginout,#mobile-navloginout").click(function() {
    layer.open({
        skin: 'layui-layer-molv',
        content : "是否确认退出系统？",
        icon: 3,
        btn : ['是','否'],
        yes : function(){
            var url = '/login/index/loginout';
            loading.show();
            $.post(url, null, function (result) {
                loading.end();
                if(result.status == 1){
                    return dialog.success(result.message, '/');
                }
                return dialog.error("退出失败"+result.message);
            },"JSON");
        },
    })
});
//手机侧边栏处理
$(".site-tree-mobile").click(function () {
    $('body').addClass('site-mobile');
});
$(".site-mobile-shade").click(function () {
    $('body').removeClass('site-mobile');
})
//badge处理
function badgecheck() {
    $(".badge").each(function () {
        $(this).addClass('layui-hide');
        if($(this).text() != "0") {
            $(this).removeClass('layui-hide');
        }
    })
}
//获取未读消息数目
function getUnReadMsgNum() {
    var deal_url = '/user/public/initmsgnum';
    $.post(deal_url, null, function (result) {
        if(result.status == 1){
            if(result.data['num'] > 0){
                $('#unreadmessage').text(result.data['num']);
                $('#navshownum').text(result.data['num']);
                $('#mobile-navshownum').text(result.data['num']);
            }
            $('#unreadmsg').text(result.data['num']);
        }
        if(result.status == 0){
            layer.msg("未读消息数获取失败");
        }
    },"JSON");
}