<?php
namespace Common\Model;
use Think\Exception;
use Think\Model;

class ThemeModel extends Model {
	private $_db_chapter = '';
	private $_db_themes = '';

	public function __construct() {
		$this->_db_chapter = M('chapters');
		$this->_db_themes = M('themes');
	}
	//获取指定条件的作业数量
    public function getNumOfHw($paras) {
	    $ret = $this->_db_themes->where($paras)->count();
	    return $ret;
    }
    public function getPageNumOfHw($paras, $pageSize = 15) {
        $ret = $this->_db_themes->where($paras)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getListOfHw($paras, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_themes->where($paras)->order('ThemeID desc')->limit($offset, $pageSize)
            ->select();
        return $list;
    }
    //获取单tid的内容
    public function getInfoByTID($tid) {
	    $ret = $this->_db_themes->where(array('ThemeID'=>$tid))->find();
	    return $ret;
    }
    //获取tid的类型（小组作业或个人作业）
    public function getthemetype($tid) {
        $ret = $this->_db_themes->where(array('ThemeID'=>$tid))->getField('ThType');
        return $ret;
    }
	//查找指定章的最新的主题，返回ID号
    public function latestThemeID($paras = array()) {
        $num = $this->_db_themes->where($paras)->order('CreateTime desc')->getField('ThemeID');
        return $num;
    }
    public function latestChapterID($paras = array()) {
        $num = $this->_db_chapter->where($paras)->order('CreateTime desc')->getField('ChapterID');
        return $num;
    }
    //Chapter分页
    public function getChapterPageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_chapter->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getChapterList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_chapter->where($data)->order('ChapterID desc')->limit($offset, $pageSize)
            ->select();
        return $list;
    }
    public function getChapterAllList($data, $field) {
        $list = $this->_db_chapter->where($data)->order('ChapterID')->field($field)->select();
        return $list;
    }
    //Theme分页
    public function getThemePageCount($data = array(), $pageSize = 15) {
        $ret = $this->_db_themes->where($data)->count();
        if($ret % $pageSize == 0){
            $ret /= $pageSize;
        } else{
            $ret = $ret / $pageSize + 1;
        }
        return $ret;
    }
    public function getThemeList($data, $page = 1, $pageSize = 15) {
        $offset = ($page - 1) * $pageSize;
        $list = $this->_db_themes->where($data)->order('ThemeID desc')->limit($offset, $pageSize)
            ->select();
        return $list;
    }
    //获取Thtype为介绍的所有帖子（ThemeID、ShowName、CreatorID和时间）
    public function getIntroList($cid) {
	    $data['ThType'] = array('eq', '0');
	    $data['ChapterID'] = $cid;
        $list = $this->_db_themes->where($data)->order('ThemeID desc')
            ->field('ThemeID,ShowName,CreatorID,CreateTime')->select();
        return $list;
    }
    //删除指定chapter
    public function delChapterByCID($cid) {
        $ret = $this->_db_chapter->where(array('ChapterID'=>$cid))->delete();
        return $ret;
    }
    //删除指定theme
    public function delThemeByTID($tid) {
        $ret = $this->_db_themes->where(array('ThemeID'=>$tid))->delete();
        return $ret;
    }
    //获取指定上课年级的全体cid+name
    public function getChapterByGrade($grade) {
	    $ret = $this->_db_chapter->where(array('Grade'=>$grade))
                    ->field(array('ChapterID','ShowName'))->select();
	    return $ret;
    }
    //获取指定cid的内容
    public function getChapterByCID($cid) {
	    $ret = $this->_db_chapter->where(array('ChapterID'=>$cid))->find();
	    return $ret;
    }
    //获取指定tid的内容
    public function getThemeByTID($tid) {
	    $ret = $this->_db_themes->where(array('ThemeID'=>$tid))->find();
	    return $ret;
    }
	//新建主题
    public function addNewTheme($data) {
        $creatorid = $_SESSION['TMSUser']['cardid'];
        if(trim($data['Deadline']) == null){
            $data['Deadline'] = getPHPTime();
        }
        $data['CreatorID'] = $creatorid;
        $index = $this->latestThemeID($data['ChapterID']);
        if($index === null){
            $index = $data['ChapterID'] * 1000 + 1;
        } else{
            $index++;
        }
        $data['ThemeID'] = $index;
        $ret = $this->_db_themes->add($data);
        return $ret;
    }
	//保存主题
    public function saveTheme($tid, $data) {
        $creatorid = $_SESSION['TMSUser']['cardid'];
        if(trim($data['Deadline']) == null){
            $data['Deadline'] = getPHPTime();
        }
        $data['CreatorID'] = $creatorid;
        $ret = $this->_db_themes->where(array('ThemeID'=>$tid))->save($data);
        return $ret;
    }
    //新建章
    public function addNewChapter($data) {
        $creatorid = $_SESSION['TMSUser']['cardid'];
        $data['CreatorID'] = $creatorid;
        $grade = D('Setting')->getValue('nowterm');
        $paras['Grade'] = $grade;
        $index = $this->latestChapterID($paras);
        if($index === null){
            $index = $grade * 1000 + 1;
        } else{
            $index++;
        }
        $data['ChapterID'] = $index;
        $ret = $this->_db_chapter->add($data);
        return $ret;
    }
    //保存主题
    public function saveChapter($cid, $data) {
        $creatorid = $_SESSION['TMSUser']['cardid'];
        $data['CreatorID'] = $creatorid;
        $ret = $this->_db_chapter->where(array('ChapterID'=>$cid))->save($data);
        return $ret;
    }
}
