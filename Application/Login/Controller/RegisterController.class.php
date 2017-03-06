<?php
namespace Login\Controller;
use Think\Controller;
class RegisterController extends Controller
{
    public function index() {
        if ($_SESSION['TMSUser']){
            redirect('/user');
        }
        $this->display();
    }
    //处理空数据，返回错误值
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //处理注册数据
    public function check() {
        $cid = $_POST['usr'];
        $psw = $_POST['psw'];
        $psw2 = $_POST['psw2'];
        $que = $_POST['que'];
        $ans = $_POST['ans'];
        $name = $_POST['name'];
        $grade = $_POST['grade'];
        $salt = D("Login")->createSalt();
        $this->isnull($cid, '用户名');
        $this->isnull($psw, '密码');
        $this->isnull($que, '问题');
        $this->isnull($ans, '答案');
        $this->isnull($name, '姓名');
        $this->isnull($grade, '年级');
        //检查是否已有
        $ret = D('User')->queryreged($cid);
        if ($ret['status'] == 1) {
            return show(0, '该用户已经注册，请检查输入是否有误');
        }
        if ($psw != $psw2) {
            return show(0, '两次密码输入不一致');
        }
        //打包、处理数据，插入User数据库
        $psw = md5($psw . $salt);
        $data = array(
            'CardID' => $cid, 'Password' => $psw, 'Salt' => $salt,
            'Name' => $name, 'Grade' => $grade
        );
        $res = D("Login")->registerToUsersDB($data);
        if (!$res) {
            return show(0, '注册失败，无法保存用户信息');
        }
        //保存进session
        $ret = D('Login')->getAccountByCardID($cid);
        session('TMSUser', $ret);
        //将找回密码数据插入FindPsw数据库
        $ans = md5($ans.$salt);
        $data = array('CardID' => $cid, 'Question' => $que, 'Answer' => $ans);
        $res = D("Login")->registerToFindPSWDB($data);
        if (!$res) {
            return show(1, '注册成功，但无法保存用户找回密码信息');
        }
        return show(1, '注册成功，即将为您转入首页');
    }
}