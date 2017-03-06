<?php
namespace Admin\Controller;
use Think\Controller;
class ResourceController extends Controller
{
    public function newfile() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    public function manager() {
        D('Public')->IsAdminSession();
        $this->display();
    }
    private function isnull($val, $message)
    {
        if (trim($val) == null) {
            return show(0, $message . '不能为空');
        }
    }
    //保存新资源记录
    public function addres() {
        D('Public')->NotAdminRet();
        $upload_tmp = $_POST['file'];
        $title = $_POST['title'];
        $type = $_POST['type'];
        $cid = $_POST['cid'];
        $intro = $_POST['intro'];
        $this->isnull($upload_tmp, '已上传记录');
        $this->isnull($title, '文件名');
        $this->isnull($type, '已上传文件类型');
        $this->isnull($cid, '所属章');
        if(trim($intro) == null) {
            $intro = null;
        }
        $uploader = $_SESSION['TMSUser']['cardid'];
        //处理索引值（文件id）
        $latest = D('Resources')->getFileID($cid);
        if($latest === false) {
            return show(0, '获取以前的资源目录失败');
        }
        if($latest == null) {
            $latest = $cid * 100 + 1;
        } else {
            $latest++;
        }
        //处理插入内容
        $dir = '/resources/'.$cid.'/';
        D('Resources')->folder_exist($_SERVER['DOCUMENT_ROOT'].$dir);
        $type = D('Resources')->attr_ext_filter($type);
        if($type === false) {
            return show(0, '上传了非法文件，服务器保存失败');
        }
        $filename = $dir.$title.'.'.$type;
        $ret_remove = rename($_SERVER['DOCUMENT_ROOT'].$upload_tmp
            ,iconv('utf-8', 'gb2312', $_SERVER['DOCUMENT_ROOT'].$filename));
        if(!$ret_remove) {
            return show(0, '上传到资源目录失败');
        }
        //插入数据库内容
        $data = array(
            'FileID' => $latest, 'ChapterID' => $cid, 'ShowName' => $title,
            'Remarks' => $intro, 'UploaderID' => $uploader, 'FileName' => $filename
        );
        $res = D('Resources')->addResources($data);
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
    //获取资源管理列表
    public function getrespages() {
        $paras['ChapterID'] = $_POST['cid'];
        $data['pages'] = intval(D('Resources')->getResPageCount($paras));
        if($data['pages'] === false) {
            return show(0, 'getResPagesNumFailed');
        } else{
            return show(1, 'getResPagesNumSuccessed', $data);
        }
    }
    public function getreslists() {
        $page = $_POST['page'];
        $cid = $_POST['cid'];
        $data = array('ChapterID'=>$cid);
        $list = D('Resources')->getResList($data, $page);
        if($list === false){
            return show(0, 'getResListFailed');
        } else{
            return show(1, 'getResListSuccessed', $list);
        }
    }
    //删除
    public function delresource() {
        $id = $_POST['id'];
        $existsignal = D('Resources')->countFileID($id);
        if($existsignal === false) {
            return show(0, '连接数据库查询失败');
        }
        if($existsignal < 1) {
            return show(0, '该id不存在');
        }
        $delsignal = D('Resources')->removeResource(array('FileID'=>$id));
        if($delsignal === false) {
            return show(0, '连接数据库删除失败');
        }
        if($delsignal < 1) {
            return show(0, '没有被删除的记录');
        }
        $filename = D('Resources')->getFileAddress($id);
        D('Resources')->folder_exist($_SERVER['DOCUMENT_ROOT'].'/tmp/');
        rename($_SERVER['DOCUMENT_ROOT'].$filename, $_SERVER['DOCUMENT_ROOT'].'/tmp/'.getPHPTime_num().'.tmp');
        return show(1, '删除记录'.$id.'成功');
    }
}