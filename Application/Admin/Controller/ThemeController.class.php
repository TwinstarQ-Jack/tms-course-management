<?php
namespace Admin\Controller;
use Think\Controller;
class ThemeController extends Controller
{
    public function newchapter() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function newtheme() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function manager() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    //获取目前上课年级
    public function getnowgrade() {
        D('Public')->NotAdminRet();
        $ret = D('Setting')->getValue('nowterm');
        if($ret !== false){
            return show(1, 'getNowGradeSuccessed', $ret);
        }
        return show(0, 'getNowGradeFailed');
    }
    //处理空数据，返回错误值
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    public function getchapter() {
        $grade = D('Setting')->getValue('nowterm');
        $list = D('Theme')->getChapterByGrade($grade) ;
        if($list === false) {
            return show(0, 'getchapterlistfailed');
        }
        return show(1, 'getchapterlistsuccessed', $list);
    }
    //获取chapter内容
    public function getcidcontent() {
        $cid = $_POST['cid'];
        $con = D('Theme')->getChapterByCID($cid);
        if($con === false){
            return show(0, 'getchaptercontentfailed');
        }
        return show(1, 'getchaptercontentsuccessed', $con);
    }
    //获取theme内容
    public function gettidcontent() {
        $tid = $_POST['tid'];
        $con = D('Theme')->getThemeByTID($tid);
        if($con === false){
            return show(0, 'getthemecontentfailed');
        }
        $con['thintro'] = htmlspecialchars_decode($con['thintro']);
        $con['references'] = htmlspecialchars_decode($con['references']);
        return show(1, 'getthemecontentsuccessed', $con);
    }
    //添加chapter
    public function addchapter() {
        $chname = $_POST['chname'];
        $engname = $_POST['engname'];
        $intro = $_POST['intro'];
        $grade = D('Setting')->getValue('nowterm');
        $this->isnull($chname, '主题标题名');
            $data = array('Grade'=>$grade, 'ShowName'=>$chname, 'EngName'=>$engname, 'ChIntro'=>$intro);
        $ret = D('Theme')->addNewChapter($data);
        if($ret === false){
            return show(0, '添加新主题失败');
        }
        return show(1, '添加新主题成功');
    }
    //添加chapter
    public function savechapter() {
        $cid = $_POST['cid'];
        $chname = $_POST['chname'];
        $engname = $_POST['engname'];
        $intro = $_POST['intro'];
        $grade = D('Setting')->getValue('nowterm');
        $this->isnull($cid, '主题索引');
        $this->isnull($chname, '主题标题名');
            $data = array('Grade'=>$grade, 'ShowName'=>$chname, 'EngName'=>$engname, 'ChIntro'=>$intro);
        $ret = D('Theme')->saveChapter($cid, $data);
        if($ret === false){
            return show(0, '保存主题失败');
        }
        return show(1, '保存主题成功，影响条目数为'.$ret);
    }
    //添加theme
    public function addtheme() {
        $chname = $_POST['chname'];
        $thtype = $_POST['thtype'];
        $engname = $_POST['engname'];
        $cid = $_POST['cid'];
        $intro = htmlspecialchars($_POST['intro']);
        $refers = htmlspecialchars($_POST['refers']);
        $ddl = $_POST['ddl'];
        $grade = D('Setting')->getValue('nowterm');
        $this->isnull($chname, '主题标题名');
        $this->isnull($thtype, '主题类型');
        $this->isnull($cid, '所属章');
        if($thtype > 0){
            $this->isnull($ddl, '截止时间');
        }
        $data = array(
            'Grade'=>$grade, 'ChapterID'=>$cid, 'ShowName'=>$chname, 'EngName'=>$engname
            , 'ThIntro'=>$intro, 'ThType'=>$thtype, 'References'=>$refers
            , 'Deadline'=>$ddl
        );
        $ret = D('Theme')->addNewTheme($data);
        if($ret === false){
            return show(0, '添加新主题失败');
        }
        return show(1, '添加新主题成功');
    }
    //保存theme
    public function savetheme() {
        $tid = $_POST['tid'];
        $chname = $_POST['chname'];
        $thtype = $_POST['thtype'];
        $engname = $_POST['engname'];
        $cid = $_POST['cid'];
        $intro = htmlspecialchars($_POST['intro']);
        $refers = htmlspecialchars($_POST['refers']);
        $ddl = $_POST['ddl'];
        $grade = D('Setting')->getValue('nowterm');
        $this->isnull($tid, '修改的主题索引');
        $this->isnull($chname, '主题标题名');
        $this->isnull($thtype, '主题类型');
        $this->isnull($cid, '所属章');
        if($thtype > 0){
            $this->isnull($ddl, '截止时间');
        }
        $data = array(
            'Grade'=>$grade, 'ChapterID'=>$cid, 'ShowName'=>$chname, 'EngName'=>$engname
            , 'ThIntro'=>$intro, 'ThType'=>$thtype, 'References'=>$refers
            , 'Deadline'=>$ddl
        );
        $ret = D('Theme')->saveTheme($tid, $data);
        if($ret === false){
            return show(0, '保存新主题失败');
        }
        return show(1, '保存新主题成功，影响的条目数为：'.$ret);
    }
    //chapter管理+分页
    public function getchapterpages() {
        $paras['Grade'] = $_POST['grade'];
        $data['pages'] = intval(D('Theme')->getChapterPageCount($paras));
        if($data['pages'] === false) {
            return show(0, 'getThemePagesNumFailed');
        } else{
            return show(1, 'getThemePagesNumSuccessed', $data);
        }
    }
    public function getchapterlists() {
        $page = $_POST['page'];
        $grade = $_POST['grade'];
        $data = array('Grade'=>$grade);
        $list = D('Theme')->getChapterList($data, $page);
        if($list === false){
            return show(0, 'getThemeListFailed');
        } else{
            return show(1, 'getThemeListSuccessed', $list);
        }
    }
    //theme管理+分页
    public function getthemepages() {
        $paras['ChapterID'] = $_POST['cid'];
        $data['pages'] = intval(D('Theme')->getThemePageCount($paras));
        if($data['pages'] === false) {
            return show(0, 'getThemePagesNumFailed');
        } else{
            return show(1, 'getThemePagesNumSuccessed', $data);
        }
    }
    public function getthemelists() {
        $page = $_POST['page'];
        $cid = $_POST['cid'];
        $data = array('ChapterID'=>$cid);
        $list = D('Theme')->getThemeList($data, $page);
        if($list === false){
            return show(0, 'getThemeListFailed');
        } else{
            foreach($list as $k => $v) {
                $list[$k]['thintro'] = htmlspecialchars_decode($v['thintro']);
                switch($v['thtype']) {
                    case 0: $list[$k]['thtype'] = '介绍';break;
                    case 1: $list[$k]['thtype'] = '个人作业';break;
                    case 2: $list[$k]['thtype'] = '小组作业';break;
                }
            }
            return show(1, 'getThemeListSuccessed', $list);
        }
    }
    //删除chapter
    public function delchapter() {
        $cid = $_POST['cid'];
        $ret = D('Theme')->delChapterByCID($cid);
        if($ret === false) {
            return show(0, '连接服务器删除记录失败');
        }
        if($ret < 1) {
            return show(0, '没有删除本章记录');
        }
        return show(1, '删除章id'.$cid.'成功');
    }
    //删除theme
    public function deltheme() {
        $tid = $_POST['tid'];
        $ret = D('Theme')->delThemeByTID($tid);
        if($ret === false) {
            return show(0, '连接服务器删除记录失败');
        }
        if($ret < 1) {
            return show(0, '没有删除本章记录');
        }
        return show(1, '删除章id'.$cid.'成功');
    }
}