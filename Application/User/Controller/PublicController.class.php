<?php
namespace User\Controller;
use Think\Controller;
class PublicController extends Controller
{
    //initnav: 读取已经登录的SESSION记录并返回给前台进行初始化
    public function initnav() {
        if($_SESSION['TMSUser']){
            $res['name'] = $_SESSION['TMSUser']['name'];
            $res['rootrank'] = $_SESSION['TMSUser']['rootrank'];
            return show(1, 'Logined', $res);
        }
        return show(0, 'Unlogined');
    }
    //initmsgnum：导航栏消息数目查询(ajax)
    public function initmsgnum()
    {
        $cid = $_SESSION['TMSUser']['cardid'];
        $num = D('PushMsg')->unreadNumByCid($cid);
        $data['num'] = $num;
        if (is_numeric($num)) {
            return show(1, 'Success', $data);
        } else {
            return show(0, 'Fail');
        }
    }
    //getcredit:获取贡献值
    public function getcredit() {
        $paras[0] = "Credits";
        $ret = D('User')->getspecial($_SESSION['TMSUser']['cardid'], $paras);
        $ret = $ret['credits'];
        if($ret === false){
            return show(0, 'getcreditFailed');
        }
        return show(1, 'getcreditSuccessed', $ret);
    }
    //searchcid:获取指定真实姓名的cardid
    public function searchcid() {
        $name = $_POST['name'];
        $ret = D('User')->getcidbyname($name);
        if($ret == false){
            return show(0, 'QueryCidFailed');
        }
        return show(1, 'QueryCidSuccess', $ret);
    }
    //isuser:查找cid是否已经注册
    public function isuser() {
        $cid = $_POST['cid'];
        $ret = D('User')->queryreged($cid);
        if(is_numeric($ret)){
            return show(1, 'isuser', $ret);
        }
        return show(0, 'notuser');
    }
}