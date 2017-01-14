<?php 
namespace Home\Controller;
use Think\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Model;


class QaController extends Controller{
    

    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
    
    /**
     * 问答首页(www.liansuo.com/qa/)
     */
    public function index(){
            $redislbn = A("Common/Comment");
            $redislbn->getRedisNew();
            $member_ent_brandinfoModel = new Member_ent_brandinfoModel();    //实例化模型
            //将行业存入redis
            if($redislbn->redisNew->exists('qa_inarrlbn')){
                $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
            }else{
                $inarr = $member_ent_brandinfoModel->getIndustryTotal();
                $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
            }
           // echo "<pre>";var_Dump($inarr);die;
            //将专题存入redis 
            if($redislbn->redisNew->exists('qa_seotaglbn')){
                $seotag = json_decode($redislbn->redisNew->get('qa_seotaglbn'),true);
            }else{
               $seotag = $member_ent_brandinfoModel->getSeotaglist();      //专题
                $redislbn->redisNew->setex('qa_seotaglbn',259000,json_encode($seotag));
            }
            //将分页总数存入redis        
            //$redislbn->redisNew->del('qa_pagenumlbn');
            if($redislbn->redisNew->exists('qa_pagenumlbn')){
                $numPage = json_decode($redislbn->redisNew->get('qa_pagenumlbn'),true);
            }else{
                $askModel = new Model();
                $askArrayStart = $askModel->query("select count(-1) as num from ls_member_base as a,ls_qa_ask as b where a.memberid=b.pid and a.delstatus=1 and b.ask_content <> '' and b.req_num>1 ");
                $numPage = $askArrayStart[0]['num'];
                $redislbn->redisNew->setex('qa_pagenumlbn',555000,json_encode($numPage));
            }
            
            //问题搜索
            if(!empty($_POST['asklike'])){
                $asklike = I('post.asklike');
                $where3['ask_content'] = $asklike;
                $asklikeArray = $askModel->where($where3)->select();
                $numPage = count($asklikeArray);
                if(empty($asklikeArray)){
                    $asklikeArray = '无';
                }
            }
            $pageObj = new \Think\Page($numPage,10);// 实例化分页类 传入总记录数和每页显示的记录数(20)
            $pageObj->setConfig('prev', '上一页');
            $pageObj->setConfig('next', '下一页');
            $pageObj->setConfig('last', '尾页');
            $pageObj->setConfig('first', '首页');
            $pageObj->rollPage=5;
            $page_tpl = urlencode('[PAGE]');
            $pageObj->url = 'http://www.liansuo.com/qa/p'.$page_tpl.'.html';
            $page = isset($_GET['page'])?($_GET['page']-1)*10:0;
            $pagehou = explode('/',I('server.REQUEST_URI'));
            $show = $pageObj->show();// 分页显示输出
            $this->assign('page',$show);
            $this->assign('pagenum',I('get.page'));
            $this->assign('pagehou',$pagehou[2]);
            $this->assign('seotag',$seotag);
            $this->assign('startpage',$page.',10'); 
            $this->assign('indusarr',$inarr);
            $this->assign('asklikeArray',$asklikeArray);
            echo $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/qa/','index');
    }
    
    /**
     * 问答列表页面
     */
    public function qalist(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $redislbn->getRedisNew();
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();    //实例化模型
        $classModel = new Model('class_industry');
        $pathname = I('get.industry');
        $where['pathname'] = $pathname;
        $classArray = $classModel->where($where)->find();
        //获取所有行业pathname
        if($redislbn->redisNew->exists('qa_allIndustryArray')){
            $allIndustry = json_decode($redislbn->redisNew->get('qa_allIndustryArray'));
        }else{
            $allIndustryArray = $classModel->field('pathname')->select();
            foreach($allIndustryArray as $one){
                $allIndustry[] = $one['pathname'];
            }
            $redislbn->redisNew->set('qa_allIndustryArray',json_encode($allIndustry));
        }
        if(!in_array($_GET['industry'],$allIndustry)){
            header("Location:http://www.liansuo.com/error.html");die;
        }
        if($classArray['id']>16){
            $subclass = $member_ent_brandinfoModel->getAnyindustry ($classArray['parentid']);//行业
        }else{
            $subclass = $member_ent_brandinfoModel->getAnyindustry ($classArray['id']);//行业
        }
        $seotag = $member_ent_brandinfoModel->getSeotaglist($classArray['id']); 
        if(empty($seotag)){
            $seotag = $member_ent_brandinfoModel->getSeotaglist($classArray['parentid']);
        }
        //问题搜索
        if(!empty($_POST['asklike'])){
            $askModel = new Model('qa_ask');
            $asklike = I('post.asklike');
            $where3['ask_content'] = $asklike;
            $asklikeArray = $askModel->where($where3)->select();
            $numPage = count($asklikeArray);
            if(empty($asklikeArray)){
                $asklikeArray = '无';
            }
        }
        //数据分页
        $pageObj = new \Think\Page(100,10);// 实例化分页类 传入总记录数和每页显示的记录数(20)
        $pageObj->setConfig('prev', '上一页');
        $pageObj->setConfig('next', '下一页');
        $pageObj->setConfig('last', '尾页');
        $pageObj->setConfig('first', '首页');
        $pageObj->rollPage=5;
        $page_tpl = urlencode('[PAGE]');
        $pageObj->url = 'http://www.liansuo.com/qa/'.$pathname.'/p'.$page_tpl.'.html';
        $page = isset($_GET['page'])?($_GET['page']-1)*10:0;
        $show = $pageObj->show();// 分页显示输出
        
        
        //echo "<pre>";var_dump($asklikeArray);die;
       // echo $classArray['id'];
        
        $this->assign('page',$show);
        $this->assign('pagenum',I('get.page'));
        $this->assign('seotag',$seotag);
        $this->assign('startpage',$page.',10'); 
        $this->assign('cid',$classArray['id']);  
        $this->assign('classArray',$classArray); 
        $this->assign('asklikeArray',$asklikeArray); 
        $this->assign('subclass',$subclass);
        $this->buildHtml($pathname.'.html', DOCUMENT_ROOT_DIR.'/qa/','qalist');
        $this->display();
    }
    
    
    /**
     * 问答详情页面
     */
    public function qadetail(){
        $redislbn = A('Common/Comment');
        $redislbn->getredis();
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();    //实例化模型
        $aid = I('get.id');
        $publicModel = new Model('qa_know');
        $knowArray = $publicModel->query("select * from ls_qa_know where aid=$aid limit 10");
        $askArray = $publicModel->query("select * from ls_qa_ask where askid=$aid limit 10");
        if(empty($askArray)){
            header('HTTP/1.1 404 Not Found');
            include('/data/www/www.liansuo.com/header_footer/error.html');die;
        }
        $seotag = $member_ent_brandinfoModel->getSeotaglist($askArray['id']);                                       //相关专题
        $industry = $publicModel->query("select * from ls_class_industry where id=".$askArray[0]['industry']);      //面包屑大行业
        $subindustry = $publicModel->query("select * from ls_class_industry where id=".$askArray[0]['subindustry']);//面包屑小行业
        
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', $askArray[0]['pid']), true);
        
        if(!empty($_POST['knowtext'])){
            $data['aid'] = $aid;
            $data['ctime'] = date("Y-m-d H:i:s");
            $data['know_content'] = $_POST['knowtext'];
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $data['nickname'] = "匿名";
            $submitArray = $publicModel->data($data)->add();
        }

        $this->assign('knowArray',$knowArray);
        $this->assign('askArray',$askArray[0]);
        $this->assign('subindustry',$subindustry[0]);
        $this->assign('brandinfo',$brandinfo);
        $this->assign('industry',$industry[0]);
        $this->assign('seotag',$seotag);
        $this->buildHtml($aid.'.html', DOCUMENT_ROOT_DIR.'/qa/','qadetail');
        $this->display();
    }
}
?>