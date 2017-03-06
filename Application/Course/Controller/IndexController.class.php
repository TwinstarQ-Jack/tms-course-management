<?php
namespace Course\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index() {
        D('Public')->IsUserSession();
        $this->display();
    }
    public function read() {
        D('Public')->IsUserSession();
        $this->display();
    }
}