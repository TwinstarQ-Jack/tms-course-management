<?php
namespace User\Controller;
use Think\Controller;
class AccountController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    public function findcid() {
        D('Public')->IsUserSession();
        $this->display();
    }
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //获取当前用户的个人信息
    public function getuserprofiles() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $ret = D('User')->getspecial($cid, array('CardID','Name','RootRank','Grade','RegTime','LoginTime'));
        if($ret === false){
            return show(0, 'getUserProfilesFailed');
        }
        $ret['rootrank'] = D('User')->dealrootrank($ret['rootrank']);
        return show(1, 'getUserProfilesSuccessed', $ret);
    }
    //更改当前登录用户的个人信息
    public function editprofiles() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $name = $_POST['name'];
        $grade = $_POST['grade'];
        $psw = $_POST['psw'];
        $this->isnull($name, '姓名');
        $this->isnull($grade, '年级');
        $this->isnull($psw, '确认密码');
        //密码校验
        $dbdata = D('User')->getspecial($cid, array('Salt','Password'));
        $salt = $dbdata['salt'];
        $db_psw = $dbdata['password'];
        if(md5($psw.$salt) != $db_psw){
            D('Public')->updateEvents($cid, '在个人中心更改个人信息时使用了错误的密码');
            return show(0, "密码校验错误");
        } else{
            $data['Name'] = $name;
            $data['Grade'] = $grade;
            $ret = D('User')->changeUserData($cid, $data);
            if($ret === false){
                D('Public')->updateEvents($cid, '在个人中心更改姓名、年级失败');
                return show(0, '更新姓名、年级失败');
            }
            D('Public')->updateEvents($cid, '在个人中心更改姓名、年级信息成功');
            return show(1, '更新姓名、年级成功');
        }
    }
    //更改当前登录用户的找回账号信息
    public function editfindpsw() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $code = $_POST['code'];
        $que = $_POST['que'];
        $ans = $_POST['ans'];
        $this->isnull($code, '授权码');
        $this->isnull($que, '找回密码问题');
        $this->isnull($ans, '找回密码答案');
        $check = D('User')->checkCode($cid, $code);
        if($check === false) {
            return show(0, '验证授权码失败');
        } else if($check == 0){
            D('Public')->updateEvents($cid, '授权码'.$code.'失效，无法使用，更改找回密码信息失败');
            return show(0, '授权码'.$code.'失效');
        }
        //获取加密盐
        $dbdata = D('User')->getspecial($cid, array('Salt'));
        $salt = $dbdata['salt'];
        $data['Question'] = $que;
        $data['Answer'] = md5($ans.$salt);
        $fpret = D('User')->changeFindProblem($cid, $data);
        if($fpret === false) {
            D('Public')->updateEvents($cid, '使用授权码'.$code.'更改找回密码信息失败');
            return show(0, '更新找回密码信息失败');
        }
        D('User')->useCode($cid, $code);
        D('Public')->updateEvents($cid, '使用授权码'.$code.'更改了找回密码信息');
        return show(1, '更新找回密码信息成功');
    }
    //授权码验证
    public function checkcode() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $code = $_POST['code'];
        $ret = D('User')->checkCode($cid, $code);
        if($ret === false){
            return show(0, 'CodeIsInvaild');
        } else{
            return show(1, 'CodeIsVaild');
        }
    }
}