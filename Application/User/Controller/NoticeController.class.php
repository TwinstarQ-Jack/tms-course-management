<?php
namespace User\Controller;
use Think\Controller;
class NoticeController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    public function deal() {
        D('Public')->IsUserSession();
        $this->display();
    }
    //获取公告数目和展示
    public function getnoticepages() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $mode = $_POST['mode'];
        if($mode == "current"){
            $data['ReadSignal'] = false;
        }
        if($mode == "history"){
            $data['ReadSignal'] = true;
        }
        $ret = D('PushMsg')->getPushPageCount($cid, $data);
        if($ret === false) {
            return show(0, 'getPushPagesNumFailed' . $mode);
        } else{
            return show(1, 'getPushPagesNumSuccessed' . $mode, $ret);
        }
    }
    public function getnoticelists() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $page = $_POST['page'];
        $mode = $_POST['mode'];
        $data = array();
        if($mode == "current"){
            $data['ReadSignal'] = false;
        }
        if($mode == "history"){
            $data['ReadSignal'] = true;
        }
        $list = D('PushMsg')->getPushList($cid, $data, $page);
        if($list === false){
            return show(0, 'getPushListFailed' . $mode);
        } else{
            foreach($list as $k => $v){
                $list[$k]['pushmessages'] = htmlspecialchars_decode($v['pushmessages']);
            }
            return show(1, 'getPushListSuccessed' . $mode, $list);
        }
    }
    public function setnoticestatus() {
        $id = $_POST['id'];
        $real_cid = $_SESSION['TMSUser']['cardid'];
        $cid = D('PushMsg')->getPushToID($id);
        $status = $_POST['status'];
        if($cid != $real_cid){
            return show(0, '非法操作：不能修改不属于自己的消息');
        }
        $ret = D('PushMsg')->setPushReadStatus($id, $status);
        if($ret === false) {
            return show(0, '更新状态出错');
        }
        return show(1, '更新成功，即将返回消息列表');
    }
}