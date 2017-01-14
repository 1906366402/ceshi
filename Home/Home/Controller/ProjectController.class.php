<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class ProjectController extends Controller {

    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
    
 /**
     * 新黄页
     */
    public function indexNews(){ 
        $this->getPublic();
        $this->display();
    }

    /**
     * 黄页列表
     */
	  public function projectlist(){
		$industry = I('get.industry');
		$class_industry = M("class_industry");
		$taglistModel = new Model('seo_tagindex');
		$member_ent_brandinfo = M("member_ent_brandinfo");
		
		$getPage = I('get.page')?I('get.page'):1;
		
		//不调取的行业
		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan'";
		
		//取行业id
		$industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,parentid,industryname,pathname')->find();
		//根据行业父类id调取子行业
		if($industry_pid["parentid"]){
			$industry_name = $class_industry->where('parentid = '.$industry_pid['parentid'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}else{
			$industry_name = $class_industry->where('parentid = '.$industry_pid['id'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}

		
		//调取导航栏大行业父类
		$industry_class = $class_industry->where('parentid = 0 AND pathname NOT IN('.$null_class.')')->field('id,industryname,pathname')->select();
		
		foreach($industry_class as $key=>$v){
			$industry_class[$key]["subclass"] = $class_industry->where('parentid = '.$v["id"].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}
		//去除掉子行业没内容的
		/*
		foreach($industry_class as $key=>$v){
			if(!count($industry_class[$key]["subclass"])){
				array_splice($industry_class,$key,1);
			}
		}
		*/
		
		$sql="select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid WHERE (a.industry in(".$industry_pid['id'].") or a.subindustry in(".$industry_pid['id']."))";
		$tempiarr = $member_ent_brandinfo->query($sql); 
		$total_num = count($tempiarr); 
		$pagesize = '20';
		$pagenum = empty($getPage)?0:($getPage-1)*$pagesize;
	
		
		$pageObj = new \Think\Page($total_num,$pagesize);// 实例化分页类
        $pageObj->setConfig('prev', '上一页');
        $pageObj->setConfig('next', '下一页');
        $pageObj->setConfig('last', '尾页');
        $pageObj->setConfig('first', '首页');
        $pageObj->rollPage=5;
		$page_tpl = urlencode('[PAGE]');
		$p_url = '/p/'.$industry; 

		$pageObj->url = $p_url.'/p'.$page_tpl.'.html';;
		$show = $pageObj->show();// 分页显示输出
		

		if($industry_pid["parentid"]==0){
			$tagArrayrmbq = $taglistModel->field('id,tag')->where("catalog_id =".$industry_pid['id'])->order('total desc')->limit(130)->select();
		}else{
			$industry_pid["parent"] = $class_industry->where('id = '.$industry_pid['parentid'])->field('id,industryname,pathname')->find();
			$tagArrayrmbq = $taglistModel->field('id,tag')->where("catalog_id =".$industry_pid["parentid"])->order('total desc')->limit(130)->select();
		}
        shuffle($tagArrayrmbq);
        $tagArrayrmbq = array_slice($tagArrayrmbq,0,13);
            
		$this->assign('tagArrayrmbq',$tagArrayrmbq);
		$this->assign('industry',$industry);
		$this->assign('industry_name',$industry_name);
      	$this->assign('industry_pid',$industry_pid);
		$this->assign('industry_class',$industry_class);
		$this->assign('p_row',$pagenum.",".$pagesize);
		$this->assign('page',$show);
		
		if($getPage==1){
			echo $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/p/'.$industry.'/','projectlist');
		}else{
			echo $this->buildHtml($getPage.'.html', DOCUMENT_ROOT_DIR.'/p/'.$industry.'/','projectlist');
		}
        
        $this->display();
    }
	 
    /**
     * 黄页首页-
     */
    public function project(){
        $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
        $redislbn = A('Common/Comment');                                                //实例化Common模块下的Comment控制器
        $redislbn->getRedis();
        $redislbn->getRedisNew();                                                       //调取redis方法
		//将行业存入redis
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        //调取连锁人物
        $project_lsrw = $member_ent_brandinfoModel->news_source('news_lsrw',745,18,$source='{pic1:115*105}');     //连锁人物
        $this->assign('project_lsrw',$project_lsrw);
        $this->assign('inarr',$inarr);
        echo $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/p/','project');
    }
    
    /**
     * 项目黄页
     */
    public function index(){
        $result = $this->getPublic();
        $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','indexnews');
        $this->display("indexnews");
    }
    
    //市场前景
    public function scqj(){       
        $this->getPublic();
        $this->buildHtml('scqj.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/scqj');
        $this->display("Public/scqj");
    }
    
    //加盟优势
    public function jmys(){       
        $this->getPublic();
        $this->buildHtml('jmys.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/jmys');
        $this->display("Public/jmys");
    }
    //加盟优势
    public function jmlc(){       
        $this->getPublic();
        $this->buildHtml('jmlc.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/jmlc');
        $this->display("Public/jmlc");
    }
    
    //经营分析
    public function jyfx(){       
        $this->getPublic();
        $this->buildHtml('jyfx.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/jyfx');
        $this->display("Public/jyfx");
    }
    
    //产品展示
    public function cpzs(){       
        $this->getPublic();
        $this->buildHtml('cpzs.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/cpzs');
        $this->display("Public/cpzs");
    }
    
    //品牌新闻
    public function ppxw(){
        $this->getPublic($ppxwh='ppxwh');  
        $this->buildHtml('ppxw.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/ppxw');
        $this->display("Public/ppxw");
    }
    
    //联系方式
    public function lxfs(){       
        $this->getPublic();
        $this->buildHtml('lxfs.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/lxfs');
        $this->display("Public/lxfs");
    } 
    
    //连锁地图
    public function lsdt(){
        $this->getPublic();
        $this->buildHtml('lsdt.html', DOCUMENT_ROOT_DIR.'/p/'.I('get.id').'/','Public/lsdt');
        $this->display("Public/lsdt"); 
    } 

    /**
     * 公共方法
     */
    function getPublic($ppxwh){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();                   //实例化模型
        $redislbn = A('Common/Comment');                                                //实例化Common模块下的Comment控制器
        $redislbn->getRedis();  
        $redislbn->getRedisNew();                                                       //调取redis方法
        $id = I('get.id');
        
        /**
         * 销售(phl)需求不展示该黄页信息但是后台不能删除
         */
        if($id==166759 || $id==166765 || $id==166849){
            header("Location:http://www.liansuo.com/error.html",true,301);die;
        }
        
        //调取redis中的项目信息
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', $id), true);
        /* 安然需求将开头有空格的新闻替换掉空格 */ 
		$brandinfo['projectintro'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['projectintro']);
		$brandinfo['projectintro'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['projectintro']);	
		$brandinfo['pc_contents2'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents2']);
		$brandinfo['pc_contents2'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['pc_contents2']);	
		$brandinfo['pc_contents1'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents1']);
		$brandinfo['pc_contents1'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['pc_contents1']);	
		$brandinfo['pc_contents3'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents3']);
		$brandinfo['pc_contents3'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['pc_contents3']);	
		$brandinfo['pc_contents4'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents4']);
		$brandinfo['pc_contents4'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['pc_contents4']);	
        
		$brandinfo['contents3'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['contents3']);
		$brandinfo['contents3'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['contents3']);	
        
        //判断是否上传过海报
//         if($brandinfo['mancheck'] == 0){
//             header("Location:http://www.liansuo.com/baodao/$id.html",TRUE,301);die; 
//         }
        if(empty($brandinfo['memberid'])){
             header("Location:http://www.liansuo.com/error.html",TRUE,301);exit; 
        }
        $allindustryTemp = json_decode($redislbn->redis->hget('classindustry', 'keyarray'), true);
        $industry = $allindustryTemp[$brandinfo['industry']];
        $subindustry = $allindustryTemp[$brandinfo['subindustry']];
        $memberid = $brandinfo['memberid'];
        $url = "/p/".$memberid."/";
        //$jptj = $member_ent_brandinfoModel->jptj($brandinfo['industry'],3);             //取出精品推荐
        $jmjj = $member_ent_brandinfoModel->jmjj($brandinfo['memberid']);                 //取出加盟聚焦
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
            $yema = $_GET['p'];
            ////执行thinkphp分页操作
            $pageObj = new \Think\Page($pagenum,10);                                //实例化分页类 传入总记录数和每页显示的
            $page_tpl = urlencode('[PAGE]');
            $pageObj_tpl = urlencode('[PAGE]');
           // $pageObj->url = U("ppxw/p{$pageObj_tpl}");
            $pageObj->setConfig('prev', '上一页');
            $pageObj->setConfig('next', '下一页');
            $pageObj->setConfig('last', '尾页');
            $pageObj->setConfig('first', '首页');
            $pageObj->url = 'http://www.liansuo.com/p/'.$memberid.'/ppxw/p'.$page_tpl.'.html';
            $showpage = $pageObj->show();                                                //分页显示输
            $this->assign ( 'rgej', $ppxwArray );
            $this->assign ( 'yema', $yema );
            $this->assign ( 'fenyeStr', $showpage );
        }
        
        
        $this->assign('industry',$industry);
        $this->assign('subindustry',$subindustry);
        $this->assign('url',$url);
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
            //$allid=$newsModel->query("select newsid from ls_news_arc where flag!=0 order by newsid desc limit 465000,15000");        //取出所有新闻id
            $allid=$newsModel->query("select newsid from ls_news_arc where flag!=0 and newsid>580000 and newsid<600000 ");        //取出所有新闻id
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
            if($one['flag']!==0){                                                           //判断是否为数据库删除新闻。
                if(!empty($one)){
                    if($one['is_jump']==1 || $one['deflag']=='j'){
                        $one['url'] = $one['outsideurl'];
                    }
                $a = $redislbn->redisNew->set('news_'.$one['newsid'],json_encode($one));
                echo $a.'<br/>';
                $file_dir =  DOCUMENT_ROOT_DIR.'/news/';
                $file_dir_yd =  '/data/www/www.liansuo.com/searchtest.liansuo.com/m_Web/news/';
                $aa = unlink($file_dir.$one['newsid'].'.html');
                $bb = unlink($file_dir_yd.$one['newsid'].'.html');
                echo  'pc端删除'.$aa.'<br/>';
                echo  '移动端删除'.$bb;
                echo $file_dir.$one['newsid'].'.html';
                }
            }else{
                $a = $redislbn->redisNew->del('news_'.$one['newsid']); 
                var_Dump($a);die;
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
        $file_dir =  DOCUMENT_ROOT_DIR.'/news/';
        $file_dir_yd =  '/data/www/www.liansuo.com/searchtest.liansuo.com/m_Web/news/';
        $aa = unlink($file_dir.$newsid.'.html');
        $bb = unlink($file_dir_yd.$newsid.'.html');
        echo  'pc端删除'.$aa.'<br/>';
        echo  '移动端删除'.$bb;
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

    //删除黄页静态页面
    function delProjectFile() {
        $id = $_GET['id'];
        $dir = DOCUMENT_ROOT_DIR.'/p/'.$id;
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    if($file == 'lxfs.html' || $file == 'jyfx.html' || $file == 'lsdt.html' || $file == 'scqj.html' || $file == 'cpzs.html' || $file == 'index.html' || $file == 'jmys.html' || $file == 'jmlc.html' || $file == 'ppxw.html'){
                            $a = unlink($fullpath);    
                            var_dump($a);
                    }
                } else {
                    //deldir($fullpath);
                }
            }
        }
        closedir($dh);
        return $a;
    }
    //删除项目下的新闻
    public function delYellowNews(){
        $redislbn = A('Common/Comment');                                                //实例化Common模块下的Comment控制器
        $redislbn->getRedisNew();
        $id = $_GET['newsid'];
        $news = json_decode($redislbn->redisNew->get('news_'.$id),true);
        
        $memberid = $news['member_id'];
        if(empty($memberid)){
            return 1;
            die;
        }
        $dir = DOCUMENT_ROOT_DIR.'/p/'.$memberid;
        $array = scandir($dir);
        foreach($array as $key=>$one){
            if($one==$id.'.html'){
                $dirrr = $dir.'/'.$one;
                $b = unlink($dirrr); 
                var_dump($b);
            }
        }
    }
} 