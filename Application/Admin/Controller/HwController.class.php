<?php
namespace Admin\Controller;
use Think\Controller;
class HwController extends Controller
{
    public function manager() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function group() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function grouplist() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function person() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function personlist() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    //小组作业列表+已交查询
    public function getgrouphwlists() {
        $page = $_POST['page'];
        $groupparas_theme = array(
            'ThType' => 2, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $list = D('Theme')->getListOfHw($groupparas_theme, $page);
        if($list === false) {
            return show(0, 'getgrouphwlistsfailed');
        }
        foreach($list as $k => $v) {
            $list[$k]['handin'] = D('Hw')->getHandInNum($v['themeid']);
        }
        return show(1, 'getgrouphwlistssuccessed', $list);
    }
    //小组作业具体每个小组的上交情况
    public function getgrouphwtids() {
        $tid = $_POST['tid'];
        //查询小组列表
        $ret_list = D('Group')->regtermList();
        $ret_noupload = '';
        if($ret_list === false) {
            return show(0, 'getgrouphwtidsfailed');
        }
        foreach($ret_list as $k => $v) {
            $query = array(
                'ThemeID' => $tid, 'GroupID' => $v['id'], 'HwStatus' => 1, 'Signal' => 1
            );
            $ret_list[$k]['count'] = D('Hw')->countHw($query);
            if($ret_list[$k]['count'] > 0) {
                $ret_list[$k]['details'] = D('Hw')->getDetails($query, array('HandinID,HandinTime,FileName'));
                $ret_list[$k]['details']['handinid'] = D('User')->getnamebycid($ret_list[$k]['details']['handinid']);
            } else {
                $ret_noupload .= ($v['id'].'('.$v['leader'].')、');
            }
        }
        return show(1, $ret_noupload, $ret_list);
    }
    //个人作业列表+已交查询
    public function getpersonhwlists() {
        $page = $_POST['page'];
        $groupparas_theme = array(
            'ThType' => 1, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $list = D('Theme')->getListOfHw($groupparas_theme, $page);
        if($list === false) {
            return show(0, 'getgrouphwlistsfailed');
        }
        foreach($list as $k => $v) {
            $list[$k]['handin'] = D('Hw')->getHandInNum($v['themeid']);
        }
        return show(1, 'getgrouphwlistssuccessed', $list);
    }

    //个人作业具体每个人的上交情况
    public function getpersonhwtids() {
        $tid = $_POST['tid'];
        //查询小组列表
        $ret_list = D('User')->regUserList(array('RootRank'=>array('neq','1')),'CardID,Name');
        $ret_noupload = '';
        if($ret_list === false) {
            return show(0, 'getpersonhwtidsfailed');
        }
        foreach($ret_list as $k => $v) {
            $query = array(
                'ThemeID' => $tid, 'HandinID' => $v['cardid'], 'HwStatus' => 1, 'Signal' => 0
            );
            $ret_list[$k]['count'] = D('Hw')->countHw($query);
            if($ret_list[$k]['count'] > 0) {
                $ret_list[$k]['details'] = D('Hw')->getDetails($query, array('HandinTime,FileName'));
            } else {
                $ret_noupload .= ($v['cardid'].'('.$v['name'].')、');
            }
        }
        return show(1, $ret_noupload, $ret_list);
    }
}