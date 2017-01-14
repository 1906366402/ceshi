<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class TopController extends Controller {
    
    

    /**
     * 空方法lbn
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
    
    /**
     * 排行榜首页
     */
    public function index(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //取出行业
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        
       $this->assign('inarr',$inarr);
        
        $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/top/','index');
        $this->display();
    }

    public function zixun(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //取出行业
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        //dump($inarr);die;
         $this->assign('inarr',$inarr);
          $this->buildHtml('zixun.html', DOCUMENT_ROOT_DIR.'/top/','zixun');
        $this->display();
    }

     public function all(){
         $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //取出行业
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        
       $this->assign('inarr',$inarr);
       $this->buildHtml('all.html', DOCUMENT_ROOT_DIR.'/top/','all');
        $this->display();
    }

     public function city(){
		$member_ent_brandinfo = M("member_ent_brandinfo");
		$sql="select count(1) as total,b.cradle as provice,p.entitle as provice_py from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid left join ls_class_province as p on p.cntitle=b.cradle where a.status=1 and b.cindex >0 AND b.cradle<>'' AND p.upid<>0 group by cradle order by provice_py";
		$arealist = $member_ent_brandinfo->query($sql);
		
		$sql="select count(1) as total,b.cradle as provice,p.entitle as provice_py from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid left join ls_class_province as p on p.cntitle=b.cradle where a.status=1 and b.cindex >0 AND b.cradle<>'' AND p.upid<>0 group by cradle order by total desc limit 20";
		$hot_arealist = $member_ent_brandinfo->query($sql);
		$this->assign('hot_arealist',$hot_arealist);
		$this->assign('arealist',$arealist);
		$this->buildHtml('city.html', DOCUMENT_ROOT_DIR.'/top/','city');
        $this->display();
    }

     public function money(){
		    $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
		    //取出行业
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        
       $this->assign('inarr',$inarr);
	    $this->buildHtml('money.html', DOCUMENT_ROOT_DIR.'/top/','money');
        $this->display();
    }
    
    public function xiangqing(){	
		$redislbn = A('Common/Comment');
        $redislbn->getRedis();
		$redislbn->getRedisNew();
		$class_industry = M("class_industry");
		$taglistModel = new Model('seo_tagindex');
		
		
		//取出行业
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $member_ent_brandinfoModel= new Member_ent_brandinfoModel();
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
		
		$tag1 = strtolower(I('get.tag1'));
		$tag2 = strtolower(I('get.tag2'));
		$tag3 = strtolower(I('get.tag3'));
		$getPage = I('get.page')?I('get.page'):1;
		if($tag1){
			if(strpos($tag1,'-')!==false){//判断是不是金额
					$money =$tag1;
			}else{
				//取行业id
				$industry_pid = $class_industry->where(array('pathname' =>$tag1))->field('id,parentid,industryname,pathname')->find();
				if($industry_pid){//判断是不是行业
					$industry = $tag1;
				}else{				
					$addr=$tag1; //都不是就默认是地区
				}
			}
		}
		if($tag2){
			if(strpos($tag2,'-')!==false){
					$money =$tag2;
			}else if(empty($industry_pid)){//之前匹配到行业的话不继续匹配

					$industry_pid = $class_industry->where(array('pathname' =>$tag2))->field('id,parentid,industryname,pathname')->find();
					if($industry_pid){
						$industry = $tag2;
					}else{				
						$addr=$tag2;
					}
			}else{
				$addr=$tag2;
			}
		}
		if($tag3){
			if(strpos($tag3,'-')!==false){
					$money =$tag3;
			}else if(empty($industry_pid)){//之前匹配到行业的话不继续匹配
					$industry_pid = $class_industry->where(array('pathname' =>$tag3))->field('id,parentid,industryname,pathname')->find();
					if($industry_pid){
						$industry = $tag3;
					}else{				
						$addr=$tag3;
					}
			}else{
				$addr=$tag3;
			}
		}
		
		if($industry){
			if($industry_pid){
				//根据行业父类id调取子行业
				if($industry_pid["parentid"]){
					$industry_pid["parent"] = $class_industry->where('id = '.$industry_pid['parentid'])->field('id,industryname,pathname')->find();
					$industry_name = $class_industry->where('parentid = '.$industry_pid['parentid'].' AND pathname<>""')->field('id,industryname,pathname')->select();
				}else{
					$industry_name = $class_industry->where('parentid = '.$industry_pid['id'].' AND pathname<>""')->field('id,industryname,pathname')->select();
				}
			}
		}
		
		
		$member_ent_brandinfo = M("member_ent_brandinfo");
		$where='a.status=1 and b.cindex >0';
		if($industry_pid){
		 $where .= ' and (a.industry in(' . $industry_pid['id'] . ') or a.subindustry in(' . $industry_pid['id'] . '))';
		}
		
		if(isset($money)){
			if(strpos($money,'-')!==false){//投资额度为横线
				$moneyarr=explode('-',$money);
				$where.=' and b.joinlinemin <'.$moneyarr[1].' and b.joinlinemax>'.$moneyarr[0];
			}
		}
	

//select count(1) as total,b.cradle as provice from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where cradle<>'' AND a.status=1 and b.cindex >0 group by cradle order by total desc
		//获取城市
		$sql="select count(1) as total,b.cradle as provice,p.entitle as provice_py from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid left join ls_class_province as p on p.cntitle=b.cradle where ".$where." AND b.cradle<>'' AND p.upid<>0 group by cradle order by total desc";
		$tempiarr = $member_ent_brandinfo->query($sql);
		$arealist = array();
		$provice = '';
		foreach($tempiarr as $v){
			if(strcasecmp($addr,$v['provice_py'])==0){
				array_unshift ( $arealist, $v );
				$provice = $v["provice"];
			}else{
				$arealist [] = $v;
			}
		}
		
		if(!$provice){
			$addr='';
		}
		
		$sql="select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where ".$where;
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
		$p_url = '/top';
		if($industry){
			$p_url .='/'.$industry;
		}
		if($money){
			$p_url .='/'.$money;
		}
		if($addr){
			$p_url .='/'.$addr;
		}
		$pageObj->url = $p_url.'/p'.$page_tpl.'.html';;
		$show = $pageObj->show();// 分页显示输出
		
		//热门标签
		if($industry_pid){
			if($industry_pid["parentid"]==0){
				$tagArrayrmbq = $taglistModel->field('id,tag')->where("catalog_id =".$industry_pid['id'])->order('total desc')->limit(130)->select();
			}else{
				$industry_pid["parent"] = $class_industry->where('id = '.$industry_pid['parentid'])->field('id,industryname,pathname')->find();
				$tagArrayrmbq = $taglistModel->field('id,tag')->where("catalog_id =".$industry_pid["parentid"])->order('total desc')->limit(130)->select();
			}
		}else{
			$tagArrayrmbq = $taglistModel->field('id,tag')->order('total desc')->limit(130)->select();
		}
        shuffle($tagArrayrmbq);
        $tagArrayrmbq = array_slice($tagArrayrmbq,0,13);
            
		
		$this->assign('tagArrayrmbq',$tagArrayrmbq);

		$this->assign('arealist',$arealist);
		$this->assign('addr',$addr);
		$this->assign('provice',$provice);
		$this->assign('money',$money);
		$this->assign('industry_name',$industry_name);
		$this->assign('industry_pid',$industry_pid);
		$this->assign('industry',$industry);
		$this->assign('p_row',$pagenum.",".$pagesize);
		$this->assign('page',$show);
		$this->assign('inarr',$inarr);
		
	
		echo $this->buildHtml($industry.'_'.$money.'_'.$addr.'_'.$getPage.'.html', DOCUMENT_ROOT_DIR.'/top/xiangqing/','xiangqing');
		
        //$this->display();
    }
    
}