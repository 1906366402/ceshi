<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Model;

class NewshomeController extends Controller {
    /**
     * 新闻页面(http://www.liansuo.com/news/)
     */
    public function index(){
        echo  $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/news/','index');
    }
    /** 
     * 自动作业系统
     */
    public function fun(){
        $this->index();
    }
    

    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
}