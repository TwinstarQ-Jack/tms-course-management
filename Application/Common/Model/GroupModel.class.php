<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class GroupModel extends Model {
	private $_db_group = '';
	private $_db_member = '';

	public function __construct() {
		$this->_db_group = M('group');
		$this->_db_member = M('groupmember');
	}
    //返回小组总数
    public function getGroupCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_group->where($data)->count();
        return $ret;
    }
    //返回页码总数
    public function getGroupPageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_group->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    //返回指定页码的所有小组
    public function getGroupList($data = array(), $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_group->where($data)->order('ID')->limit($offset, $pageSize)->select();
        return $list;
    }
    //返回小组全体成员列表
    public function getMemberByGID($gid, $paras = array()) {
        $data['GID'] = $gid;
        $list = $this->_db_member->where($data)->field($paras)->select();
        return $list;
    }
    //返回指定条件的成员所属小组
    public function getMemberGIDByOthers($data) {
	    $list = $this->_db_member->where($data)->getField('GID');
	    return $list;
    }
    //根据cid查询是否加入小组
    public function checkJoinStatusByCID($cid) {
	    $data = array('CardID'=>$cid, 'GID'=>array('GT', 0));
	    $ret = $this->_db_member->where($data)->count();
        return $ret;
    }
    //根据cid查询加入小组的gid
    public function checkJoinGIDByCID($cid) {
	    $data = array('CardID'=>$cid);
	    $ret = $this->_db_member->where($data)->getField('GID');
        return $ret;
    }
    //根据gid查询组长
    public function getLeaderByGID($gid) {
	    $data = array('ID'=>$gid);
	    $ret = $this->_db_group->where($data)->getField('Leader');
	    return $ret;
    }
    //返回当前年级的注册人数
    public function regtermNum() {
	    $grade = D('Setting')->getValue('nowterm');
	    $ret = $this->_db_member->where(array('Grade' => $grade))->count();
	    return $ret;
    }
    //返回当前年级的注册列表
    public function regtermList() {
        $grade = D('Setting')->getValue('nowterm');
        $ret = $this->_db_group->where(array('Grade' => $grade))->field('ID,Leader')->select();
        return $ret;
    }
    //返回指定小组的人数
    public function regmemberNum($gid) {
        $grade = D('Setting')->getValue('nowterm');
        $ret = $this->_db_member->where(array('Grade' => $grade, 'GID' => $gid))->count();
        if($ret > 0){
            $this->_db_group->where(array('ID' => $gid))->setField('Num', $ret);
        }
        return $ret;
    }
    //添加新组，返回状态号或MID
    public function addnewGroup() {
        $grade = D('Setting')->getValue('nowterm');
        $latest = $this->_db_group->getField('ID');
        if($latest === null){
            $latest = $grade * 1000 +1;
        } else{
            $latest++;
        }
        $name = $_SESSION['TMSUser']['name'];
        $data = array('ID' => $latest, 'Grade' => $grade, 'Num' => 1, 'Leader' => $name);
        $ret1 = $this->_db_group->add($data);
        if($ret1 === false){
            return -1;
        } else{
            $cid = $_SESSION['TMSUser']['cardid'];
            $data = array('GID' => $latest, 'CardID' => $cid, 'Name' => $name);
            $ret2 = $this->_db_member->add($data);
            if($ret2 === false){
                return -2;
            }
            return $latest;
        }
    }
    //添加新成员
    public function addnewMember($gid, $cid, $name) {
	    $data = array('GID' => $gid, 'CardID' => $cid, 'Name' => $name);
	    $ret = $this->_db_member->add($data);
	    if($ret === false){
	        return -1;
        } else{
	        $this->_db_group->where(array('ID' => $gid))->setInc('Num',1);
	        return $ret;
        }
    }
}
