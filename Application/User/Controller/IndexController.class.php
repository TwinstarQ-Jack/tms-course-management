<?php
namespace User\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    //获取公告数目和展示
    public function getcoursepages() {
        $mode = $_POST['mode'];
        if($mode == "current"){
            $data['AnnoDeadline'] = array('EGT', getPHPTime());
        }
        if($mode == "history"){
            $data['AnnoDeadline'] = array('LT', getPHPTime());
        }
        $ret = D('Anno')->getAnnoPageCount($data);
        if($ret === false) {
            return show(0, 'getAnnoPagesNumFailed' . $mode);
        } else{
            return show(1, 'getAnnoPagesNumSuccessed' . $mode, $ret);
        }
    }
    public function getcourselists() {
        $page = $_POST['page'];
        $mode = $_POST['mode'];
        $data = array();
        if($mode == "current"){
            $data['AnnoDeadline'] = array('EGT', getPHPTime());
        }
        if($mode == "history"){
            $data['AnnoDeadline'] = array('LT', getPHPTime());
        }
        $list = D('Anno')->getAnnoList($data, $page);
        if($list === false){
            return show(0, 'getAnnoListFailed' . $mode);
        } else{
            foreach($list as $k => $v){
                $list[$k]['annomessages'] = htmlspecialchars_decode($v['annomessages']);
            }
            return show(1, 'getAnnoListSuccessed' . $mode, $list);
        }
    }
}