<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class HwModel extends Model {
	private $_db_hw = '';

	public function __construct() {
		$this->_db_hw = M('homework');
	}
    //根据查询条件返回条数
    public function countHw($paras) {
	    $ret = $this->_db_hw->where($paras)->count();
	    return $ret;
    }
    //作业分页
    public function getGroupHwPageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_hw->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getGroupHwList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_hw->where($data)->order('HwID desc')->limit($offset, $pageSize)
            ->select();
        return $list;
    }
    //获取某项themeid的提交情况
    public function getHandinList($paras) {
	    $list = $this->_db_hw->where($paras)->order('HandinTime desc')->select();
	    return $list;
    }
    //提交作业
    public function uploadhw($data) {
	    $ret = $this->_db_hw->add($data);
	    return $ret;
    }
    //更改指定HwID，将记录失效
    public function disableHwID($hwid) {
	    $ret = $this->_db_hw->where(array('HwID'=>$hwid))->setField('HwStatus', 0);
	    return $ret;
    }
    //查询最近符合查询记录的HwID
    public function findHwID($paras) {
	    $ret = $this->_db_hw->where($paras)->order('HandinTime desc')->getField('HwID');
	    return $ret;
    }
    //查询指定tid的有效提交数目
    public function getHandInNum($tid) {
	    $paras = array(
	        'ThemeID' => $tid, 'HwStatus' => 1
        );
	    $ret = $this->_db_hw->where($paras)->count();
	    return $ret;
    }
    //查询指定条件的hw的具体内容
    public function getDetails($paras, $field = array()) {
        $ret = $this->_db_hw->where($paras)->field($field)->find();
        return $ret;
    }
}
