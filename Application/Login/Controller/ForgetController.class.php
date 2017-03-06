<?php
namespace Login\Controller;
use Think\Controller;
class ForgetController extends Controller
{
    public function index() {
        if ($_SESSION['TMSUser']){
            redirect('/user/index');
        }
        $this->display();
    }
    //处理空数据，返回错误值
    private function isnull($val, $message)
    {
        if (!trim($val)) {
            return show(0, $message . '不能为空');
        }
    }
    //处理获取忘记密码问题
    public function forgetqa() {
        $cid = $_POST['usr'];
        $this->isnull($cid, '用户名');
        $ret = D('Login')->getFindPswByCardID($cid);
        if(!$ret){
            return show(0, '用户名不存在');
        }
        session('FindPsw', $ret);
        return show(1, '获取成功', $ret);
    }
    //处理输入问题答案与重置密码
    public function resetpsw() {
        //从session中获取
        $cid = $_SESSION['FindPsw']['cardid'];
        $realans = $_SESSION['FindPsw']['answer'];
        $salt = D('Login')->getAccountByCardID($cid)['salt'];
        $psw = $_POST['psw'];
        $psw2 = $_POST['psw2'];
        //判断密码是否一致
        $this->isnull($psw, '密码');
        if ($psw != $psw2) {
            return show(0, '两次密码输入不一致');
        }
        //处理
        $ans = md5($_POST['ans'] . $salt);
        if($ans != $realans) {
            D('Public')->updateEvents($cid, '通过找回密码试图更改密码失败。');
            return show(0, '找回密码的答案错误');
        }
        $psw = md5($psw.$salt);
        $ret = D('Login')->resetPswByCardID($cid, $psw);
        if(!is_numeric($ret) ){
            return show(0, '更改密码失败');
        }
        D('Public')->updateEvents($cid, '通过找回密码界面更改了系统密码。');
        //销毁当前保存的所有session
        session_destroy();
        return show(1, '更改密码成功，请重新登录，更新的条数为'.$ret);
    }

}