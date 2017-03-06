<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class UserModel extends Model {
	private $_db_user = '';
    private $_db_findpsw = '';
    private $_db_code = '';

    public function __construct() {
        $this->_db_user = M('users');
        $this->_db_findpsw = M('findpsw');
        $this->_db_code = M('code');
    }
    //处理等级
    public function dealrootrank($rootrank) {
        switch($rootrank){
            case '0' : return '普通用户';
            case '1' : return '教师';
            case '-1' : return '被封禁';
            case '2' : return '助教';
            case '3' : return '系统管理员';
            default : return 'Error';
        }
    }
    //查询users表的内容
    public function queryusers($cid) {
        $ret = $_SESSION['TMSUser'];
        return $ret;
    }
    //查询是否已经注册
    public function queryreged($cid) {
        $ret = $this->_db_user->where('CardID="' .$cid. '"')->count();
        return $ret;
    }
    //获取user表指定用户的某个字段
    public function getspecial($cid, $paras = array()) {
        $ret = $this->_db_user->where('CardID="' .$cid. '"')->field($paras)->find();
        return $ret;
    }
    //查询指定姓名的cid
    public function getcidbyname($name) {
        $ret = $this->_db_user->where('Name="'. $name. '"')->field('CardID')->select();
        return $ret;
    }
    //查询指定cid的姓名
    public function getnamebycid($cid) {
        $ret = $this->_db_user->where('CardID="'. $cid. '"')->getField('Name');
        return $ret;
    }
    //获取当前年级的所有同学名单
    public function regUserList($paras = array(), $field = '') {
        $paras['Grade'] = D('Setting')->getValue('nowterm');
        $ret = $this->_db_user->where($paras)->field($field)->select();
        return $ret;
    }
    //用户总人数
    public function getUserCount($data = array()) {
        $ret = $this->_db_user->where($data)->count();
        return $ret;
    }
    //分页
    public function getUserPageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_user->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getUserList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_user->where($data)->order('RegTime desc')->limit($offset, $pageSize)
                               ->field(array('Password', 'Salt'), true)->select();
        return $list;
    }
    //更改用户资料
    public function changeUserData($cid, $data) {
        $ret = $this->_db_user->where('CardID="' .$cid. '"')->save($data);
        return $ret;
    }
    //更改用户的找回信息
    public function changeFindProblem($cid, $data) {
        $ret = $this->_db_findpsw->where('CardID="' .$cid. '"')->save($data);
        return $ret;
    }
    //确定授权码信息是否对应、是否有效（时间）
    public function checkCode($cid, $code) {
        $paras['Deadline'] = array('ELT', getPHPTime());
        $paras['CardID'] = $cid;
        $paras['Codevalue'] = $code;
        $paras['Status'] = 1;
        $ret = $this->_db_code->where($paras)->count();
        return $ret;
    }
    //更改授权码使用状态
    public function useCode($cid, $code) {
        $paras['CardID'] = $cid;
        $paras['Codevalue'] = $code;
        $ret = $this->_db_code->where($paras)->setField('Status', 0);
        return $ret;
    }
    //创建授权码
    public function createCode($cid){
        $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $data['Codevalue'] = substr(str_shuffle($str),10,16);
        $data['Deadline'] = date("Y-m-d H:i:s",strtotime("+1 day"));
        $data['Creator'] = $_SESSION['TMSUser']['name'];
        $data['CardID'] = $cid;
        $data['num'] = $this->_db_code->add($data);
        return $data;
    }
    //更新过期授权码状态
    private function updateDeadCode() {
        $data['Deadline'] = array("lt", getPHPTime());
        $this->_db_code->where($data)->setField('Status',-1);
    }
    public function getCodePageCount($data = array(), $pageSize = 15) {
        $this->updateDeadCode();
        $ret = $this->_db_code->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getCodeList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_code->where($data)->order('Status desc, ID desc')->limit($offset, $pageSize)->select();
        return $list;
    }
}
