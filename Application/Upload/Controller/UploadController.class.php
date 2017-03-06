<?php
namespace Upload\Controller;
use Think\Controller;
class UploadController extends Controller
{

    //后台资源管理上传入口
    public function resource() {
        D('Public')->NotUserRet();
        $errorcode = $_FILES['resource']['code'];
        $name = $_FILES['resource']['name'];
        $size = $_FILES['resource']['size'];
        $tmp = $_FILES['resource']['tmp_name'];
        $type = $_FILES['resource']['type'];
        if($errorcode > 0){
            $error = D('Resources')->uploaderror($errorcode);
            return show(0, $error);
        } else{
            if(D('Resources')->attr_ext_filter($type) === false) {
                return show(0, '上传的文件不合法');
            }
            $size = $size / 1000 / 1000;//以M为单位
            if($size > 7.8) {
                return show(0, '上传文件大小超过系统设置上限');
            }
            $dir = '/upload/temp/';
            D('Resources')->folder_exist($_SERVER['DOCUMENT_ROOT'].$dir);
            $moveto = $dir.getPHPTime_num().'.tmp';
            $ret_remove = rename($tmp, $_SERVER['DOCUMENT_ROOT'].$moveto);
            if(!$ret_remove) {
                return show(0, '上传文件至服务器临时文件夹失败');
            } else {
                return show(1, '上传成功', array('src'=>$moveto, 'name'=>$name, 'size'=>$size, 'type'=>$type));
            }
        }
    }
    //删除上传文件
    public function delfile() {
        D('Public')->NotUserRet();
        $file = $_POST['file'];
        $dir = $_SERVER['DOCUMENT_ROOT'].'/tmp/';
        D('Resources')->folder_exist($dir);
        $ret = rename($_SERVER['DOCUMENT_ROOT'].$file, $dir.getPHPTime_num().'.tmp');
        if(!$ret) {
            return show(0, '删除文件失败');
        } else {
            return show(1, '删除文件成功');
        }
    }
    //编辑器图片上传
    public function editorpic() {
        D('Public')->NotUserRet();
        $errorcode = $_FILES['editorpic']['code'];
        $name = $_FILES['editorpic']['name'];
        $size = $_FILES['editorpic']['size'];
        $tmp = $_FILES['editorpic']['tmp_name'];
        $type = $_FILES['editorpic']['type'];
        if($errorcode > 0){
            $error = D('Resources')->uploaderror($errorcode);
            return show(0, $error);
        } else{
            $type = D('Resources')->pic_ext_filter($type);
            if($type === false) {
                return show(0, '上传文件非图片类型');
            }
            $size = $size / 1000 / 1000;//以M为单位
            if($size > 2) {
                return show(0, '上传文件大小超过系统设置上限');
            }
            $dir = '/upload/editor/'.getPHPTime_YYYYMM().'/';
            D('Resources')->folder_exist($_SERVER['DOCUMENT_ROOT'].$dir);
            $moveto = $dir.$_SESSION['TMSUser']['cardid'].getPHPTime_dHis().$type;
            $ret_remove = rename($tmp, $_SERVER['DOCUMENT_ROOT'].$moveto);
            if(!$ret_remove) {
                return show(0, '上传文件至服务器临时文件夹失败');
            } else {
                return show(1, '上传成功', array('src'=>$moveto, 'name'=>$name, 'size'=>$size));
            }
        }
    }
}