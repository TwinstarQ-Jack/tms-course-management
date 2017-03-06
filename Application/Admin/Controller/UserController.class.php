<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller
{
    //入口
    public function manager() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function adduser() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function group() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function code() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //成员数目（除去老师）
    public function getusernum() {
        $data = intval(D('User')->getUserCount()) - 1;
        if($data === false) {
            return show(0, 'getUserNumFailed');
        } else{
            return show(1, 'getUserNumSuccessed', $data);
        }
    }
    //成员管理分页
    public function getuserpages() {
        $data['pages'] = intval(D('User')->getUserPageCount());
        if($data['pages'] === false) {
            return show(0, 'getUserPagesNumFailed');
        } else{
            return show(1, 'getUserPagesNumSuccessed', $data);
        }
    }
    public function getuserlists() {
        $page = $_POST['page'];
        $data = array();
        $list = D('User')->getUserList($data, $page);
        if($list === false){
            return show(0, 'getUserListFailed');
        } else{
            foreach($list as $k => $v){
                $list[$k]['rootrank'] = D('User')->dealrootrank($v['rootrank']);
            }
            return show(1, 'getUserListSuccessed', $list);
        }
    }
    //获取指定用户的资料
    public function getuserdetails() {
        $cid = $_POST['cid'];
        $ret = D('User')->getspecial($cid, array('CardID','Name','RootRank','Grade'));
        if($ret === false){
            return show(0, 'getUserDetailsFailed');
        }
        return show(1, 'getUserDetailsSuccessed', $ret);
    }
    //添加新用户
    public function addusercheck() {
        $cid = $_POST['usr'];
        $psw = $_POST['psw'];
        $psw2 = $_POST['psw2'];
        $que = $_POST['que'];
        $ans = $_POST['ans'];
        $name = $_POST['name'];
        $grade = $_POST['grade'];
        $rank = $_POST['rank'];
        $change = $_POST['change'];
        $this->isnull($cid, '用户名');
        $this->isnull($name, '姓名');
        $this->isnull($grade, '年级');
        //新增用户
        if($change == "0"){
            $salt = D("Login")->createSalt();
            $this->isnull($psw, '密码');
            $this->isnull($que, '问题');
            $this->isnull($ans, '答案');
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
                'Name' => $name, 'Grade' => $grade, 'RootRank' => $rank,
            );
            $res = D("Login")->registerToUsersDB($data);
            if (!$res) {
                return show(0, '新增失败，无法保存用户信息');
            }
            //将找回密码数据插入FindPsw数据库
            $ans = md5($ans.$salt);
            $data = array('CardID' => $cid, 'Question' => $que, 'Answer' => $ans);
            $res = D("Login")->registerToFindPSWDB($data);
            if (!$res) {
                return show(1, '新增成功，但无法保存用户找回密码信息');
            }
            return show(1, '新增用户'.$name.'成功，即将为您转入用户列表');
        }
        //该部分为修改
        else if($change == "1"){
            $data = array(
                'Name' => $name, 'Grade' => $grade, 'RootRank' => $rank,
            );
            $salt = null;
            if(trim($psw) != null){
                if ($psw != $psw2) {
                    return show(0, '两次密码输入不一致');
                } else{
                    $salt = D('User')->getspecial($cid, array('Salt'));
                    $salt = $salt['salt'];
                    $psw = md5($psw.$salt);
                    $data['Password'] = $psw;
                }
            }
            $ret = D('User')->changeUserData($cid, $data);
            if($ret === false){
                return show(0, '更新用户资料失败');
            }
            if(trim($que) != null){
                $this->isnull($ans, '找回问题答案');
                $ans = md5($ans.$salt);
                $data = array(
                    'Question' => $que, 'Answer' => $ans
                );
                $ret2 = D('User')->changeFindProblem($cid, $data);
                if($ret === false){
                    return show(1, '更新资料成功，更新找回问题、答案失败');
                }
                return show(1, '更新资料成功，更改数目为'.$ret.'，更新找回问题与答案成功，更改书目为'.$ret2);
            } else{
                return show(1, '更新资料成功，更改数目为'.$ret.'，不更新找回问题与答案');
            }
        }
        return show(0, '未定义操作');
    }
    //获取小组列表页数
    public function getgrouppages() {
        $paras['Grade'] = $_POST['grade'];
        $data['pages'] = D('Group')->getGroupPageCount($paras);
        if($data['pages'] === false) {
            return show(0, 'getGroupPagesNumFailed');
        } else{
            return show(1, 'getGroupPagesNumSuccessed', intval($data['pages']));
        }
    }
    //获取小组数
    public function getgroupnum() {
        $paras['Grade'] = D('Setting')->getValue('nowterm');;
        $data['pages'] = D('Group')->getGroupCount($paras);
        if($data['pages'] === false) {
            return show(0, 'getGroupNumFailed');
        } else{
            return show(1, 'getGroupNumSuccessed', intval($data['pages']));
        }
    }
    //获取当前分页的小组列表+成员清单
    public function getgrouplists() {
        $page = $_POST['page'] || 1;
        $data['Grade'] = $_POST['grade'];
        $list = D('Group')->getGroupList($data, $page);
        if($list === false){
            return show(0, 'getGroupListFailed');
        } else{
            foreach($list as $k => $v){
                $ret = D('Group')->getMemberByGID($v['id'], array('name'));
                foreach($ret as $rk => $rv){
                    $list[$k]['member'] .= ($rv['name'].',');
                }
            }
            return show(1, 'getGroupListSuccessed', $list);
        }
    }
    //初始化小组管理相关设置
    public function getgroupsettings() {
        $require = array('regleader', 'regmember', 'groupslimit', 'groupsmemberlimit');
        $data = array();
        $errors = 0;
        $errors_msg = "";
        foreach ($require as $k => $v){
            $data[$v] = D('Setting')->getValue($v);
            if($data[$v] === false){
                $errors++;
                $errors_msg .= ($v.",");
            }
        }
        if($errors > 0){
            return show(0, $errors_msg.'获取失败');
        } else{
            return show(1, 'getGroupSettingsSuccessed', $data);
        }
    }
    //传递小组设置与结果
    public function savegradeset() {
        $name = $_POST['name'];
        $value = $_POST['value'];
        $cname = $_POST['cname'];
        $ret = D('Setting')->setValue($name, $value);
        if($ret === false){
            return show(0, $cname.'保存失败');
        } else{
            return show(1, $cname.'保存成功，影响条目数为'. $ret);
        }
    }
    public function savelimitedset() {
        $data = $_POST['data'];
        $error = 0;
        $errormsg = "";
        foreach($data as $k => $v){
            $name = $k;
            $value = $v;
            $ret = D('Setting')->setValue($name, $value);
            if($ret === false){
                $error++;
                $errormsg .= ($k.",");
            }
        }
        if($error > 0){
            return show(0, $errormsg.'保存失败，其他保存成功。');
        }
        return show(1, '保存成功');
    }
    //授权码相关
    public function getcodepages() {
        $ret = D('User')->getCodePageCount();
        if($ret === false) {
            return show(0, 'getCodePagesNumFailed');
        } else{
            return show(1, 'getCodePagesNumSuccessed', intval($ret));
        }
    }
    public function getcodelists() {
        $page = $_POST['page'] || 1;
        $data = array();
        $list = D('User')->getCodeList($data, $page);
        if($list === false){
            return show(0, 'getCodeListFailed');
        } else{
            foreach($list as $k => $v){
                    switch($v['status']){
                        case '1' : $list[$k]['status'] = '可使用'; break;
                        case '0' : $list[$k]['status'] = '已使用'; break;
                        case '-1' : $list[$k]['status'] = '已过期'; break;
                    }
                }
            }
            return show(1, 'getCodeListSuccessed', $list);
    }
    public function addcode() {
        $cid = $_POST['cid'];
        $this->isnull($cid, '一卡通号');
        $checkusr = D('User')->queryreged($cid);
        if(!$checkusr){
            return show(0, '一卡通号不存在');
        } else{
            $ret = D('User')->createCode($cid);
            if($ret['num'] === false){
                return show(0, '创建授权码，写入数据库失败');
            }
            return show(1, 'addcodesuccessed', $ret);
        }
    }
}