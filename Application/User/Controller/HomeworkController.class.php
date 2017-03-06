<?php
namespace User\Controller;
use Think\Controller;
class HomeworkController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    public function hand() {
        D('Public')->IsUserSession();
        $this->display();
    }
    //panel颜色
    private function getColor($result) {
        switch ($result) {
            case '1': return 'panel-success';break;
            default: return 'panel-danger';
        }
    }
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //初始化个人中心首页数目
    public function getnotfinnum() {
        //处理小组
        $groupparas_theme = array(
            'ThType' => 2, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $groupparas_hw = array(
            'Signal' => 1, 'HwStatus' => 1, 'GroupID' => $_SESSION['TMSUser']['group']
        );
        //处理个人
        $person_theme = array(
            'ThType' => 1, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $person_hw = array(
            'Signal' => 0, 'HwStatus' => 1, 'HandinID' => $_SESSION['TMSUser']['cardid']
        );
        $numgroup = D('Theme')->getNumOfHw($groupparas_theme);
        $numgroupfin = D('Hw')->countHw($groupparas_hw);
        $numperson = D('Theme')->getNumOfHw($person_theme);
        $numpersonfin = D('Hw')->countHw($person_hw);

        if($numgroup === false || $numgroupfin === false || $numperson === false || $numpersonfin === false){
            return show(0, 'getnotfinnumfailed');
        }
        $ret['group'] = $numgroup - $numgroupfin;
        $ret['person'] = $numperson - $numpersonfin;
        return show(1, 'getnotfinnumsuccessed', $ret);
    }
    //小组作业分页处理
    public function getgrouphwpages() {
        $groupparas_theme = array(
            'ThType' => 2, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $numgroupfin = D('Theme')->getPageNumOfHw($groupparas_theme);
        if($numgroupfin === false) {
            return show(0, 'getgrouphwpagesfailed');
        }
        return show(1, 'getgrouphwpagessuccessed', $numgroupfin);
    }
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
            $paras = array(
                'Signal' => true, 'HwStatus' => true, 'GroupID' => $_SESSION['TMSUser']['group'],
                'ThemeID' => $v['themeid']
            );
            $list[$k]['color'] = $this->getColor(D('Hw')->countHw($paras));
            $list[$k]['thintro'] = htmlspecialchars_decode($v['thintro']);
            $list[$k]['references'] = htmlspecialchars_decode($v['references']);
            $list[$k]['creatorid'] = D('User')->getnamebycid($v['creatorid']);
        }
        return show(1, 'getgrouphwlistssuccessed', $list);
    }
    //个人作业分页处理
    public function getpersonhwpages() {
        $groupparas_theme = array(
            'ThType' => 1, 'Grade' => $_SESSION['TMSUser']['grade']
        );
        $numgroupfin = D('Theme')->getPageNumOfHw($groupparas_theme);
        if($numgroupfin === false) {
            return show(0, 'getpersonhwpagesfailed');
        }
        return show(1, 'getpersonhwpagessuccessed', $numgroupfin);
    }
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
            $paras = array(
                'Signal' => false, 'HwStatus' => true, 'GroupID' => $_SESSION['TMSUser']['group'],
                'ThemeID' => $v['themeid']
            );
            $list[$k]['color'] = $this->getColor(D('Hw')->countHw($paras));
            $list[$k]['thintro'] = htmlspecialchars_decode($v['thintro']);
            $list[$k]['references'] = htmlspecialchars_decode($v['references']);
            $list[$k]['creatorid'] = D('User')->getnamebycid($v['creatorid']);
        }
        return show(1, 'getpersonhwlistssuccessed', $list);
    }
    //获取单ThemeID的信息
    public function getthemeinfo() {
        $tid = $_POST['tid'];
        $info = D('Theme')->getInfoByTID($tid);
        if($info === false) {
            return show(0, 'getthemeinfofailed');
        }
        $type = $info['thtype'];
        //处理ThemeInfo
        $info['thintro'] = htmlspecialchars_decode($info['thintro']);
        $info['references'] = htmlspecialchars_decode($info['references']);
        $info['creatorid'] = D('User')->getnamebycid($info['creatorid']);
        $paras = array();
        $color = array();
        //个人
        if($type == 1) {
            $paras = array(
                'Signal' => false, 'HandinID' => $_SESSION['TMSUser']['cardid'],
                'ThemeID' => $info['themeid']
            );
            $color = array(
                'Signal' => false, 'HwStatus' => true, 'GroupID' => $_SESSION['TMSUser']['group'],
                'ThemeID' => $info['themeid']
            );
        }
        //小组
        if($type == 2) {
            $paras = array(
                'Signal' => 1, 'GroupID' => $_SESSION['TMSUser']['group'], 'ThemeID' => $info['themeid']
            );
            $color = array(
                'Signal' => 1, 'HwStatus' => 1, 'GroupID' => $_SESSION['TMSUser']['group'],
                'ThemeID' => $info['themeid']
            );
        }
        $handin = D('Hw')->getHandinList($paras);
        $info['color'] = $this->getColor(D('Hw')->countHw($color));
        if($handin === false) {
            return show(0, 'getHandInDataFailed');
        }
        foreach($handin as $k => $v) {
            $handin[$k]['handinid'] = D('User')->getnamebycid($v['handinid']);
        }
        $ret = array('info'=>$info, 'handin'=>$handin);
        return show(1, 'getthemeinfosuccessed', $ret);
    }
    //保存上传作业
    public function savehw() {
        D('Public')->NotUserRet();
        $upload_tmp = $_POST['file'];
        $title = $_POST['title'];
        $type = $_POST['type'];
        $tid = $_POST['tid'];
        $gid = $_SESSION['TMSUser']['group'];
        $leader = D('Group')->getLeaderByGID($gid);
        $cid = $_SESSION['TMSUser']['cardid'];
        $this->isnull($upload_tmp, '已上传记录');
        $this->isnull($title, '文件名');
        $this->isnull($type, '已上传文件类型');
        $this->isnull($tid, '所属主题');
        //处理插入内容
        $dir = '/homework/'.$tid.'/'.$gid.'-'.$leader.'/';
        D('Resources')->folder_exist($_SERVER['DOCUMENT_ROOT'].$dir);
        $type = D('Resources')->attr_ext_filter($type);
        if($type === false) {
            return show(0, '上传了非法文件，服务器保存失败');
        }
        //判断类型
        $thtype = D('Theme')->getthemetype($tid);
        if($thtype === false) {
            return show(0, '获取主题类型失败');
        }
        if($thtype == 1) {
            $thtype = 0;
        } else if($thtype == 2) {
            $thtype = 1;
        }
        $filename = $dir.$cid.'-'.getPHPTime_num().'-'.$title.'.'.$type;
        $ret_remove = rename($_SERVER['DOCUMENT_ROOT'].$upload_tmp
            ,iconv('utf-8', 'gb2312', $_SERVER['DOCUMENT_ROOT'].$filename));
        if(!$ret_remove) {
            return show(0, '上传到资源目录失败');
        }
        //查询上一条记录并更改其status
        $query = array('ThemeID' => $tid, 'Signal' => $thtype);
        if($thtype) {
            $query['GroupID'] = $gid;
        } else {
            $query['HandinID'] = $cid;
        }
        $gethwid = D('Hw')->findHwID($query);
        if($gethwid){
            D('Hw')->disableHwID($gethwid);
        }
        //插入数据库内容
        $data = array(
            'ThemeID' => $tid, 'HandinID' => $cid, 'GroupID' => $gid,
            'FileName' => $filename, 'Signal'=> $thtype, 'HandinTime' => getPHPTime()
        );
        $res = D('Hw')->uploadhw($data);
        if($res === false) {
            rename($_SERVER['DOCUMENT_ROOT'].$filename, $upload_tmp);
            return show(0, '保存到数据库目录失败');
        }
        if($res === 0) {
            return show(0, '保存记录失败');
        } else {
            return show(1, '添加新资源成功，返回值为：'.$res);
        }
    }

}