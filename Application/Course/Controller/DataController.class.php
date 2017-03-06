<?php
namespace Course\Controller;
use Think\Controller;
class DataController extends Controller
{
    //获取章列表（导航、介绍、标题）
    public function getchapterlist() {
        $grade = $_SESSION['TMSUser']['grade'];
        $list = D('Theme')->getChapterAllList(array('Grade'=>$grade), 'ChapterID,ShowName,ChIntro');
        if($list === false) {
            return show(0, 'getchapterlistfailed');
        }
        return show(1, 'getchapterlistsuccessed', $list);
    }
    //获取资源列表
    public function getresourcelist() {
        $cid = $_POST['cid'];
        $list = D('Resources')->getAllResList(array('ChapterID'=>$cid));
        if($list === false) {
            return show(0, 'getresourcelistfailed');
        }
        foreach($list as $k => $v) {
            $list[$k]['uploaderid'] = D('User')->getnamebycid($v['uploaderid']);
        }
        return show(1, 'getresourcelistsuccessed', $list);
    }
    //获取theme中类型为介绍（intro）的列表
    public function getthemeintrolist() {
        $cid = $_POST['cid'];
        $list = D('Theme')->getIntroList($cid);
        if($list === false) {
            return show(0, 'getthemeintrolistfailed');
        }
        foreach($list as $k => $v) {
            $list[$k]['creatorid'] = D('User')->getnamebycid($v['creatorid']);
        }
        return show(1, 'getthemeintrolistsuccessed', $list);
    }
    //获取指定tid的内容
    public function getthemedetail() {
        $tid = $_POST['tid'];
        $list = D('Theme')->getInfoByTID($tid);
        if($list === false) {
            return show(0, 'getthemedetailfailed');
        }
        $list['creatorid'] = D('User')->getnamebycid($list['creatorid']);
        $list['thintro'] = htmlspecialchars_decode($list['thintro']);
        $list['references'] = htmlspecialchars_decode($list['references']);
        return show(1, 'getthemedetailsuccessed', $list);
    }
}