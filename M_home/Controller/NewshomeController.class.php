<?php
namespace  M_home\Controller;
use Think\Controller;
use  M_home\Model\Member_ent_brandinfoModel;
use Think\Model;

class NewshomeController extends Controller {
    /**
     * 新闻页面
     */
    public function index(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();  //实例化模型
        $brandModel = new Model('member_ent_brandinfo');
        $where['joinlinemin'] = array('ELT',1);
        $brandArray = $brandModel->where($where)->order('cindex desc')->limit(8)->select();
 
        //$this->buildHtml('index.html', $_SERVER['DOCUMENT_ROOT'].'/Web/news/','index');  
        $this->display();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
} 