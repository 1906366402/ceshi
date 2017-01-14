<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class UnitController extends Controller {
	private $spx=NULL;
	/**
	 * 详情首页
	 */
	public function index(){
	    //echo "<pre>";var_dump($_SERVER);die;
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
	    $page = I('get.page');
	    $model = new Model('news_keywords');
	    $numpage = $model->count();
	    $pageObj = new \Think\Page($numpage,160);// 实例化分页类 传入总记录数和每页显示的记录数(20)
	    $pageObj->setConfig('prev', '上一页');
	    $pageObj->setConfig('next', '下一页');
	    $pageObj->setConfig('last', '尾页');
	    $pageObj->setConfig('first', '首页');
	    $pageObj->rollPage=5;
	    $page_tpl = urlencode('[PAGE]');
	    $pageObj->url = 'http://www.liansuo.com/unit/p'.$page_tpl.'.html';
	    $pagehou = explode('/',I('server.REQUEST_URI'));
	    $show = $pageObj->show();// 分页显示输出 
	    $pagenum = empty($page)?0:($page-1)*160; 
	    $this->assign('pagenum',$pagenum.',160');
	    $this->assign('pagehou',$pagehou[2]);
	    $this->assign('pageurl',$page);
	    $this->assign('inarr',$inarr);
	    $this->assign('page',$show);
        $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/unit/','index'); 
	    $this->display();
	}
	
	/**
	 * 测试页面
	 */
	public function ceshi(){
	    /**
	     * 将文件中的数据写入数据库中file()
	     * file@abstract
	     * news_keywords@abstract
	     */
// 	    $dirname = DOCUMENT_ROOT_DIR.'/ceshi.txt';
// 	    //file将整个文件读入一个数组中。file_get_contents()。将整个文件读入一个字符串中.
// 	    $array = file($dirname);
// 	    echo "<pre>";
// 	    $seoModel = new Model('news_search');
// 	    foreach($array as $two){
// 	        $one = explode(',',$two);
// 	        //$word = str_replace(array("\r","\n","\r\n","\n\r","\r\n\t","\t"),array('','','','','',''),$one[0]); 
// 	        $data['keyword'] = $one[0];
// 	        $data['industry'] =$one[2];
// 	        $data['updated'] =date('Y-m-d H:i:s');
// 	        $data['subindustry'] = $one[1];
// 	        $seoArray = $seoModel->data($data)->add(); 
//             //echo $seoModel->getLastSql(),'<br/>'; 
// 	        var_dump($seoArray);
// 	    }
	    
	    /**
	     * 删除数据库中重复数据
	     * group_concat
	     * group_by@abstract
	     * 
	     */
// 	    $model = new Model('news_keywords');
// 	    $array = $model->query("select group_concat(aid) as aidd,`key` from ls_news_keywords where `key` <> '' group by `key`  limit 10000");
// 	    echo "<pre>";  
// 	    foreach($array as $one){
// 	        if(strlen($one['aidd']) > 7){
// 	            $liu[] = $one['aidd'];
// 	        }
// 	    } 
// 	    var_Dump($liu);die;  
// 	    foreach($liu as $two){
// 	        $bao = explode(',',$two);
// 	        array_shift($bao);
// 	        $nan = implode(',',$bao);
// 	        //var_Dump($nan);
//             $aa = $model->delete($nan);
//             var_Dump($aa);
// 	    }
// 	    die;
	    /**
	     * 更新数据库数据。
	     * 
	     */
/* 	    $seoModel = new Model('seo_words');
	    $newsModel = new Model('news_keywords');
	    echo "<pre>";
	    $seoArray = $seoModel->limit(50000,10000)->order('id asc')->select();
	    //var_dump($seoArray);die;
	    foreach($seoArray as $one){
	        $where3['keyword'] = $one['word'];
	        $selectArray = $newsModel->where($where3)->find();
	        if($selectArray){
	            echo 'dd'.'<br/>';
	        }else{
            	//var_dump($selectArray);die;
            	$data['keyword'] = $one['word']; 
            	$data['rpurl'] = $one['url'];
            	$data['industry'] = $one['industry'];
            	$data['subindustry'] = $one['subindustry'];
            	$data['mdkey'] = $one['md5word'];
            	$data['projectid'] = $one['memberlist'];
            	$data['updated'] = $one['addtime'];
            	$data['status'] = $one['status'];
            	$newsArray = $newsModel->data($data)->add();
            	var_dump($newsArray);
	        }
	    }
	     */
	}
	
    /**
     * 新闻页面
     */
    public function detail(){		
		header("Content-type: text/html; charset=utf-8");
		$redislbn = A('Common/Comment');
		$redislbn->getRedisNew();
		$model = new Model('news_keywords');
		$where3['aid'] = I('get.id');
		$urlcuo = $model->where($where3)->find();
		if(!$urlcuo){ 
		    header('HTTP/1.1 404 Not Found');
		    include('/data/www/www.liansuo.com/header_footer/error.html');die;
		}
		//将行业存入redis
		//$redislbn->redisNew->del('qa_inarrlbn');
		if($redislbn->redisNew->exists('qa_inarrlbn')){
		    $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
		}else{
			$member_ent_brandinfoModel= new Member_ent_brandinfoModel(); 
		    $inarr = $member_ent_brandinfoModel->getIndustryTotal();
		    $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
		}
		
		///取出系统的关键词
		if(!isset($_GET['keyword'])){
			$seo_words=M('news_keywords');
			$keyword=$seo_words->field('aid as id,if(`key`=\'\',`keyword`,`key`) as word,industry,subindustry')->where('aid='.intval($_GET['id']))->find(); 
			//echo $seo_words->getLastSql();
		}else{
			$keyword['word']=$_GET['keyword'];
		}		
		///根据关键词调用sphinx数据
		if(!$this->spx){//已经实例
			import("Org.Util.Sphinxapi");
			$this->spx = new \SphinxClient();       
			
		}
		$this->spx->SetServer(C('SPHINX_HOST'),C('SPHINX_PORT'));                    //设置searchd主机名和端口
		$this->spx->setLimits(0,500,500);
		$this->spx->SetFieldWeights(array('title'=>100,'keywords'=>60,'title_sub'=>50,'tag'=>40,'content'=>10));//设置字段的权重，如果area命中，那么权重算2		
		//$this->spx->SetSortMode (SPH_SORT_EXPR,'@weight+cindex*0.0001');//按照权重排序		cindex DESC,@id ASC,@weight DESC
		$this->spx->SetMatchMode(SPH_MATCH_ALL);                                //使用多字段模式
		$this->spx->SetSortMode(SPH_SORT_EXPR,'@weight');
		//$this->spx->AddQuery('3,4,5,6', 'testxml');
		$res = $this->spx->Query($keyword['word'],'allnews');//@brandname 
		//echo "<pre>";var_dump($res);die;
		$reswords = $res['words'];
		$reswordss = array();
		foreach($reswords as $key=>$one){
		    $reswordss[] = $key; 
		}
		$news_arc=M('news_arc');
		if(isset($res['matches'])){
			foreach($res['matches'] as $two){
				$allnewsid[]=$two['attrs']['id'];
			}
			$temparr=$news_arc->field('title,url,outsideurl,counthits')->where('newsid in ('.join(',',$allnewsid).')')->group('title')->select();
		}	
		if(count($temparr)<50){
		    $num = 50-count($temparr);
		    $temparr3 = $news_arc->field('title,url,outsideurl,counthits')->where('newsid>100000')->limit(1000)->select();
		    shuffle($temparr3);
		    $temparr2 = array_slice($temparr3,0,50);
		    if(empty($temparr)){
		        $temparr = $temparr2;
		    }else{
    		    $temparr = array_merge($temparr,$temparr2);
		    }
		}
		//var_dump($temparr);
		foreach($temparr as $one){
			if(!empty($one['outsideurl'])){
				$one['url']=$one['outsideurl'];
			}
		}
		$this->assign('industry',empty($keyword['subindustry'])?$keyword['industry']:$keyword['subindustry']);
		$this->assign('data1',array_splice($temparr,0,4));
		$this->assign('data2',array_splice($temparr,0,4));
		$this->assign('data3',array_splice($temparr,0,4));
		$this->assign('data4',array_splice($temparr,0,4));
		$this->assign('data5',array_splice($temparr,0,4));
		$this->assign('data5',array_splice($temparr,0,4));
		$this->assign('data6',array_splice($temparr,0,4));
		$this->assign('data7',array_splice($temparr,0,4));
		$this->assign('data8',array_splice($temparr,0,4));
		$this->assign('data9',array_splice($temparr,0,4));
		$this->assign('data10',array_splice($temparr,0,4));
		$this->assign('data11',array_splice($temparr,0,4));
		$this->assign('data12',array_splice($temparr,0,4));		
		$this->assign('keyword',$keyword);		 
		$this->assign('reswords',$reswordss);
		if(empty($keyword)){
		    $keyword['industry']=0;
		}
		//创业故事、创业播报、看谁再找项目
		$inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
		$indusArray = $inarr[$keyword['industry']]['subarr'];
		
		$this->assign('indusArray',$indusArray);
		$this->assign('inarr',$inarr);
		$id = I('get.id');
		echo $this->buildHtml($id.'.html', DOCUMENT_ROOT_DIR.'/unit/','detail');
    }
    

    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
} 