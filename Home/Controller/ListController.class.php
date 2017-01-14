<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class ListController extends Controller {

    //加盟指南
    public function jmzn(){
        $deflag = 'e';
        $this->publicmb($deflag);
        $this->display('jmzn');
    }
    
    //选址筹备
    public function xzcb(){
        $deflag = 'g';
        $this->publicmb($deflag);
        $this->display('xzcb');
    }
    
    //日常经营
    public function rcjy(){
        $deflag = 'i,k,o';
        $this->publicmb($deflag);
        $this->display('rcjy');
    }
     
    //项目咨询
    public function xmzx(){
        $deflag = 'i,q';
        $this->publicmb($deflag);
        $this->display('xmzx');
    }
    
    //经典案例
    public function jdal(){
        $deflag = 'n';
        $this->publicmb($deflag);
        $this->display('jdal');
    }
    
    //人物专访
    public function rwzf(){
        $deflag = 'm';
        $this->publicmb($deflag);
        $this->display('rwzf');
    }
    
    //参展信息
    public function czxx(){
        $deflag = 'x';
        $this->publicmb($deflag);
        $this->display('czxx');
    }
    
    //行业动态
    public function hydt(){
        $deflag = 'q,u';
        $this->publicmb($deflag);
        $this->display('hydt');
    }
    
    //公用方法
    public function publicmb($deflag){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();    //实例化模型
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
        $pageUrl2 = explode('/',$_SERVER['PATH_INFO']);
        $pageUrl = $pageUrl2[1];
        $industry = I('get.industry');      //获取行业
        //取出新闻栏目id
        $category = json_decode($redislbn->redis->hget('category_lib','category'),true);
        foreach($category as $key=>$four){
            if($four['categorydir'] == $industry){
                  $deCid = $key;  
                  $s_industry = $four['s_industry'];
                  $b_industry = $four['b_industry'];
                  $industryname = $four['categoryname'];
            }
        }
        //若小于17则为二级行业，再取出二级行业所对应的栏目id
        
        if($deCid<17){
            $parid_arr = json_decode($redislbn->redis->hget('category_lib','parid_arr'),true);
            $deCid = $parid_arr[$deCid];
            $deCid = implode(',',$deCid);
            $cid = $b_industry; 
        }else{
            $cid = $deCid;
        }
        //判断具有几个新闻属性
        $deflag = explode(',',$deflag);
        if(count($deflag)==1){
            $where = $deflag[0].'=1';
        }elseif(count($deflag)==2){
            $where = $deflag[0].'=1 or '.$deflag[1].'=1';
        }elseif(count($deflag)==3){
            $where = $deflag[0].'=1 or '.$deflag[1].'=1 or '.$deflag[2].'=1';
        }
        $getPage = I('get.page');   //获取当前页码
        $pagenum = empty($getPage)?0:($getPage-1)*20;
        $tagModel = new Model();
        if($industry=="news"){
            $tagArray1 = $tagModel->query("select newsid from ls_news_deflag where iscreatehtml=1 and ($where) order by newsid desc limit $pagenum,20");
        }else{
            $tagArray1 = $tagModel->query("select newsid from ls_news_deflag where cid in ($deCid) and iscreatehtml=1 and ($where) order by newsid desc limit $pagenum,20"); 
        }

        
        $redislbn->redisNew->del($industry.'_'.$pageUrl);
        if($redislbn->redisNew->exists($industry.'_'.$pageUrl)){
                $countArc =$redislbn->redisNew->get($industry.'_'.$pageUrl);
        }else{
            if($industry=="news"){
                $countArc = $tagModel->query("select count(1) as num from ls_news_deflag where ($where) and iscreatehtml=1 order by newsid desc ");
                $countArc = isset($countArc[0]['num'])?$countArc[0]['num']:0;
            }else{
                $countArc = $tagModel->query("select count(1) as num from ls_news_deflag where ($where) and cid in ($deCid) and iscreatehtml=1 order by newsid desc ");
                $countArc = isset($countArc[0]['num'])?$countArc[0]['num']:0;
            }
            $a = $redislbn->redisNew->setex($industry.'_'.$pageUrl,86400,$countArc);
        }
        foreach($tagArray1 as $one){
            $tagArray2[] = 'news_'.$one['newsid'];
        }
        $tagArray3 = $redislbn->redisNew->getMultiple($tagArray2);
        foreach($tagArray3 as $two){
            $tagArray[] = json_decode($two,true);
        }

        //页面碎片
        $news_ztbd = $member_ent_brandinfoModel->news_source('news_ztbd',759,3,$source="{pic:240*64}");   //专题报道
        $list_top = $member_ent_brandinfoModel->phb($s_industry,10);                                      //排行榜
        $ad_er = $member_ent_brandinfoModel->newsad('ad_er',905,0,3);                                     //排行榜下方广告
        
        //大家都在搜
        $tagindexModel = new Model('seo_tagindex');
        $where2['catalog_id'] = $deCid[0];
        $tagindexArray = $tagindexModel->field('id,tag')->where($where2)->order('id desc,total desc')->limit(15)->select();

        
        //数据分页
        $pageObj = new \Think\Page($countArc,20);// 实例化分页类 传入总记录数和每页显示的记录数(20)
        $pageObj->setConfig('prev', '上一页');
        $pageObj->setConfig('next', '下一页');
        $pageObj->setConfig('last', '尾页');
        $pageObj->setConfig('first', '首页');
        $pageObj->rollPage=5;
        $page_tpl = urlencode('[PAGE]');

        
        if($pageUrl=='jmzn'){$pageUrlname = '加盟指南';
        }elseif($pageUrl=='xzcb'){$pageUrlname = '选址筹备';
        }elseif($pageUrl=='rcjy'){$pageUrlname = '日常经营';
        }elseif($pageUrl=='xmzx'){$pageUrlname = '项目资讯';
        }elseif($pageUrl=='jdal'){$pageUrlname = '经典案例';
        }elseif($pageUrl=='rwzf'){$pageUrlname = '人物专访';
        }elseif($pageUrl=='czxx'){$pageUrlname = '参展信息';
        }elseif($pageUrl=='hydt'){$pageUrlname = '行业动态';
        }elseif($pageUrl=='jmyh'){$pageUrlname = '加盟优惠';
        }elseif($pageUrl=='hybg'){$pageUrlname = '行业报告';}
        
        $industry = I('get.industry');
        $pageObj->url = 'http://www.liansuo.com/'.$industry.'/'.$pageUrl.'/p'.$page_tpl.'.html';
        
        $hurl = dirname($pageObj->url).'/';
        
        $show = $pageObj->show();// 分页显示输出
        //模板输出
        $this->assign('list_top',$list_top);
        $this->assign('connPage',$getPage);
        $this->assign('news_ztbd',$news_ztbd);
        $this->assign('pageUrl',$pageUrl);
        $this->assign('industryname',$industryname);
        $this->assign('cid',$cid);
        $this->assign('industry',$industry);
        $this->assign('pageUrlname',$pageUrlname);
        $this->assign('hurl',$hurl);
        $this->assign('ad_er',$ad_er);
        $this->assign('tagArray',$tagArray);
        $this->assign('tagArrayAll',$tagindexArray);
        $this->assign('page',$show);
        
    }
} 