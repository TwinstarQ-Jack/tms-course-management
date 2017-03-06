<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class SettingModel extends Model {
	private $_db_setting = '';

    public function __construct() {
        $this->_db_setting = M('setting');
    }

    public function getValue($name) {
        $ret = $this->_db_setting->where('Name = "'. $name.'"')->getField('Value');
        return $ret;
    }

    public function setValue($name, $value) {
        $ret = $this->_db_setting->where('Name = "'. $name.'"')->setField('Value', $value);
        return $ret;
    }

}
