/**
 * admin.js
 * 管理员后台板块的相关处理
 * author: TwinstarQ
 */
layui.use(['form'],function () {
    var form = layui.form;
});
//按钮跳转操作
$('#admin-notice-add').click(function () {
    location.href = '/admin/notice/add';
});
$('#admin-user-adduser').click(function () {
    location.href = '/admin/user/adduser';
});
$('#admin-refresh').click(function () {
    location.href = this.href;
});
//具体行为操作
$('#admin-notice-addsubmit').click(function () {
    loading.show();
    var content = $('#admin-notice-editor').text();
    var jumpto = $('input[name="jumpto"]').val();
    var deadline = $('#admin-notice-date').val();
    var change = $('#rchangestate').val()
    public.isnull(content,'内容');
    public.isnull(deadline, '有效期');
    var data = {"msg":content, "ddl":deadline, "url":jumpto, "change":change};
    if(change == "1"){
        data = {"msg":content, "ddl":deadline, "url":jumpto, "change":change, "id":$.hash("id")};
    }
    $.post(admin_api.notice_add, data, function (result) {
        loading.end();
        if(result.status == 1){
            return dialog.success(result.message, '/admin/notice/course');
        }
        if(result.status == 0){
            return dialog.error(result.message);
        }
    },'JSON');
});
$('#admin-sendmsg-addsubmit').click(function () {
    loading.show();
    var content = $('#admin-sendmsg-editor').text();
    var jumpto = $('input[name="sendmsg-jumpto"]').val();
    var sendto = $('input[name="sendmsg-sendto"]').val();
    public.isnull(sendto, '发送对象');
    if(sendto.leng > 20){
        return dialog.error('用户名超出系统限制，请重新输入');
    }
    $.post(user_api.is_user, {"cid": sendto}, function (r) {
        if(r.status == 1){
            if(parseInt(r.data) > 0){
                public.isnull(content,'内容');
                var data = {'sendto': sendto, 'msg': content, 'url': jumpto};
                $.post(admin_api.sendmsg, data, function (r) {
                    loading.end();
                    if(r.status == 1){
                        return dialog.success(r.message, '/admin/notice/sendmsg');
                    }
                    if(r.status == 0){
                        return dialog.error(r.message);
                    }
                },'JSON');
            } else{
                loading.end();
                return dialog.error('无此用户，请重新确认');
            }

        } else{
            loading.end();
            return dialog.error('查询失败');
        }
    },"JSON");
});
$('#admin-sendmsg-search').click(function () {
    layer.open({
        type: 2,
        title: '查询用户一卡通号',
        shadeClose: true,
        shade: 0.8,
        area: ['300px', '80%'],
        content: '/user/account/findcid' //iframe的url
    });
});
$('#admin-user-addusersubmit').click(function () {
    loading.show();
    //获取相关信息
    var usr = $('input[name="rcardaccount"]').val();
    var psw = $('input[name="rpassword"]').val();
    var psw2 = $('input[name="rpassword2"]').val();
    var que = $('input[name="rquestion"]').val();
    var ans = $('input[name="ranswer"]').val();
    var name = $('input[name="rname"]').val();
    var grade = $('select[name="rgrade"]').val();
    var rank = $('input[name="root"]:checked').val();
    var change = $('#rchangestate').val();

    public.isnull(usr, '用户名');
    public.isnull(name, '姓名');
    public.isnull(grade, '年级');
    if(grade.length != 4) {
        dialog.error('年级必须为4位数');
        exit();
    }
    if(name.length > 20) {
        dialog.error('姓名超过了规定数目字符');
        exit();
    }
    if(change == "0"){
        public.isnull(psw, '密码');
        public.isnull(que, '问题');
        public.isnull(ans, '答案');
        if(que.length > 60 || ans.length > 60) {
            dialog.error('问题或者答案超过规定数目字符');
            exit();
        }
        if(psw != psw2) {
            dialog.error('两次密码输入不一致');
            exit();
        }
    }
    var deal_url = admin_api.user_mgr_add;
    var data = {'usr':usr, 'psw':psw, 'psw2':psw2, 'que':que, 'ans':ans, 'name':name, 'grade':grade, 'rank':rank, 'change':change};
    $.post(deal_url, data, function (result) {
        loading.end();
        if(result.status == 0) {
            return dialog.error(result.message);
        }
        if(result.status == 1) {
            return dialog.success(result.message, '/admin/user/manager');
        }
    },"JSON");
});
$('#admin-index-change').click(function () {
    layui.use('layer', function () {
        var layer = layui.layer;
        layer.open({
            skin: 'layui-layer-molv',
            content : '修改当前上课年级会影响所有学生的显示页面，您确定要修改吗？',
            icon: 3,
            btn : ['是','否'],
            yes : function(){
                loading.show();
                var value = $('select[name="rgrade"]').val();
                var send = {'grade': value};
                $.post(admin_api.nowgrade_set, send, function (r) {
                    loading.end();
                    if(r.status == 1){
                        return dialog.success('修改当前上课年级成功，影响条目数为：'+ r.data, '/admin/index/setgrade');
                    }
                    if(r.status == 0){
                        return dialog.error('修改当前上课年级失败，原因为：'+ r.message);
                    }
                },'JSON');
            },
        });
    });


});