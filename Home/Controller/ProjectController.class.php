<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class ProjectController extends Controller {

    //项目首页
    public function index(){
//         $redislbn = A('Common/Comment');                                                //实例化Common模块下的Comment控制器
//         $redislbn->getRedis();
//         $redislbn->getRedisNew();
        
//         $a = $redislbn->redisNew->keys('*');
        
//         echo count($a);die;
//         echo "<pre>";  var_dump(json_decode($a,true));
//         die; 
        
         
        $result = $this->getPublic(); 
        $this->display("Public/index");
    }
    
    
    //市场前景
    public function scqj(){       
        $this->getPublic();
        $this->display("Public/scqj");
    }
    
    //加盟优势
    public function jmys(){       
        $this->getPublic();
        $this->display("Public/jmys");
    }
    
    //经营分析
    public function jyfx(){       
        $this->getPublic();
        $this->display("Public/jyfx");
    }
    
    //产品展示
    public function cpzs(){       
        $this->getPublic();
        $this->display("Public/cpzs");
    }
    
    //品牌新闻
    public function ppxw(){
        $this->getPublic($ppxwh='ppxwh');
        $this->display("Public/ppxw");
    }
    
    //联系方式
    public function lxfs(){       
        $this->getPublic();
        $this->display("Public/lxfs");
    }

    /**
     * 公共方法
     */
    function getPublic($ppxwh){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();                   //实例化模型
        $redislbn = A('Common/Comment');                                                //实例化Common模块下的Comment控制器
        $redislbn->getRedis();  
        $redislbn->getRedisNew();                                                       //调取redis方法
        //调取redis中的项目信息
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', I('get.id')), true);
        if(empty($brandinfo)){
             header("Location:http://www.liansuo.com/error.html");exit; 
        }
        $allindustryTemp = json_decode($redislbn->redis->hget('classindustry', 'keyarray'), true);
        $industry = $allindustryTemp[$brandinfo['industry']];
        $subindustry = $allindustryTemp[$brandinfo['subindustry']];
        $memberid = $brandinfo['memberid'];
        $url = "/p".$memberid."/";
        $jptj = $member_ent_brandinfoModel->jptj($brandinfo['industry'],3);             //取出精品推荐
        $jmjj = $member_ent_brandinfoModel->jmjj($brandinfo['memberid']);               //取出加盟聚焦
        $phbCom = $member_ent_brandinfoModel->phb(0);
        $phbMax = $member_ent_brandinfoModel->phb($brandinfo['industry']);
        $phbMin = $member_ent_brandinfoModel->phb($brandinfo['subindustry']);
        $newsModel = new Model();
        //判断是否有新闻 
        $page = empty($_GET['p'])?1:$_GET['p'];
        $pgnum = 10;//每页显示数
        $start = $pgnum * ($page - 1);//起始数
        
        //var_dump(substr($brandinfo['vcr_href'],11,-1));
        
        
        if($redislbn->redisNew->exists($memberid)){            
            if($redislbn->redisNew->get($memberid)!='[]'){
                $pagenum = count(json_decode($redislbn->redisNew->get($memberid)));      //取出新闻总数
            }else{
                $pagenum = 0;
            }
        }else{
            $a = $member_ent_brandinfoModel->ppxwcx($memberid,$start,$pgnum);
            if($redislbn->redisNew->get($memberid)!='[]'){
                $pagenum = count(json_decode($redislbn->redisNew->get($memberid)));      //取出新闻总数
            }else{
                $pagenum = 0;
            }
        }
      
        //品牌新闻
        if($ppxwh=='ppxwh'){
            $ppxwArray = $member_ent_brandinfoModel->ppxwcx($memberid,$start,$pgnum);   //取出品牌新闻
            $newsurl = "/p$memberid/ppxw/";
            ////执行thinkphp分页操作
            $pageObj = new \Think\Page($pagenum,$pgnum);                                //实例化分页类 传入总记录数和每页显示的
            $pageObj_tpl = urlencode('[PAGE]');
            $pageObj->url = U("p{$memberid}/ppxw/p{$pageObj_tpl}");
            $pageObj->setConfig('prev', '上一页');
            $pageObj->setConfig('next', '下一页');
            $pageObj->setConfig('last', '尾页');
            $pageObj->setConfig('first', '首页');
            $showpage = $pageObj->show();                                                //分页显示输
            $this->assign ( 'rgej', $ppxwArray );
            $this->assign ( 'fenyeStr', $showpage );
        }
        
        $this->assign('industry',$industry);
        $this->assign('subindustry',$subindustry);
        $this->assign('url',$url);
        $this->assign('jptj',$jptj);
        $this->assign('jmjj',$jmjj);
        $this->assign('brandinfo',$brandinfo);
        $this->assign('newshas',$pagenum);
        $this->assign('newsurl',$newsurl);
        $this->assign('phbCom',$phbCom);
        $this->assign('phbMin',$phbMin);
        $this->assign('phbMax',$phbMax);
    }
    
    
                                                            /** end--项目代码结束--end **/ 
    
    
    //存入全部新闻
    function saveToredisFromNews(){
        $id = I('get.newsid');
        $newsModel = new Model();
        //更新所有新闻
        if($id=='all'){
            $allid=$newsModel->query("select newsid from ls_news_arc where flag!=0 order by newsid desc limit 445000,30000");        //取出所有新闻id
            //$allid=$newsModel->query("select newsid from ls_news_arc where flag!=0 and newsid>99844 and newsid<99846 ");        //取出所有新闻id
            $allnewsid=array();
            foreach ($allid as $one){
                $allnewsid[]=$one['newsid'];                   
            }
            $clipCount = count($allnewsid)/100;
            for($a=0;$a<$clipCount;$a++){
                $start = $a*100;
                $num = array_slice($allnewsid,$start,101);
                $changeArray = $newsModel->query("select a.*,b.* from ls_news_arc as a,ls_news_text as b where a.newsid=b.news_id and a.newsid in (".join(',',$num).") ");
                $clipArray = $this->outArrayField($changeArray);  //去除数组空字段
                foreach($clipArray as $key=>&$one){
                    if($key!==100){
                         $one = $this->addNewsSource($one);    //添加资源
                         // $one = $this->addNewsXgxw($one);      //添加相关新闻 
                         $strtotag = $this->strtotag($one['newsid']);
                         $one['strtotag'] = $strtotag;
                        if($key==0){
                            $one['b_id'] = $tempid;
                            $one['b_title'] = $temptitle;
                            $one['b_url'] = $tempurl;
                            $one['l_id'] = $clipArray[$key+1]['newsid'];
                            $one['l_title'] = $clipArray[$key+1]['title'];
                            $one['l_url'] = $clipArray[$key+1]['url'];
                        }else{
                            $one['b_id'] = $clipArray[$key-1]['newsid'];
                            $one['b_title'] = $clipArray[$key-1]['title'];
                            $one['b_url'] = $clipArray[$key-1]['url'];
                            $one['l_id'] = $clipArray[$key+1]['newsid'];
                            $one['l_title'] = $clipArray[$key+1]['title'];
                            $one['l_url'] = $clipArray[$key+1]['url'];
                        }
                    }
                    $tempid = $clipArray[99]['newsid'];
                    $temptitle = $clipArray[99]['title'];
                    $tempurl = $clipArray[99]['url'];
                }
                $this->doSaveToRedis($clipArray);
            }
        }else{
            //只更新一篇新闻
            $newsid=intval($id);
            $clipArray = $newsModel->query("select a.*,b.* from ls_news_arc as a,ls_news_text as b where a.newsid=b.news_id and a.newsid=".$newsid." ");
            $clipArrayBefore = $newsModel->query("select newsid,title,url from ls_news_arc where newsid<".$newsid." order by newsid desc limit 2 ");
            $clipArrayLast = $newsModel->query("select newsid,title,url from ls_news_arc where newsid>".$newsid." limit 2 ");
            $clipArray = $this->outArrayField($clipArray);  //去除数组空字段
            foreach($clipArray as &$one){
                $one = $this->addNewsSource($one);      //添加资源
                //$one = $this->addNewsXgxw($one);      //添加相关新闻
                $strtotag = $this->strtotag($one['newsid']);
                $one['strtotag'] = $strtotag;
                $one['b_id'] = $clipArrayBefore[0]['newsid'];
                $one['b_title'] = $clipArrayBefore[0]['title'];
                $one['b_url'] = $clipArrayBefore[0]['url'];
                $one['l_id'] = $clipArrayLast[0]['newsid'];
                $one['l_title'] = $clipArrayLast[0]['title'];
                $one['l_url'] = $clipArrayLast[0]['url'];
            } 
            $this->doSaveToRedis($clipArray);
        }
    }
    
    /**
     * 写入redis
     */
    function doSaveToRedis($clipArray){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        foreach ($clipArray as $key=>&$one){
            if($one['flag']!==0){                       //判断是否为数据库删除新闻。
                if(!empty($one)){
                    if($one['is_jump']==1 || $one['deflag']=='j'){
                        $one['url'] = $one['outsideurl'];
                    }
                $a = $redislbn->redisNew->set('news_'.$one['newsid'],json_encode($one));
                echo $a.'<br/>';
                }
            }else{
                $a = $redislbn->redisNew->del('news_'.$one['newsid']);
            }
        }
    }
    
    
    /**
     * 后台执行删除操作
     */
    function doDelToRedis(){
        $newsid = I('get.newsid');
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $newsModel = new Model();
        $clipArrayBefore = $newsModel->query("select a.*,b.* from ls_news_arc as a,ls_news_text as b where a.newsid=b.news_id and a.newsid<".$newsid." order by a.newsid desc limit 2 ");
        $clipArrayLast = $newsModel->query("select a.*,b.* from ls_news_arc as a,ls_news_text as b where a.newsid=b.news_id and a.newsid>".$newsid." limit 2 ");
        //修改上一篇新闻的下一篇新闻id
        foreach($clipArrayBefore as &$one){
            $one['l_id'] = $clipArrayLast[0]['newsid'];
            $one['l_title'] = $clipArrayLast[0]['title'];
            $one['l_url'] = $clipArrayLast[0]['url'];
            $one['b_id'] = $clipArrayBefore[1]['newsid'];
            $one['b_title'] = $clipArrayBefore[1]['title'];
            $one['b_url'] = $clipArrayBefore[1]['url'];
            $a = $redislbn->redisNew->set('news_'.$one['newsid'],json_encode($one));
        }
        //修改下一篇新闻的上一篇新闻id
        foreach($clipArrayLast as &$one){
            $one['b_id'] = $clipArrayBefore[0]['newsid'];
            $one['b_title'] = $clipArrayBefore[0]['title'];
            $one['b_url'] = $clipArrayBefore[0]['url'];
            $one['l_id'] = $clipArrayLast[1]['newsid'];
            $one['l_title'] = $clipArrayLast[1]['title'];
            $one['l_url'] = $clipArrayLast[1]['url'];
            $a = $redislbn->redisNew->set('news_'.$one['newsid'],json_encode($one));
        } 
        $a = $redislbn->redisNew->del('news_'.$newsid);
    }
    
    //取出数组空字段
    function outArrayField($changeArray){
        $clipArray  = array();
        foreach($changeArray as $key=>$v){
            $oneKey = array_keys($v);
            foreach($oneKey as $vv){
                if($v[$vv] !== '' && $v[$vv] !== NULL){
                    $clipArray[$key][$vv] = $v[$vv];
                }
            }
        }
        unset($changeArray);
        return $clipArray;
    }
    
    //向单条新闻中添加资源
    function addNewsSource($one){
        $newsModel = new Model();
        $sourceArray = $newsModel->query('select newsid from ls_resources_news where newsid='.$one['newsid']);
        if($sourceArray){
            $sourceArray = $newsModel->query("select typeid,source_content from ls_source_content where newsid=".$one['newsid']." order by typeid desc,id desc ");
            $i = 1;
            $type = array();
            foreach($sourceArray as $two){
                if(in_array($two['typeid'],$type)){
                    $i++;
                    $one["s_".$two['typeid']."_".$i.""] = $two['source_content'];
                }else{
                    $type[] = $two['typeid'];
                    $one["s_".$two['typeid']."_1"] = $two['source_content'];
                    $i = 1;
                }
            }
        }
        return $one;
    }
    
    
    /**
     * 为单篇新闻添加相关新闻
     */
    public function addNewsXgxw($newsXgxw){
        $string = $newsXgxw['tag'];
        $newsid = $newsXgxw['newsid'];
        $catalog_id = $newsXgxw['catalog_id'];
        
        $model = new Model('news_arc');
        if(!empty($string)){
            $string=str_replace('，', ',', $string);
            $string=str_replace(' ', ',', $string);
            $temparr=explode(',', $string);
            $string=array();
            $where['keywords'] =array('like','%'.$temparr[0].'%');
            $where['iscreatehtml'] = 1;
            $where['flag'] = array('neq',0);
            $arcArray = $model->field('newsid')->where($where)->order('newsid desc')->limit(8)->select();
            foreach($arcArray as $one){
                $array[] = $one['newsid'];
            }
            shuffle($array);
        }
        if(!empty($temparr[1])){
            $where2['keywords'] =array('like','%'.$temparr[1].'%');
            $where2['iscreatehtml'] = 1;
            $where2['flag'] = array('neq',0);
            $arcArray = $model->field('newsid')->where($where2)->order('newsid desc')->limit(8)->select();
            foreach($arcArray as $one){
                array_push($array,$one['newsid']);
                shuffle($array);
            }
        }
        if(count($array)<8){
            $where3['catalog_id'] = $catalog_id;
            $where3['iscreatehtml'] = 1;
            $where3['flag'] = array('neq',0);
            $arcArray = $model->field('newsid')->where($where3)->order('newsid desc')->limit(8)->select();
            if(empty($array)){$array = array();}
            foreach($arcArray as $one){
                array_push($array,$one['newsid']);
                shuffle($array);
            }
        }
        $arrayNews = array();
        foreach($array as $one){
            $arrayNews[] = 'news_'.$one;
        }
        $arrayNews = implode(',',$arrayNews);
        $newsXgxw['xgxw'] = $arrayNews;
        return $newsXgxw;
    }
    
    //news页面下相关阅读出的tag处理
    public function strtotag($newsid){
        $tagindexModel = new Model('seo_taglist');
        $where['aid'] = $newsid;
        $tagindexArray = $tagindexModel->field('tid,tag')->where($where)->select();
        foreach ($tagindexArray as $one){
            if(!empty($one)){
                $tagindexArray2[] = $one;
            }
        }
        return $tagindexArray2;
    }
    
} 
