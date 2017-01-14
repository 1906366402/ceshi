<?php
namespace Home\Controller;

use Think\Controller;
use Think\Model;
header("Content-type: text/html; charset=utf-8"); 
/**
 * 新闻页面
 */

/*
 * SiteMap接口类
 */
class Top10Controller extends Controller
{
    
    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }

    
    function index(){
        //调取16个大行业
        $class_industry = M("class_industry");
        $industry_class = $class_industry->where(array('parentid' =>0))->field('id,industryname,pathname')->select();
         $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //将行业存入redis
        if($redislbn->redisNew->exists('qa_inarrlbn')){
          $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
          $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
          $inarr = $member_ent_brandinfoModel->getIndustryTotal();
          $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        $this->assign('industry_class',$industry_class);
        $this->assign('inarr',$inarr);
        echo $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/top10/','index');         
        //$this->display();
    }

    
    function t10list(){
        $industry = I('get.industry');
        $years = I('get.years');
        //根据年份调取不同十大品牌
        $years = empty($years) ? '2017' : $years;
        if($years=='2017'){
            $begin = '0,10';
        }elseif ($years=='2016') {
            $begin = '10,10';
        }elseif ($years=='2015') {
            $begin = '20,10';
        }
        
        $class_industry = M("class_industry");
        //取行业id
        $industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,parentid,industryname')->find();
        //根据行业父类id调取子行业
        if($industry_pid['parentid']==0){
            $parentid= $industry_pid['id'];
        }else{
            $parentid= $industry_pid['parentid'];
        }
        //dump($parentid);die;
        $industry_list = $class_industry->where(array('parentid' =>$parentid,'status'=>1))->field('id,industryname,pathname')->select();
        //删除子行业为“其他”的元素/pathname值为空
        foreach ($industry_list as $key => $value) {
            if ($value['pathname']==null) {
                unset($industry_list[$key]);
            }
            
        }
        //移动调取头部导航大行业
        $industry_name = $class_industry->where(array('id' =>$parentid,'status'=>1))->field('id,industryname,pathname')->find();
        //dump($industry_name);die;

        //获取新闻总条数
        $news= M("news_deflag");
        //$number对应plist标签参数momid=1
        $number = $news->where("status=1 and (iscreatehtml=1 or j=1) and cid='".$industry_pid['id']."' and memberid =0")->count();
        //$num对应plist标签参数brandinfo="1"
        $num = $news->where("status=1 and (iscreatehtml=1 or j=1) and cid='".$industry_pid['id']."' and memberid !=0")->count();
        /*取所有行业*/
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //将行业存入redis
        if($redislbn->redisNew->exists('qa_inarrlbn')){
          $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
          $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
          $inarr = $member_ent_brandinfoModel->getIndustryTotal();
          $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        $this->assign('years',$years);
        $this->assign('parentid',$parentid);
        $this->assign('number',$number);
        $this->assign('num',$num);
        $this->assign('begin',$begin);
        $this->assign('industry_class',$industry_class);
        $this->assign('industry_list',$industry_list);
        $this->assign('industry_name',$industry_name);
        $this->assign('inarr',$inarr);
        $this->assign('industry',$industry);
        $this->assign('industry_pid',$industry_pid);
        echo $this->buildHtml($industry.'_'.$years.'.html' , DOCUMENT_ROOT_DIR.'/top10/','t10list');        
    }
    
    /**
     * 十大品牌详情页面
     */
    public function t10detail(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $redislbn->getRedisNew();
        $id = I('get.memberid');
        //根据项目id查询redis中项目信息
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', $id), true);
        //echo "<pre>";var_dump($brandinfo);die;
        //没有该项目时跳转到错误页面
        if(empty($brandinfo)){ 
            header('HTTP/1.1 404 Not Found');
            include('/data/www/www.liansuo.com/header_footer/error.html');die;
        }      
        //取出所有行业信息,寻找行业pathname
        $tempcatstr=$redislbn->redis->hget('category_lib','category');
        $tempcaarr=json_decode($tempcatstr,true);
        $pathname = $tempcaarr[$brandinfo['industry']]['categorydir'];
        
        $this->assign('pathname',$pathname);  
        $this->assign('brandinfo',$brandinfo);
        echo $this->buildHtml($id.'.html', DOCUMENT_ROOT_DIR.'/top10/','t10detail');
    }
}
?> 