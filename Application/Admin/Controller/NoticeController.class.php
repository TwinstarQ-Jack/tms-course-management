<?php
namespace Admin\Controller;
use Think\Controller;
class NoticeController extends Controller
{
    public function course() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function add() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function sendmsg() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    //处理空数据，返回错误值
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //发送短信息
    public function sendmsgto() {
        $sendto = $_POST['sendto'];
        $msg = $_POST['msg'];
        $msg .= ("[来自用户：". $_SESSION['TMSUser']['name']. "]");
        $url = $_POST['url'];
        $this->isnull($msg, '内容');
        $this->isnull($sendto, '发送对象');
        if(!trim($url)){
            $ret = D('PushMsg')->updatePushMessages($sendto, $msg);
        } else{
            $ret = D('PushMsg')->updatePushMessages($sendto, $msg, $url);
        }
        if(!$ret){
            return show(0, '发送失败');
        }
        return show(1, '发送成功，编号为'. $ret);
    }
    //修改、添加公告
    public function addcheck() {
        $msg = $_POST['msg'];
        $url = $_POST['url'];
        $ddl = $_POST['ddl'];
        $change = $_POST['change'];
        $this->isnull($msg, '内容');
        $this->isnull($ddl, '有效期');
        if($change == "0"){
            if(trim($url) == null){
                $ret = D('Anno')->createnew($msg, $ddl);
            } else{
                $ret = D('Anno')->createnew($msg, $ddl, $url);
            }
            if(!$ret){
                return show(0, '创建新公告错误');
            }
            return show(1, '创建新公告成功，编号为'. $ret);
        }
        if($change == "1"){
            $id = $_POST['id'];
            if(trim($url) == null){
                $ret = D('Anno')->changeAnno($id, $msg, $ddl);
            } else{
                $ret = D('Anno')->changeAnno($id, $msg, $ddl, $url);
            }
            if($ret === false){
                return show(0, '更改公告'. $id. '错误');
            }
            return show(1, '更改公告'. $id. '成功，修改的条目数为'. $ret);
        }
    }
    //课程公告异步分页
    public function getcoursepages() {
        $data['pages'] = intval(D('Anno')->getAnnoPageCount());
        if(!$data['pages']) {
            return show(0, 'getAnnoPagesNumFailed');
        } else{
            return show(1, 'getAnnoPagesNumSuccessed', $data);
        }
    }
    public function getcourselists() {
        $page = $_POST['page'];
        $data = array();
        $list = D('Anno')->getAnnoList($data, $page);
        if(!$list){
            return show(0, 'getAnnoListFailed');
        } else{
            foreach($list as $k => $v){
                $list[$k]['annomessages'] = htmlspecialchars_decode($v['annomessages']);
            }
            return show(1, 'getAnnoListSuccessed', $list);
        }
    }
    //获取单个公告
    public function getcoursedetails() {
        $id = $_POST['id'];
        $ret = D('Anno')->getAnnoSingle($id);
        if(!$ret){
            return show(0, 'getAnnoDetailsFailed');
        }
        $ret['annomessages'] = htmlspecialchars_decode($ret['annomessages']);
        return show(1, 'getAnnoDetailsSuccessed', $ret);
    }
    //删除公告
    public function delcourse() {
        $id = $_POST['id'];
        $ret = D('Anno')->delAnnoSingle($id);
        if($ret === false){
            return show(0, 'delAnnoSingleFailed');
        } else{
            return show(1, 'delAnnoSingleSuccessed');
        }
    }
}