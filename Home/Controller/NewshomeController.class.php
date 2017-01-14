<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Model;

class NewshomeController extends Controller {
    /**
     * 新闻页面
     */
    public function index(){
        
        
        $this->buildHtml('index.html', $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/Web/news/','index');
       //  $this->display();
    }
    
    public function fun(){
        $this->index();
    }
    
    
    public function create(){
        $model = new Model('class_industry');
        $array = $model->field('id,pathname')->select();
        foreach($array as $one){
            if(!empty($one['pathname'])){
                echo "http://".$one['pathname'].".liansuo.com/qa/"." "."http://m.liansuo.com/qa/".$one['pathname'].'/<br/>';
            }
                
        }
        
    }
    
    
    
}