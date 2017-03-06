/**
 * login.js
 * 登录板块的相关处理
 * author: TwinstarQ
 */
layui.use('form',function () {
    var form = layui.form();
});
$('document').ready(function () {
    init_grade();
});
//登录功能异步处理
var login = {
    check : function () {
        loading.show();
        //获取登录页面的用户名、密码
        var usr = $('input[name="lcardaccount"]').val();
        var psw = $('input[name="lpassword"]').val();
        public.isnull(usr, '用户名');
        public.isnull(psw, '密码');
        var deal_url = plugin_url.login_url;
        var data = {'usr':usr, 'psw':psw};
        //执行异步请求
        $.post(deal_url, data, function (result) {
            loading.end();
            if(result.status == 0) {
                return dialog.error(result.message);
            }
            if(result.status == 1) {
                return dialog.success(result.message, plugin_url.success_url);
            }
        },"JSON");
    }
}
//注册功能异步处理
var register = {
    check : function () {
        loading.show();
        //获取相关信息
        var usr = $('input[name="rcardaccount"]').val();
        var psw = $('input[name="rpassword"]').val();
        var psw2 = $('input[name="rpassword2"]').val();
        var que = $('input[name="rquestion"]').val();
        var ans = $('input[name="ranswer"]').val();
        var name = $('input[name="rname"]').val();
        var grade = $('select[name="rgrade"]').val();

        public.isnull(usr, '用户名');
        public.isnull(psw, '密码');
        public.isnull(que, '问题');
        public.isnull(ans, '答案');
        public.isnull(name, '姓名');
        public.isnull(grade, '年级');

        if(que.length > 60 || ans.length > 60) {
            dialog.error('问题或者答案超过规定数目字符');
            exit();
        }
        if(grade.length != 4) {
            dialog.error('年级必须为4位数');
            exit();
        }
        if(name.length > 20) {
            dialog.error('姓名超过了规定数目字符');
            exit();
        }
        if(psw != psw2) {
            dialog.error('两次密码输入不一致');
            exit();
        }
        var deal_url = plugin_url.register_url;
        var data = {'usr':usr, 'psw':psw, 'psw2':psw2, 'que':que, 'ans':ans, 'name':name, 'grade':grade};
        $.post(deal_url, data, function (result) {
            loading.end();
            if(result.status == 0) {
                return dialog.error(result.message);
            }
            if(result.status == 1) {
                return dialog.success(result.message, plugin_url.success_url);
            }
        },"JSON");
    }
}
//找回账号功能异步处理
var forget = {
    getquestion : function () {
        loading.show();
        var usr = $('input[name="fcardaccount"]').val();
        if(!usr) {
            usr = $('#fcardaccount').text();
        }
        public.isnull(usr, '用户名');
        var deal_url = plugin_url.forget_getq_url;
        var data = {'usr':usr};
        $.post(deal_url, data, function (result) {
            loading.end();
            if(result.status == 0) {
                return dialog.error(result.message);
            }
            if(result.status == 1) {
                $('input[name="fcardaccount"]').attr("disabled", "disabled");
                $('input[name="fcardaccount"]').addClass("layui-disabled");
                $('#fgetquestion').removeAttr("onclick");
                $('#fgetquestion').addClass("layui-btn-disabled");
                $('input[name="fanswer"]').removeAttr("disabled");
                $('input[name="fpassword"]').removeAttr("disabled");
                $('input[name="fpassword2"]').removeAttr("disabled");
                $('#fresetpsw').removeClass("layui-btn-disabled");
                $('#fshowquestion').text(result.data["question"]);
                $('#fresetpsw').attr("onclick", "forget.checkqa();")
                return dialog.toconfirm(result.message);
            }
        },"JSON");
    },
    checkqa : function () {
        loading.show();
        var deal_url = plugin_url.forget_resetpsw_url;
        var psw = $('input[name="fpassword"]').val();
        var psw2 = $('input[name="fpassword2"]').val();
        var ans = $('input[name="fanswer"]').val();
        var jump_url = plugin_url.success_login_url;
        public.isnull(ans, '答案');
        public.isnull(psw, '密码');
        if(psw != psw2) {
            dialog.error('两次密码输入不一致');
            exit();
        }
        var data = {'ans':ans , 'psw':psw, 'psw2':psw2};
        $.post(deal_url, data, function (result) {
            loading.end();
            if(result.status == 0) {
                return dialog.error(result.message);
            }
            if(result.status == 1) {
                return dialog.success(result.message, jump_url);
            }
        },"JSON");
    }
}

//处理年级下拉菜单（注册页面）
function init_grade() {
    var mydate = new Date();
    var thisyear = mydate.getFullYear();
    var select = $('select[name="rgrade"]');
    for(var i = 2014; i <= (thisyear-2); i++) {
        select.append("<option value="+i+">"+i+"</option>");
    }
}
