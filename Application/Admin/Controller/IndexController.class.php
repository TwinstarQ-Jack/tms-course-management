<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function setgrade() {
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
    //修改目前上课年级
    public function setnowgrade() {
        D('Public')->NotAdminRet();
        $grade = $_POST['grade'];
        $ret = D('Setting')->setValue('nowterm', $grade);
        if($ret !== false){
            return show(1, 'setNowGradeSuccessed', $ret);
        }
        return show(0, 'setNowGradeFailed');
    }

}