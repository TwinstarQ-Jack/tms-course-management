<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class PublicModel extends Model {
	private $_db_events = '';
    public function __construct() {
        $this->_db_events = M('events');
    }
    //更新用户关键事件
	public function updateEvents($cid, $contents) {
	    $ip = get_client_ip();
	    $data = array('CreatorID' => $cid, 'IPRecord' => $ip, 'Contents' => $contents);
        $this->_db_events->add($data);
    }
    //检测不到用户SESSION的处理
    public function IsUserSession() {
        if(!$_SESSION['TMSUser']) {
            redirect('/login#;user=0');
        }
        if($_SESSION['TMSUser']['rootrank'] == -1) {
            session_destroy();
            redirect('/login#;rank=0');
        }
    }
    //检测用户管理员身份的合理性
    public function IsAdminSession() {
        $admin_code = true;
        switch($_SESSION['TMSUser']['rootrank']){
            case 1 : break;
            case 2 : break;
            case 3 : break;
            default: $admin_code = false;
        }
        if(!$admin_code) {
            redirect('/user');
        }
    }
    //非法调用接口判断
    public function NotUserRet() {
        if(!$_SESSION['TMSUser'] ) {
            return show(0, 'NoUserAuth');
        }
    }
    public function NotAdminRet() {
        $admin_code = true;
        switch($_SESSION['TMSUser']['rootrank']){
            case 1 : break;
            case 2 : break;
            case 3 : break;
            default: $admin_code = false;
        }
        if(!$admin_code) {
            return show(0, 'NoAdminAuth');
        }
    }
    //事件日志读取
    public function getEventPageCount($cid, $data = array(), $pageSize = 10) {
        $data['CreatorID'] = $cid;
        $ret = $this->_db_events->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getEventList($cid, $data, $page = 1, $pageSize = 10) {
        $data['CreatorID'] = $cid;
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_events->where($data)->order('EventTime desc')->limit($offset, $pageSize)->select();
        return $list;
    }
}
