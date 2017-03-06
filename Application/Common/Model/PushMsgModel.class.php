<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class PushMsgModel extends Model {
	private $_db_push = '';

	public function __construct() {
		$this->_db_push = M('pushmessages');
	}
    //返回指定cid的未读消息数目
    public function unreadNumByCid($cid) {
	    $ret = $this->_db_push->where('PushToID="'.$cid.'" AND ReadSignal=false')->count();
	    return $ret;
    }
    //更新推送内容
    public function updatePushMessages($pushto, $contents, $url = null) {
	    $contents = htmlspecialchars($contents);
	    $data = array('PushToID' => $pushto, 'PushMessages' => $contents, 'PushToUrl' => $url);
        if(!$url){
            $data = array('PushToID' => $pushto, 'PushMessages' => $contents);
        }
        $ret =  $this->_db_push->add($data);
        return $ret;
    }
    //获取条数
    public function getPushPageCount($cid, $data = array(), $pageSize = 15) {
        $data['PushToID'] = $cid;
        $ret = $this->_db_push->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getPushList($cid, $data, $page = 1, $pageSize = 15) {
        $data['PushToID'] = $cid;
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_push->where($data)->order('PushTime desc')->limit($offset, $pageSize)->select();
        return $list;
    }
    //更新指定id的已读状态
    public function setPushReadStatus($id, $status) {
	    $data['PushID'] = $id;
	    $ret = $this->_db_push->where($data)->setField('ReadSignal', $status);
	    return $ret;
    }
    //获取指定id的推送用户
    public function getPushToID($id) {
	    $data['PushID'] = $id;
        $ret = $this->_db_push->where($data)->getField('PushToID');
        return $ret;
    }
}
