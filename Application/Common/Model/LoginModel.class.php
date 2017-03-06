<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class LoginModel extends Model {
	private $_db_users = '';
	private $_db_findpsw = '';

	public function __construct() {
		$this->_db_users = M('users');
		$this->_db_findpsw = M('findpsw');
	}

	public function createSalt() {
        $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $salt = substr(str_shuffle($str),10,8);
        return $salt;
    }
   
    public function getAccountByCardID($cid) {
        $res = $this->_db_users->where('CardID="'.$cid.'"')->find();
        return $res;
    }

    public function updateLoginTimeByCardId($cid){
        if(!$cid) {
            E("CID不合法");
        }
        return $this->_db_users->where('CardID="'.$cid.'"')->setField('LoginTime', getPHPTime()); // 根据条件更新记录
    }

    public function registerToUsersDB($data) {
        try{
            $data['RegTime'] = getPHPTime();
            $res = $this->_db_users->add($data);
            return $res;
        } catch (Exception $e) {
            return show(0 , $e->getMessage());
        }
    }

    public function registerToFindPSWDB($data) {
        try{
            $res = $this->_db_findpsw->add($data);
            return $res;
        } catch (Exception $e) {
            return show(0 , $e->getMessage());
        }
    }

    public function getFindPswByCardID($cid='') {
        $res = $this->_db_findpsw->where('CardID="'.$cid.'"')->find();
        return $res;
    }

    public function resetPswByCardID($cid, $psw){
        try{
            $ret = $this->_db_users->where('CardID="'.$cid.'"')->setField('Password', $psw);
            return $ret;
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }
    }

}
