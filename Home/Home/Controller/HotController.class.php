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
class HotController extends Controller
{
	function index(){
		//调取导航栏大行业父类
		$class_industry = M("class_industry");
		$industry_class = $class_industry->where(array('parentid' =>0))->field('id,industryname,pathname')->select();
		$this->assign('industry_class',$industry_class);
		$this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/hot/','index');		
		
		$this->display('Hot/index');
	}
	function detail(){
		//调取行业相关的新闻
		/*$news_search = M("news_search");
		$aid = I('get.id');
		$pid = $news_search->where(array('aid' =>$aid))->field('subindustry')->find();*/
		header("Content-type: text/html; charset=utf-8");
		$redislbn = A('Common/Comment');
		$redislbn->getRedisNew();
		//$model = new Model('news_keywords');
		$model = new Model('news_search');
		$where3['aid'] =I('get.id');
		$urlcuo = $model->where($where3)->find();
		//dump($urlcuo);//die;
		if(!$urlcuo){
		    header("Location:http://www.liansuo.com/error.html");die;
		}
		
		
		if($redislbn->redisNew->exists('qa_inarrlbn')){
		    $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
		}else{
			$member_ent_brandinfoModel= new Member_ent_brandinfoModel(); 
		    $inarr = $member_ent_brandinfoModel->getIndustryTotal();
		    $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
		}
		
		///取出系统的关键词
		if(!isset($_GET['keyword'])){
			$seo_words=M('news_search');
			$keyword=$seo_words->field('aid as id,if(`keyword`=\'\',`key`,`keyword`) as word,industry,subindustry')->where('aid='.intval($_GET['id']))->find(); 
			//echo $seo_words->getLastSql();
		}else{
			$keyword['word']=$_GET['keyword'];
		}


		/*if(!isset($_GET['keyword'])){
			$ls_news_search=M('news_search');
			$keyword=$ls_news_search->field('aid as id,if(`key`=\'\',`keyword`,`key`) as word,industry,subindustry')->where('industry<>0 and subindustry<>0 and aid='.intval($_GET['id']))->find(); 
			//echo $seo_words->getLastSql();
		}*/
		
		///根据关键词调用sphinx数据
		if(!$this->spx){//已经实例
			import("Org.Util.Sphinxapi");
			$this->spx = new \SphinxClient();       
			
		}
;
		$this->spx->SetServer(C('SPHINX_HOST'),C('SPHINX_PORT'));                    //设置searchd主机名和端口
		$this->spx->setLimits(0,500,500);
		$this->spx->SetFieldWeights(array('title'=>100,'keywords'=>60,'title_sub'=>50,'tag'=>40,'content'=>10));//设置字段的权重，如果area命中，那么权重算2		
		$this->spx->SetSelect('*');


		//$this->spx->SetSortMode (SPH_SORT_EXPR,'@weight+cindex*0.0001');//按照权重排序		cindex DESC,@id ASC,@weight DESC
		//$this->spx->SetMatchMode(SPH_MATCH_ALL);                                //使用多字段模式
		$this->spx->SetMatchMode (SPH_MATCH_PHRASE);
		$this->spx->SetRankingMode (SPH_MATCH_ALL);
		$this->spx->SetSortMode(SPH_SORT_EXTENDED,'@id DESC'); 
		//$this->spx->SetSortMode(SPH_SORT_EXPR,'@weight');
		//$this->spx->AddQuery('3,4,5,6', 'testxml');
		
		$res = $this->spx->Query($keyword['word'],'allnews');//@brandname
		//$res = $this->spx->Query($keyword['word'],'*');		

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
			if(count($allnewsid)<25){
				array_pop($reswordss);
				foreach($reswordss as $v){
					$new_word.=$v;
				}
				$res = $this->spx->Query($new_word,'allnews');
				foreach($res['matches'] as $two){
					$allnewsid[]=$two['attrs']['id'];
				}
			}
			shuffle($$allnewsid);

			$temparr = $news_arc->field('title,url,editor,description,outsideurl,counthits,ctime,picture')->where('newsid in ('.join(',',$allnewsid).') AND iscreatehtml > 0')->group('title')->select();
			$temparr_img = array();
			$n=0;
			foreach($temparr as $v){
				if($n++>8){
					break;
				}
				if($v["picture"]<>''){
					array_push($temparr_img,$v);
				}
			}
			//$temparr_img=$news_arc->field('title,url,picture')->where('newsid in ('.join(',',$allnewsid).') AND `picture` <>"" AND iscreatehtml > 0')->group('title')->limit(8)->select();
		}	

		if(count($temparr)<20){
		    $num = 20-count($temparr);
		    $temparr3 = $news_arc->field('title,url,editor,description,outsideurl,counthits')->where('newsid>100000 AND iscreatehtml > 0')->limit($num)->select();
		    shuffle($temparr3);
		    $temparr2 = array_slice($temparr3,0,50);
		    if(empty($temparr)){
		        $temparr = $temparr2;
		    }else{
    		    $temparr = array_merge($temparr,$temparr2);
		    }
		}
		
		if(count($temparr_img)<8){
		    $num = 8-count($temparr_img);
		    $temparr3 = $news_arc->field('title,url,picture')->where('newsid>100000 AND `picture` <>"" AND iscreatehtml > 0')->limit($num)->select();
		    shuffle($temparr3);
		    $temparr2 = array_slice($temparr3,0,50);
		    if(empty($temparr_img)){
		        $temparr_img = $temparr2;
		    }else{
    		    $temparr_img = array_merge($temparr_img,$temparr2);
		    }
		}
	//	dump($temparr_img);



		if(!$keyword){
			 header("Location:http://www.liansuo.com/error.html");
			exit();
		}
		$this->assign('temparr',$temparr);
		$this->assign('temparr_img',$temparr_img);
		
		$this->assign('industry',$keyword['industry']);
		$this->assign('subindustry',$keyword['subindustry']);
		$this->assign('id',intval($_GET['id']));
		$this->assign('kw',$keyword);
		$this->assign('pid',$pid);

		//print_r($keyword);die;
		echo $this->buildHtml($_GET['id'].'.html', DOCUMENT_ROOT_DIR.'/hot/','detail');
		//$this->display('Hot/detail');
	}
	
	function hotlist(){
		$industry = I('get.industry');
		$class_industry = M("class_industry");
		//取行业id
		$industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,industryname')->find();
		//根据行业父类id调取子行业
		$industry_name = $class_industry->where(array('parentid' =>$industry_pid['id']))->field('id,industryname,pathname')->select();
		//删除子行业为“其他”的元素/pathname值为空
		foreach ($industry_name as $key => $value) {
			if ($value['pathname']==null) {
				unset($industry_name[$key]);
			}
			
		}
		//调取导航栏大行业父类
		$industry_class = $class_industry->where(array('parentid' =>0))->field('id,industryname,pathname')->select();
		
		/*$redislbn = A('Common/Comment');
      	$redislbn->getRedisNew();
      	//将行业存入redis
      	if($redislbn->redisNew->exists('qa_inarrlbn')){
      	    $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
      	}else{
      	    $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
      	    $inarr = $member_ent_brandinfoModel->getIndustryTotal();
      	    $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
      	}*/
      		//dump($industry_pid);die;
      		//$this->assign('inarr',$inarr);
      		$this->assign('industry',$industry);
      		$this->assign('industry_pid',$industry_pid);
      		$this->assign('industry_name',$industry_name);
      		$this->assign('industry_class',$industry_class);
      	  $this->buildHtml($industry.'.html', DOCUMENT_ROOT_DIR.'/hot/','hotlist');
			$this->display();
	}

	/**
	 * 空方法lbn
	 */
	public function _empty(){
	    header('HTTP/1.1 404 Not Found');
	    include('/data/www/www.liansuo.com/header_footer/error.html');die;
	}

}
?> 