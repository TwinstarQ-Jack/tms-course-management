<?php
namespace User\Controller;
use Think\Controller;
class LogController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    //获取公告数目和展示
    public function getlogpages() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $ret = D('Public')->getEventPageCount($cid);
        if($ret === false) {
            return show(0, 'getEventPageCountNumFailed');
        } else{
            return show(1, 'getEventPageCountNumSuccessed', $ret);
        }
    }
    public function getloglists() {
        $cid = $_SESSION['TMSUser']['cardid'];
        $page = $_POST['page'];
        $data = array();
        $list = D('Public')->getEventList($cid, $data, $page);
        if($list === false){
            return show(0, 'getEventListFailed');
        } else{
            return show(1, 'getEventListSuccessed', $list);
        }
    }
}