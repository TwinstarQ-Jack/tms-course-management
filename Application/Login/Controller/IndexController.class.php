<?php
namespace Login\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index() {
        if ($_SESSION['TMSUser']){
            redirect('/user/index');
        }
        $this->display();
    }
    //退出
    public function loginout(){
        unset($_SESSION['TMSUser']);
        $data = array();
        if($_SESSION['TMSUser']) {
            return show(0, '删除SESSION失败');
        } else {
            return show(1, '退出成功');
        }
    }
    //处理空数据，返回错误值
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //处理登录数据
    public function check()
    {
        $cid = $_POST['usr'];
        $psw = $_POST['psw'];
        $this->isnull($cid, '用户名');
        $this->isnull($psw, '密码');
        $ret = D('Login')->getAccountByCardID($cid);
        $salt = $ret['salt'];
        //检查是否已有
        if (!$ret) {
            return show(0, '该用户不存在');
        }
        if ($ret['password'] != md5($psw.$salt)) {
            D('Public')->updateEvents($cid, '试图登录系统错误，输入的密码为'.$psw);
            return show(0, '密码错误');
        }
        D("Login")->updateLoginTimeByCardId($ret['cardid']);
        $ret['group'] = D('Group')->checkJoinGIDByCID($cid);
        unset($ret['password']);
        unset($ret['salt']);
        //写入session
        session('TMSUser', $ret);
        return show(1, '登录成功，即将为你转入首页');
    }

}