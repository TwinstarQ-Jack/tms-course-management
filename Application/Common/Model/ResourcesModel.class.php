<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class ResourcesModel extends Model {
	private $_db_res = '';

	public function __construct() {
		$this->_db_res = M('resources');
	}
    //上传错误提示转换为中文
    public function uploaderror($errorcode) {
        switch($errorcode){
            case 1: return 'UPLOAD_ERR_INI_SIZE,上传的文件超过了php限制';break;
            case 2: return 'UPLOAD_ERR_FORM_SIZE,上传文件的大小超过了HTML表单选项指定的值';break;
            case 3: return 'UPLOAD_ERR_PARTIAL,文件只有部分被上传';break;
            case 4: return 'UPLOAD_ERR_NO_FILE,没有文件被上传';break;
            case 5: return '上传文件大小为0';break;
            case 6: return 'UPLOAD_ERR_NO_TMP_DIR,找不到临时文件夹';break;
            case 7: return 'UPLOAD_ERR_CANT_WRITE,文件写入失败';break;
            default: return 'UNDEFINED,未定义错误(upload)';
        }
    }
    //图片类型过滤
    public function pic_ext_filter($type) {
        switch($type) {
            case 'image/jpeg': return 'jpg';break;
            case 'image/gif': return 'gif';break;
            case 'image/png': return 'png';break;
            case 'image/bmp': return 'bmp';break;
            default: return false;
        }
    }
    //上传附件过滤
    public function attr_ext_filter($type) {
        switch($type) {
            case 'application/zip': return 'zip';break;
            case 'application/vnd.ms-excel': return 'xls';break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': return 'xlsx';break;
            case 'application/msword': return 'doc';break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': return 'docx';break;
            case 'application/vnd.ms-powerpoint': return 'ppt';break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': return 'pptx';break;
            case 'application/pdf': return 'pdf';break;
            default: return false;
        }
    }
    //检查文件夹是否存在，不存在则创建
    public function folder_exist($path) {
        if(!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
    }
    //获取指定cid的最新fileid
    public function getFileID($cid) {
	    $ret = $this->_db_res->where(array('ChapterID'=>$cid))
            ->order('UploaderTime desc')->getField('FileID');
	    return $ret;
    }
    //获取指定fileID的文件目录
    public function getFileAddress($fid) {
        $ret = $this->_db_res->where(array('FileID'=>$fid))->getField('FileName');
        return $ret;
    }
    //检查指定fileID是否存在
    public function countFileID($fid) {
	    $ret = $this->_db_res->where(array('FileID'=>$fid))->count();
	    return $ret;
    }
    //增加新资源目录
    public function addResources($data) {
	    $ret = $this->_db_res->add($data);
	    return $ret;
    }
    //删除指定id的资源
    public function removeResource($data) {
	    $ret = $this->_db_res->where($data)->delete();
	    return $ret;
    }
    //分页
    public function getResPageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_res->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getResList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_res->where($data)->order('UploaderTime desc')->limit($offset, $pageSize)
            ->field(array('ChapterID'), true)->select();
        return $list;
    }
    public function getAllResList($data) {
        $list = $this->_db_res->where($data)->order('UploaderTime desc')
            ->field(array('ChapterID'), true)->select();
        return $list;
    }
}
