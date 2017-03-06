<?php
namespace User\Controller;
use Think\Controller;
class GroupController extends Controller
{
    //判断是否加入小组，以进行相关操作
    private function checkMember() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $ret = D('Group')->checkJoinStatusByCID($cid);
        if ($ret === false) {
            return -1;
        }
        return $ret;
    }
    public function index() {
        D('Public')->IsUserSession();
        if($this->checkMember() == 0){
            redirect('/user/group/join');
        }
        $this->display();
    }

    public function join() {
        D('Public')->IsUserSession();
        if($this->checkMember() == 1){
            redirect('/user/group');
        }
        $this->display();
    }
    //获取当前已注册的小组数目
    public function getregterms() {
        $num = D('Group')->regtermNum();
        if($num === false){
            return show(0, '获取已注册小组数目失败');
        } else{
            return show(1, 'getregtermssuccessed', $num);
        }
    }
    //创建新组并成为组长
    public function reggroup() {
        D('Public')->NotUserRet();
        if($this->checkMember() == 1){
            return show(0, '你已经加入了组，不需要创建新组。');
        } else{
            if(D('Setting')->getValue('regleader') != 1) {
                return show(0, '当前未开放注册');
            }
            $limit = D('Setting')->getValue('groupslimit');
            $reged = D('Group')->regtermNum();
            if($limit === false || $reged === false){
                return show(0, '获取已经注册和限制数目失败');
            }
            if($reged >= $limit){
                return show(0, '已经达到可注册组数的上限');
            }
            $st = D('Group')->addnewGroup();
            switch($st){
                case -1: return show(0, '注册新小组失败');break;
                case -2: D('Public')->updateEvents($_SESSION['TMSUser']['cardid'], '注册新小组成功，但无法加入成员，请联系管理员进行人工处理');
                         return show(1, '注册新小组成功，但无法加入成员，请联系管理员进行人工处理');break;
                default: D('Public')->updateEvents($_SESSION['TMSUser']['cardid'],'注册新小组成功，小组号为'.$st);
                    return show(1, '注册新小组成功，小组号为'.$st);break;
            }
        }
    }
    //读取小组全体成员
    public function getgroupmembers() {
        $gid = $_POST['gid'];
        $memberlist = D('Group')->getMemberByGID($gid, array('CardID', 'Name'));
        if($memberlist === false){
            return show(0, '获取成员失败');
        } else if($memberlist === array()){
            return show(0, '小组号'.$gid.'不存在');
        } else{
            return show(1, 'getGroupMembersListSuccessed', $memberlist);
        }
    }
    //加入小组
    public function regjoingroup() {
        $gid = $_POST['gid'];
        $name = $_SESSION['TMSUser']['name'];
        $cid = $_SESSION['TMSUser']['cardid'];
        if(D('Setting')->getValue('regmember') != 1) {
            return show(0, '当前未开放成员加入功能');
        }
        $limit = D('Setting')->getValue('groupsmemberlimit');
        $reged = D('Group')->regmemberNum($gid);
        if($limit === false || $reged === false){
            return show(0, '获取成员和限制数目失败');
        }
        if($reged >= $limit){
            return show(0, '已经达到成员上限');
        }
        $ret = D('Group')->addnewMember($gid, $cid, $name);
        switch($ret){
            case -1: return show(0, '加入小组失败，错误：保存失败');break;
            default: D('Public')->updateEvents($_SESSION['TMSUser']['cardid'],'加入了小组'.$gid);
                     return show(1, '加入小组成功');
        }
    }
    //以SESSION为搜索条件，获取全组数据
    public function getloginergroup() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $gid = D('Group')->getMemberGIDByOthers(array('cardid'=>$cid));
        if($gid === false){
            return show(0, '获取当前用户所属小组失败');
        }
        $list = D('Group')->getMemberByGID($gid);
        if($list === false){
            return show(0, '获取当前用户所属小组成员失败');
        }
        return show(1, 'getloginergroupsuccessed', $list);
    }
}