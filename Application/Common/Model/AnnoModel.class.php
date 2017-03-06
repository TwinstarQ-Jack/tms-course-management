<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class AnnoModel extends Model {
	private $_db_anno = '';

	public function __construct() {
		$this->_db_anno = M('announces');
	}
    //创建新公告
	public function createnew($msg, $ddl, $url = null) {
        $data['AnnoMessages'] = htmlspecialchars($msg);
        $data['AnnoName'] = $_SESSION['TMSUser']['name'];
        $data['AnnoDeadline'] = $ddl;
        if($url) {
            $data['AnnoToUrl'] = $url;
        }
        $ret = $this->_db_anno->add($data);
        D('Public')->updateEvents($_SESSION['TMSUser']['cardid'], "创建了新的课程公告");
        return $ret;
    }
    //修改公告
    public function changeAnno($id, $msg, $ddl, $url = null) {
        $data['AnnoMessages'] = htmlspecialchars($msg);
        $data['AnnoName'] = $_SESSION['TMSUser']['name'];
        $data['AnnoDeadline'] = $ddl;
        if($url) {
            $data['AnnoToUrl'] = $url;
        }
        $ret = $this->_db_anno->where('AnnoID = "'. $id. '"')->save($data);
        D('Public')->updateEvents($_SESSION['TMSUser']['cardid'], "修改了id为". $id. "的课程公告");
        return $ret;
    }
    //删除某一条公告（状态为0）
    public function delAnnoSingle($id) {
	    $ret = $this->_db_anno->where('AnnoID = "'. $id. '"')->setField('AnnoStatus','0');
	    return $ret;
    }
    //获取条数
    public function getAnnoPageCount($data = array(), $pageSize = 15) {
	    $data['AnnoStatus'] = array('neq', 0);
	    $ret = $this->_db_anno->where($data)->count();
	    if($ret % $pageSize == 0){
	        $ret /= $pageSize;
        } else{
	        $ret = $ret / $pageSize + 1;
        }
	    return $ret;
    }
    public function getAnnoList($data, $page = 1, $pageSize = 15) {
        $data['AnnoStatus'] = array('eq', 1);
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_anno->where($data)->order('AnnoID desc')->limit($offset, $pageSize)->select();
        return $list;
    }
    //获取单条公告内容
    public function getAnnoSingle($id) {
	    $ret = $this->_db_anno->where(array('AnnoID' => $id))->find();
	    return $ret;
    }
}
