/**
 * user.js
 * 用户板块的相关处理
 * author: TwinstarQ
 */
layui.use(['form'],function () {
    var form = layui.form();
});
//user页面：如何获取积分
$('#getcreditships').click(function () {
    return dialog.toconfirm("获取方式：作业及时提交、参与讨论、讨论获得赞赏、获得推荐等方式均可获得课程贡献值。贡献值不作为最后课程得分的评分参考。");
});
//重置找回密码问题与答案的处理
function getresetcode() {
    return dialog.toconfirm("获取方式：联系助教或者系统管理员获取授权码，授权码生成后有效期为一天。");
};
//index数据获取与处理
function initIndexShowData() {
    $.post(user_api.get_credits, null, function (result) {
        if(result.status == 1){
            $('#userindex-creditnum').text(result.data);
        }
        if(result.status == 0){
            msg.show("获取当前用户课程贡献值失败");
        }
    },"JSON");
    $.post(user_api.get_notfinhw, null, function (r) {
        if(r.status == 1){
            $('#userindex-ufhw').text(r.data['person']);
            $('#userindex-ufgw').text(r.data['group']);
        }
        if(r.status == 0){
            msg.show("获取当前用户未完成作业失败");
        }
    },"JSON");
};
//查找cid
$('#user-account-addsubmit').click(function () {
    $('#user-account-queryrst').html("");
    var name = $('input[name="user-account-name"]').val();
    var para = {"name": name};
    $.post(user_api.search_cid, para, function (result) {
        if(result.status == 1){
            if(result.data == null){
                msg.show("该用户不存在");
            } else{
                $.each(result.data, function (index, r) {
                    $('#user-account-queryrst').append($('<li/>').html(r.cardid));
                });
            }
        }
        if(result.status == 0){
            msg.show("获取用户失败");
        }
    },"JSON");
});