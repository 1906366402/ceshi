<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class ListController extends Controller {
	/* 二级分类页标签模式(未使用) */
	 public function list2(){
		/*$redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
		*/

		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan'";
		$industry = I('get.industry');
		$class_industry = M("class_industry");
		
		//取行业id
		$industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,parentid,industryname,pathname')->find();
		
		if(empty($industry_pid["id"])){
            header("Location:http://www.liansuo.com/error.html");die;
        }
		
		//根据行业父类id调取子行业
		if($industry_pid["parentid"]){
			$industry_name = $class_industry->where('parentid = '.$industry_pid['parentid'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}else{
			$industry_name = $class_industry->where('parentid = '.$industry_pid['id'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}
		
		//dump($industry_name);
		$industry_group=array();
		foreach($industry_name as $k=>$v){
			$industry_group[$k/3][$k%3]=$v;
			if($v["id"]!=58||$v["id"]!=29){
				$industry_group[$k/3]["cid"].=$v["id"];
				if(($k+1)%3&&($k+1)!=count($industry_name)){
					$industry_group[$k/3]["cid"].=',';
				}
			}
		}
	
		//调取导航栏大行业父类
		$industry_class = $class_industry->where('parentid = 0 AND pathname NOT IN('.$null_class.')')->field('id,industryname,pathname')->select();
		
		foreach($industry_class as $key=>$v){
			$industry_class[$key]["subclass"] = $class_industry->where('parentid = '.$v["id"].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
			
		}
		
	/*	
		$industryList=json_decode($redislbn->redis->hget('classindustry','classindustry'),true);
		//去除pathname为空的分类
		$allindustry=array();
		foreach($industryList as $k=>$v){
			if($v["pathname"]!=''){
				$allindustry[]=$v;
			}
		}
		
*/


		$this->assign('industry',$industry);
		$this->assign('industry_group',$industry_group);
      	$this->assign('industry_pid',$industry_pid);
      	$this->assign('industry_name',$industry_name);
      	$this->assign('industry_class',$industry_class);
		//$this->assign('allindustry',$allindustry);
	    //$this->buildHtml('index.html', $_SERVER['DOCUMENT_ROOT']."/searchtest.liansuo.com/Web/list/".$industry."/",'index');
        $this->display('list');
    }
	
	
	/*  二级分类 */
	 public function index(){
		$redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
		$member_ent_brandinfoModel = new Member_ent_brandinfoModel();    //实例化模型

		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan'
		,'hongbei','kafei','jiushui','naicha','malatang','dangao','huwai','jjjc','zhaosheng','liuxue','ycjy','wanju','myyp','yuesao','yezj','ytsp','xhyp','etsy','txtf','leyuan','xiangshui','mfmj','yaozhuang','qudou','ganxidian','feicui','gylp','caibao','menchuang','diban','cizhuan','fangshui','suoju','muye','zhaoming','shicai','zswj','jiazheng','techan','diaoju','ktv','hunqing','shuiguodian','chongwudian','jpcy'
		,'jinrong'";
		$industry = I('get.industry');
		
		$class_industry = M("class_industry");
		$news_category=M("news_category");
		
		//取行业id
		$industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,parentid,industryname,pathname')->find();
		
		if(empty($industry_pid["id"])){
            header("Location:http://www.liansuo.com/error.html");die;
        }
		//根据行业父类id调取子行业
		if($industry_pid["parentid"]){
			$industry_pid["parent"] = $class_industry->where('id = '.$industry_pid['parentid'])->field('id,industryname,pathname')->find();
			$industry_name = $class_industry->where('parentid = '.$industry_pid['parentid'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}else{
			$industry_name = $class_industry->where('parentid = '.$industry_pid['id'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}
		
		//子类三个一组
		$industry_group=array();

		foreach($industry_name as $k=>$v){
			$industry_group[$k/3][$k%3]=$v;
			$industry_group[$k/3]["cid"].=$v["id"];
			$industry_group[$k/3]["industryname"][]=$v["industryname"];
			if(($k+1)%3&&($k+1)!=count($industry_name)){
				$industry_group[$k/3]["cid"].=',';
			}
		}

		//调取导航栏大行业父类
		$industry_class = $class_industry->where('parentid = 0 AND pathname NOT IN('.$null_class.')')->field('id,industryname,pathname')->select();
		
		foreach($industry_class as $key=>$v){
			$industry_class[$key]["subclass"] = $class_industry->where('parentid = '.$v["id"].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
			
		}
		
		/**********************************页面中涉及的项目查询开始*************************************/
		$member_ent_brandinfo = M("member_ent_brandinfo");
		//取出所有该分类下付费的项目
		$sql = "select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where  a.status=1 and b.cindex >0  and (a.industry in(".$industry_pid["id"].") or a.subindustry in(".$industry_pid["id"].")) and b.orderid>0 ORDER BY b.orderid desc limit 1000";
		$tempiarr = $member_ent_brandinfo->query($sql); 
		$tempidarr = array();
        foreach ($tempiarr as $one) {
            $tempidarr[] = $one['memberid'];
        }
		//打乱项目
		shuffle($tempidarr);
		//只取1000条数据
		if(count($tempidarr)>1000){
			$tempidarr_1000=array_slice($tempidarr,1,1000);
		}else{
			$tempidarr_1000 = $tempidarr;
		}
					
		$temparr = $redislbn->redis->hmget('ls_member_ent_brandinfo', $tempidarr_1000);
        $i = 1;
		/*
			$industry_ispay_1                  分类下付费项目
			$industry_ispay_1_img_587          分类下付费带有s_587_1图片的项目
			$industry_ispay_1_img_307          分类下付费带有s_307_1图片的项目
			$industry_ispay_1_img_logo         分类下付费带有项目logo图片的项目
			$industry_ispay_1_img_55           分类下付费带有s_55_1图片的项目
			$industry_ispay_1_img_421		   分类下付费带有s_421_1图片的项目
			
			
			$subindustr_ispay_1				   子分类付费项目
			$subindustr_ispay_1_img_587        子分类下付费带有s_587_1图片的项目
			$subindustr_ispay_1_img_307        子分类下付费带有s_307_1图片的项目
			$subindustr_ispay_1_img_logo       子分类下付费带有项目logo图片的项目
			
			
		*/
        $industry_ispay_1 = array();
		$industry_ispay_1_img_587 = array();
		$industry_ispay_1_img_307 = array();
		$industry_ispay_1_img_logo = array();
		$industry_ispay_1_img_55 = array();
		$industry_ispay_1_img_421 = array();
		
		$subindustr_ispay_1 = array();
		$subindustr_ispay_1_img_587 = array();
		$subindustr_ispay_1_img_307 = array();
		$subindustr_ispay_1_img_logo = array();
		
        foreach ($temparr as &$one) {
            $one = json_decode($one, true);
            $one['title'] = $one['brandname'];
			if(empty($one['title'])){
				 continue;
			}
            $one['oid'] = $i;
            $i++;
            $industry_ispay_1[] = $one;
			if(!empty($one["s_587_1"])){
				$industry_ispay_1_img_587[] = $one;
			}
			if(!empty($one["s_307_1"])){
				$industry_ispay_1_img_307[] = $one;
			}
			if(!empty($one["logo"])){
				$industry_ispay_1_img_logo[] = $one;
			}
			if(!empty($one["s_55_1"])){
				$industry_ispay_1_img_55[] = $one;
			}
			if(!empty($one["s_421_1"])){
				$industry_ispay_1_img_421[] = $one;
			}
			foreach($industry_group as $key=>$g){
				if(in_array($one["subindustryname"],$g["industryname"])){
					$subindustr_ispay_1[$key][] = $one;
					if(!empty($one["s_587_1"])){
						$subindustr_ispay_1_img_587[$key][] = $one;
					}
					if(!empty($one["s_307_1"])){
						$subindustr_ispay_1_img_307[$key][] = $one;
					}
					if(!empty($one["logo"])){
						$subindustr_ispay_1_img_logo[$key][] = $one;
					}
				}
			}
        }

		//取出所有该分类下非付费的项目
		$sql_f = "select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where  a.status=1 and b.cindex >0  and (a.industry in(".$industry_pid["id"].") or a.subindustry in(".$industry_pid["id"].")) and b.orderid=0 limit 1000";
		$tempiarr_f = $member_ent_brandinfo->query($sql_f); 
		$tempidarr_f = array();
        foreach ($tempiarr_f as $one) {
            $tempidarr_f[] = $one['memberid'];
        }
		//打乱项目
		shuffle($tempidarr_f);
		//只取1000条数据
		if(count($tempidarr_f)>1000){
			$tempidarr_f_1000=array_slice($tempidarr_f,1,1000);
		}else{
			$tempidarr_f_1000 = $tempidarr_f;
		}
		$temparr_f = $redislbn->redis->hmget('ls_member_ent_brandinfo', $tempidarr_f_1000);
		$i = 1;
		/*
			$industry_ispay_0                  分类下非付费项目
			$industry_ispay_0_img_587          分类下非付费带有s_587_0图片的项目
			$industry_ispay_0_img_307          分类下非付费带有s_307_0图片的项目
			$industry_ispay_0_img_logo         分类下非付费带有项目logo图片的项目
			$industry_ispay_0_img_55 		   分类下非付费带有项目s_55_0图片的项目
			$industry_ispay_0_img_421 		   分类下非付费带有项目s_421_0图片的项目
			
			$subindustr_ispay_0				   子分类非付费项目
			$subindustr_ispay_0_img_587        子分类下非付费带有s_587_0图片的项目
			$subindustr_ispay_0_img_307        子分类下非付费带有s_307_0图片的项目
			$subindustr_ispay_0_img_logo       子分类下非付费带有项目logo图片的项目
		*/
        $industry_ispay_0 = array();
		$industry_ispay_0_img_587 = array();
		$industry_ispay_0_img_307 = array();
		$industry_ispay_0_img_logo = array();
		$industry_ispay_0_img_55 = array();
		$industry_ispay_0_img_421 = array();
		
		$subindustr_ispay_0 = array();
		$subindustr_ispay_0_img_587 = array();
		$subindustr_ispay_0_img_307 = array();
		$subindustr_ispay_0_img_logo = array();
		
        foreach ($temparr_f as &$one) {
            $one = json_decode($one, true);
            $one['title'] = $one['brandname'];
			if(empty($one['title'])){
				 continue;
			}
            $one['oid'] = $i;
            $i++;
            $industry_ispay_0[] = $one;
			if(!empty($one["s_587_1"])){
				$industry_ispay_0_img_587[] = $one;
			}
			if(!empty($one["s_307_1"])){
				$industry_ispay_0_img_307[] = $one;
			}
			if(!empty($one["logo"])){
				$industry_ispay_0_img_logo[] = $one;
			}
			if(!empty($one["s_55_1"])){
				$industry_ispay_0_img_55[] = $one;
			}
			if(!empty($one["s_421_1"])){
				$industry_ispay_0_img_421[] = $one;
			}
			foreach($industry_group as $key=>$g){
				if(in_array($one["subindustryname"],$g["industryname"])){
					$subindustr_ispay_0[$key][] = $one;
					if(!empty($one["s_587_1"])){
						$subindustr_ispay_0_img_587[$key][] = $one;
					}
					if(!empty($one["s_307_1"])){
						$subindustr_ispay_0_img_307[$key][] = $one;
					}
					if(!empty($one["logo"])){
						$subindustr_ispay_0_img_logo[$key][] = $one;
					}
				}
			}
        }
		
		//付费不足的用非付费补足
		if(count($industry_ispay_1) < 18){
			$old_num = count($industry_ispay_1);
			$new_num = count($industry_ispay_0);
			if($old_num&&$new_num){
				if($new_num<=(18-$old_num)){
					$industry_ispay_1=array_merge($industry_ispay_1, $industry_ispay_0);
				}else{
					$temp_array=array_slice($industry_ispay_0,1,(18-$oldnum));
					$industry_ispay_1=array_merge($industry_ispay_1, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1=$industry_ispay_0;
			}
			
		}
		if(count($industry_ispay_1_img_587) < 8){
			$old_num = count($industry_ispay_1_img_587);
			$new_num = count($industry_ispay_0_img_587);
			if($old_num&&$new_num){
				if($new_num<=(8-$old_num)){
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $industry_ispay_0_img_587);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_587,1,(8-$oldnum));
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_587=$industry_ispay_0_img_587;
			}
		}
		
		if(count($industry_ispay_1_img_421) < 4){
			$old_num = count($industry_ispay_1_img_421);
			$new_num = count($industry_ispay_0_img_421);
			if($old_num&&$new_num){
				if($new_num<=(4-$old_num)){
					$industry_ispay_1_img_421=array_merge($industry_ispay_1_img_421, $industry_ispay_0_img_421);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_421,1,(4-$oldnum));
					$industry_ispay_1_img_421=array_merge($industry_ispay_1_img_421, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_421=$industry_ispay_0_img_421;
			}
		}

		if(count($industry_ispay_1_img_307) < 6){
			$old_num = count($industry_ispay_1_img_307);
			$new_num = count($industry_ispay_0_img_307);
			if($old_num&&$new_num){
				if($new_num<=(6-$old_num)){
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $industry_ispay_0_img_307);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_307,1,(6-$oldnum));
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_307=$industry_ispay_0_img_307;
			}
			
		}
		if(count($industry_ispay_1_img_logo) < 12){
			$old_num = count($industry_ispay_1_img_logo);
			$new_num = count($industry_ispay_0_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(12-$old_num)){
					$industry_ispay_1_img_logo=array_merge($industry_ispay_1_img_logo, $industry_ispay_0_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_logo,1,(12-$oldnum));
					$industry_ispay_1_img_logo=array_merge($industry_ispay_1_img_logo, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_logo=$industry_ispay_0_img_logo;
			}
			
		}
		if(count($industry_ispay_1_img_55) < 12){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_0_img_55);
			if($old_num&&$new_num){
				if($new_num<=(12-$old_num)){
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_0_img_55);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_55,1,(12-$oldnum));
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_55=$industry_ispay_0_img_55;
			}
			
		}
		
		//大类首屏滚动图，没有用180*180代替
		if(count($industry_ispay_1_img_55) < 2){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_1_img_587);
			if($old_num&&$new_num){
				if($new_num<=(2-$old_num)){
					foreach($industry_ispay_1_img_587 as $k=>$one){
						$industry_ispay_1_img_587[$k]["s_55_1"]=$one["s_587_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_1_img_587);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_587,1,(2-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_55_1"]=$one["s_587_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_587 as $one){
					foreach($industry_ispay_1_img_587 as $k=>$one){
						$industry_ispay_1_img_55[$k]=$one;
						$industry_ispay_1_img_55[$k]["s_55_1"]=$one["s_587_1"];
					}
				}
			}
			
		}
		//大图没有用logo来补
		if(count($industry_ispay_1_img_587) < 8){
			$old_num = count($industry_ispay_1_img_587);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(8-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_587_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $industry_ispay_1_img_logo);		
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(8-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_587_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $k=>$one){
					$industry_ispay_1_img_587[$k]=$one;
					$industry_ispay_1_img_587[$k]["s_587_1"]=$one["s_581_1"];
				}
			}
		}
		if(count($industry_ispay_1_img_307) < 6){
			$old_num = count($industry_ispay_1_img_307);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(6-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_307_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $industry_ispay_1_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(6-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_307_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $one){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_307[$k]=$one;
						$industry_ispay_1_img_307[$k]["s_307_1"]=$one["s_581_1"];
					}
				}
			}
			
		}
		if(count($industry_ispay_1_img_55) < 2){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(2-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_55_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_1_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(2-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_55_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $one){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_55[$k]=$one;
						$industry_ispay_1_img_55[$k]["s_55_1"]=$one["s_581_1"];
					}
				}
			}
			
		}
		
		
		foreach($industry_group as $key=>$g){
			if(count($subindustr_ispay_1[$key]) < 10){
				$old_num = count($subindustr_ispay_1[$key]);
				$new_num = count($subindustr_ispay_0[$key]);
				if($old_num&&$new_num){
					if($new_num<=(10-$old_num)){
						$subindustr_ispay_1[$key]=array_merge($subindustr_ispay_1[$key], $subindustr_ispay_0[$key]);
					}else{
						$temp_array=array_slice($subindustr_ispay_0[$key],1,(10-$oldnum));
					$subindustr_ispay_1[$key]=array_merge($subindustr_ispay_1[$key], $temp_array);
					}
				}elseif($new_num){
					$subindustr_ispay_1[$key]=$subindustr_ispay_0[$key];
				}
			}
			if(count($subindustr_ispay_1_img_587[$key]) < 8){
				$old_num = count($subindustr_ispay_1_img_587[$key]);
				$new_num = count($subindustr_ispay_0_img_587[$key]);
				if($old_num&&$new_num){
					if($new_num<=(8-$old_num)){
						$subindustr_ispay_1_img_587[$key]=array_merge($subindustr_ispay_1_img_587[$key], $subindustr_ispay_0_img_587[$key]);
					}else{
						$temp_array=array_slice($subindustr_ispay_0_img_587[$key],1,(8-$oldnum));
					$subindustr_ispay_1_img_587[$key]=array_merge($subindustr_ispay_1_img_587[$key], $temp_array);
					}
				}elseif($new_num){
					$subindustr_ispay_1_img_587[$key]=$subindustr_ispay_0_img_587[$key];
				}
			}
			if(count($subindustr_ispay_1_img_307[$key]) < 2){
				$old_num = count($subindustr_ispay_1_img_307[$key]);
				$new_num = count($subindustr_ispay_0_img_307[$key]);
				if($old_num&&$new_num){
					if($new_num<=(2-$old_num)){
						$subindustr_ispay_1_img_307[$key]=array_merge($subindustr_ispay_1_img_307[$key], $subindustr_ispay_0_img_307[$key]);
					}else{
						$temp_array=array_slice($subindustr_ispay_0_img_307[$key],1,(2-$oldnum));
					$subindustr_ispay_1_img_307[$key]=array_merge($subindustr_ispay_1_img_307[$key], $temp_array);
					}
				}elseif($new_num){
					$subindustr_ispay_1_img_307[$key]=$subindustr_ispay_0_img_307[$key];
				}
			}
			if(count($subindustr_ispay_1_img_logo[$key]) < 9){
				$old_num = count($subindustr_ispay_1_img_logo[$key]);
				$new_num = count($subindustr_ispay_0_img_logo[$key]);
				if($old_num&&$new_num){
					if($new_num<=(9-$old_num)){
						$subindustr_ispay_1_img_logo[$key]=array_merge($subindustr_ispay_1_img_logo[$key], $subindustr_ispay_0_img_logo[$key]);
					}else{
						$temp_array=array_slice($subindustr_ispay_0_img_logo[$key],1,(9-$oldnum));
					$subindustr_ispay_1_img_logo[$key]=array_merge($subindustr_ispay_1_img_logo[$key], $temp_array);
					}
				}elseif($new_num){
					$subindustr_ispay_1_img_logo[$key]=$subindustr_ispay_0_img_logo[$key];
				}
			}
			
				//大图没有用logo来补
			if(count($subindustr_ispay_1_img_587[$key]) < 8){
				$old_num = count($subindustr_ispay_1_img_587[$key]);
				$new_num = count($subindustr_ispay_1_img_logo[$key]);
				if($old_num&&$new_num){
					if($new_num<=(8-$old_num)){
						foreach($subindustr_ispay_1_img_logo[$key] as $k=>$one){
							$subindustr_ispay_1_img_logo[$key][$k]["s_587_1"]=$one["s_581_1"];
						}
						$subindustr_ispay_1_img_587[$key] = array_merge($subindustr_ispay_1_img_587[$key], $subindustr_ispay_1_img_logo[$key]);		
					}else{
						$temp_array=array_slice($subindustr_ispay_1_img_logo[$key],1,(8-$oldnum));
						foreach($temp_array as $k=>$one){
							$temp_array[$k]["s_587_1"]=$one["s_581_1"];
						}
						$subindustr_ispay_1_img_587[$key] = array_merge($subindustr_ispay_1_img_587[$key], $temp_array);
					}
				}elseif($new_num){
					foreach($subindustr_ispay_1_img_logo[$key] as $k=>$one){
						$subindustr_ispay_1_img_587[$key][$k]=$one;
						$subindustr_ispay_1_img_587[$key][$k]["s_587_1"]=$one["s_581_1"];
					}
				}
			}
			if(count($subindustr_ispay_1_img_307[$key]) < 6){
				$old_num = count($subindustr_ispay_1_img_307[$key]);
				$new_num = count($subindustr_ispay_1_img_logo[$key]);
				if($old_num&&$new_num){
					if($new_num<=(6-$old_num)){
						foreach($subindustr_ispay_1_img_logo[$key] as $k=>$one){
							$subindustr_ispay_1_img_logo[$key][$k]["s_307_1"]=$one["s_581_1"];
						}
						$subindustr_ispay_1_img_307[$key] = array_merge($subindustr_ispay_1_img_307[$key], $subindustr_ispay_1_img_logo[$key]);
					}else{
						$temp_array=array_slice($subindustr_ispay_1_img_logo[$key],1,(6-$oldnum));
						foreach($temp_array as $k=>$one){
							$temp_array[$k]["s_307_1"]=$one["s_581_1"];
						}
						$subindustr_ispay_1_img_307[$key]=array_merge($subindustr_ispay_1_img_307[$key], $temp_array);
					}
				}elseif($new_num){
					foreach($subindustr_ispay_1_img_logo[$key] as $one){
						foreach($subindustr_ispay_1_img_logo[$key] as $k=>$one){
							$subindustr_ispay_1_img_307[$key][$k]=$one;
							$subindustr_ispay_1_img_307[$key][$k]["s_307_1"]=$one["s_581_1"];
						}
					}
				}
				
			}
			
		}

		/**********************************页面中涉及的项目查询结束*************************************/
		/**********************************调用分类表里的tdk开始****************************************/
			$temppid = $industry_pid["id"];
			 $temparri = json_decode($redislbn->redis->hget('category_lib','category'), true);		 
			 $temparria=array();			 
			 foreach($temparri as $keyi=>$abc){
				 if($industry_pid["parentid"]){
					if(($abc['s_industry']==$temppid)&&!empty($abc["categorydir"])){
						$temparria=$keyi;
					}
				 }else{
					if(($abc['b_industry']==$temppid&&$abc['s_industry']==0)&&!empty($abc["categorydir"])){
						$temparria=$keyi;
					}
				 }
			 }		 
			 $tdk='';
			 if($temparria){
				$tdk = $news_category->where("id=".$temparria)->find();
			 }	
		 /**********************************调用分类表里的tdk结束****************************************/
		
		
		$news_ztbd = $member_ent_brandinfoModel->news_source('news_ztbd',829,4,$source="{pic:213*134}");
		
		$this->assign('industry',$industry);
		$this->assign('industry_ispay_1',$industry_ispay_1);
		$this->assign('industry_ispay_1_img_587',$industry_ispay_1_img_587);
		$this->assign('industry_ispay_1_img_307',$industry_ispay_1_img_307);
		$this->assign('industry_ispay_1_img_logo',$industry_ispay_1_img_logo);
		$this->assign('industry_ispay_1_img_55',$industry_ispay_1_img_55);
		$this->assign('industry_ispay_1_img_421',$industry_ispay_1_img_421);
		
		$this->assign('industry_group',$industry_group);
		$this->assign('subindustr_ispay_1',$subindustr_ispay_1);
		$this->assign('subindustr_ispay_1_img_587',$subindustr_ispay_1_img_587);
		$this->assign('subindustr_ispay_1_img_307',$subindustr_ispay_1_img_307);
		$this->assign('subindustr_ispay_1_img_logo',$subindustr_ispay_1_img_logo);
		
		$this->assign('tdk',$tdk); 
		$this->assign('news_ztbd',$news_ztbd);
      	$this->assign('industry_pid',$industry_pid);
      	$this->assign('industry_name',$industry_name);
      	$this->assign('industry_class',$industry_class);
	    $this->buildHtml('index.html', DOCUMENT_ROOT_DIR."/list/".$industry."/",'index');
        $this->display('list2');
    }
	/*
		三级页面
	*/
	public function list3(){
		$industry = I('get.industry');
		$class_industry = M("class_industry");
		$redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
		
		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan'
		,'hongbei','kafei','jiushui','naicha','malatang','dangao','huwai','jjjc','zhaosheng','liuxue','ycjy','wanju','myyp','yuesao','yezj','ytsp','xhyp','etsy','txtf','leyuan','xiangshui','mfmj','yaozhuang','qudou','ganxidian','feicui','gylp','caibao','menchuang','diban','cizhuan','fangshui','suoju','muye','zhaoming','shicai','zswj','jiazheng','techan','diaoju','ktv','hunqing','shuiguodian','chongwudian','jpcy'
		,'jinrong'";
		
		//取行业id
		$industry_pid = $class_industry->where(array('pathname' =>$industry))->field('id,parentid,industryname,pathname')->find();
		
		//根据行业父类id调取子行业
		if($industry_pid["parentid"]){
			$industry_pid["parent"] = $class_industry->where('id = '.$industry_pid['parentid'])->field('id,industryname,pathname')->find();
			$industry_name = $class_industry->where('parentid = '.$industry_pid['parentid'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}else{
			$industry_name = $class_industry->where('parentid = '.$industry_pid['id'].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}
		
		/**********************************页面中涉及的项目查询开始*************************************/
		$member_ent_brandinfo = M("member_ent_brandinfo");
		//取出所有该分类下付费的项目
		$sql = "select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where  a.status=1 and b.cindex >0  and (a.industry in(".$industry_pid["id"].") or a.subindustry in(".$industry_pid["id"].")) and b.orderid>0 ORDER BY b.orderid desc limit 1000";
		$tempiarr = $member_ent_brandinfo->query($sql); 
		$tempidarr = array();
        foreach ($tempiarr as $one) {
            $tempidarr[] = $one['memberid'];
        }
		//打乱项目
		shuffle($tempidarr);
		//只取1000条数据
		if(count($tempidarr)>1000){
			$tempidarr_1000=array_slice($tempidarr,1,1000);
		}else{
			$tempidarr_1000 = $tempidarr;
		}
					
		$temparr = $redislbn->redis->hmget('ls_member_ent_brandinfo', $tempidarr_1000);
        $i = 1;
		/*
			$industry_ispay_1                  分类下付费项目
			$industry_ispay_1_img_587          分类下付费带有s_587_1图片的项目
			$industry_ispay_1_img_307          分类下付费带有s_307_1图片的项目
			$industry_ispay_1_img_logo         分类下付费带有项目logo图片的项目
			$industry_ispay_1_img_55           分类下付费带有s_55_1图片的项目
			$industry_ispay_1_img_421		   分类下付费带有s_421_1图片的项目
			
			
			
		*/
        $industry_ispay_1 = array();
		$industry_ispay_1_img_587 = array();
		$industry_ispay_1_img_307 = array();
		$industry_ispay_1_img_logo = array();
		$industry_ispay_1_img_55 = array();
		$industry_ispay_1_img_421 = array();
	
        foreach ($temparr as &$one) {
            $one = json_decode($one, true);
            $one['title'] = $one['brandname'];
			if(empty($one['title'])){
				 continue;
			}
            $one['oid'] = $i;
            $i++;
            $industry_ispay_1[] = $one;
			if(!empty($one["s_587_1"])){
				$industry_ispay_1_img_587[] = $one;
			}
			if(!empty($one["s_307_1"])){
				$industry_ispay_1_img_307[] = $one;
			}
			if(!empty($one["logo"])){
				$industry_ispay_1_img_logo[] = $one;
			}
			if(!empty($one["s_55_1"])){
				$industry_ispay_1_img_55[] = $one;
			}
			if(!empty($one["s_421_1"])){
				$industry_ispay_1_img_421[] = $one;
			}
        }

		//取出所有该分类下非付费的项目
		$sql_f = "select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where  a.status=1 and b.cindex >0  and (a.industry in(".$industry_pid["id"].") or a.subindustry in(".$industry_pid["id"].")) and b.orderid=0 limit 1000";
		$tempiarr_f = $member_ent_brandinfo->query($sql_f); 
		$tempidarr_f = array();
        foreach ($tempiarr_f as $one) {
            $tempidarr_f[] = $one['memberid'];
        }
		//打乱项目
		shuffle($tempidarr_f);
		//只取1000条数据
		if(count($tempidarr_f)>1000){
			$tempidarr_f_1000=array_slice($tempidarr_f,1,1000);
		}else{
			$tempidarr_f_1000 = $tempidarr_f;
		}
		$temparr_f = $redislbn->redis->hmget('ls_member_ent_brandinfo', $tempidarr_f_1000);
		$i = 1;
		/*
			$industry_ispay_0                  分类下非付费项目
			$industry_ispay_0_img_587          分类下非付费带有s_587_0图片的项目
			$industry_ispay_0_img_307          分类下非付费带有s_307_0图片的项目
			$industry_ispay_0_img_logo         分类下非付费带有项目logo图片的项目
			$industry_ispay_0_img_55 		   分类下非付费带有项目s_55_0图片的项目
			$industry_ispay_0_img_421 		   分类下非付费带有项目s_421_0图片的项目
			
		*/
        $industry_ispay_0 = array();
		$industry_ispay_0_img_587 = array();
		$industry_ispay_0_img_307 = array();
		$industry_ispay_0_img_logo = array();
		$industry_ispay_0_img_55 = array();
		$industry_ispay_0_img_421 = array();
		
		
        foreach ($temparr_f as &$one) {
            $one = json_decode($one, true);
            $one['title'] = $one['brandname'];
			if(empty($one['title'])){
				 continue;
			}
            $one['oid'] = $i;
            $i++;
            $industry_ispay_0[] = $one;
			if(!empty($one["s_587_1"])){
				$industry_ispay_0_img_587[] = $one;
			}
			if(!empty($one["s_307_1"])){
				$industry_ispay_0_img_307[] = $one;
			}
			if(!empty($one["logo"])){
				$industry_ispay_0_img_logo[] = $one;
			}
			if(!empty($one["s_55_1"])){
				$industry_ispay_0_img_55[] = $one;
			}
			if(!empty($one["s_421_1"])){
				$industry_ispay_0_img_421[] = $one;
			}
        }
		
		//付费不足的用非付费补足
		if(count($industry_ispay_1) < 18){
			$old_num = count($industry_ispay_1);
			$new_num = count($industry_ispay_0);
			if($old_num&&$new_num){
				if($new_num<=(18-$old_num)){
					$industry_ispay_1=array_merge($industry_ispay_1, $industry_ispay_0);
				}else{
					$temp_array=array_slice($industry_ispay_0,1,(18-$oldnum));
					$industry_ispay_1=array_merge($industry_ispay_1, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1=$industry_ispay_0;
			}
			
		}
		if(count($industry_ispay_1_img_587) < 8){
			$old_num = count($industry_ispay_1_img_587);
			$new_num = count($industry_ispay_0_img_587);
			if($old_num&&$new_num){
				if($new_num<=(8-$old_num)){
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $industry_ispay_0_img_587);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_587,1,(8-$oldnum));
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_587=$industry_ispay_0_img_587;
			}
		}
		
		if(count($industry_ispay_1_img_421) < 4){
			$old_num = count($industry_ispay_1_img_421);
			$new_num = count($industry_ispay_0_img_421);
			if($old_num&&$new_num){
				if($new_num<=(4-$old_num)){
					$industry_ispay_1_img_421=array_merge($industry_ispay_1_img_421, $industry_ispay_0_img_421);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_421,1,(4-$oldnum));
					$industry_ispay_1_img_421=array_merge($industry_ispay_1_img_421, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_421=$industry_ispay_0_img_421;
			}
		}

		if(count($industry_ispay_1_img_307) < 6){
			$old_num = count($industry_ispay_1_img_307);
			$new_num = count($industry_ispay_0_img_307);
			if($old_num&&$new_num){
				if($new_num<=(6-$old_num)){
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $industry_ispay_0_img_307);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_307,1,(6-$oldnum));
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_307=$industry_ispay_0_img_307;
			}
			
		}
		if(count($industry_ispay_1_img_logo) < 12){
			$old_num = count($industry_ispay_1_img_logo);
			$new_num = count($industry_ispay_0_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(12-$old_num)){
					$industry_ispay_1_img_logo=array_merge($industry_ispay_1_img_logo, $industry_ispay_0_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_logo,1,(12-$oldnum));
					$industry_ispay_1_img_logo=array_merge($industry_ispay_1_img_logo, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_logo=$industry_ispay_0_img_logo;
			}
			
		}
		if(count($industry_ispay_1_img_55) < 12){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_0_img_55);
			if($old_num&&$new_num){
				if($new_num<=(12-$old_num)){
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_0_img_55);
				}else{
					$temp_array=array_slice($industry_ispay_0_img_55,1,(12-$oldnum));
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				$industry_ispay_1_img_55=$industry_ispay_0_img_55;
			}
			
		}
		
		//大类首屏滚动图，没有用180*180代替
		if(count($industry_ispay_1_img_55) < 2){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_1_img_587);
			if($old_num&&$new_num){
				if($new_num<=(2-$old_num)){
					foreach($industry_ispay_1_img_587 as $k=>$one){
						$industry_ispay_1_img_587[$k]["s_55_1"]=$one["s_587_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_1_img_587);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_587,1,(2-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_55_1"]=$one["s_587_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_587 as $one){
					foreach($industry_ispay_1_img_587 as $k=>$one){
						$industry_ispay_1_img_55[$k]=$one;
						$industry_ispay_1_img_55[$k]["s_55_1"]=$one["s_587_1"];
					}
				}
			}
			
		}
		//大图没有用logo来补
		if(count($industry_ispay_1_img_587) < 8){
			$old_num = count($industry_ispay_1_img_587);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(8-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_587_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $industry_ispay_1_img_logo);		
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(8-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_587_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_587=array_merge($industry_ispay_1_img_587, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $k=>$one){
					$industry_ispay_1_img_587[$k]=$one;
					$industry_ispay_1_img_587[$k]["s_587_1"]=$one["s_581_1"];
				}
			}
		}
		if(count($industry_ispay_1_img_307) < 6){
			$old_num = count($industry_ispay_1_img_307);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(6-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_307_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $industry_ispay_1_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(6-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_307_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_307=array_merge($industry_ispay_1_img_307, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $one){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_307[$k]=$one;
						$industry_ispay_1_img_307[$k]["s_307_1"]=$one["s_581_1"];
					}
				}
			}
			
		}
		if(count($industry_ispay_1_img_55) < 2){
			$old_num = count($industry_ispay_1_img_55);
			$new_num = count($industry_ispay_1_img_logo);
			if($old_num&&$new_num){
				if($new_num<=(2-$old_num)){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_logo[$k]["s_55_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $industry_ispay_1_img_logo);
				}else{
					$temp_array=array_slice($industry_ispay_1_img_logo,1,(2-$oldnum));
					foreach($temp_array as $k=>$one){
						$temp_array[$k]["s_55_1"]=$one["s_581_1"];
					}
					$industry_ispay_1_img_55=array_merge($industry_ispay_1_img_55, $temp_array);
				}
			}elseif($new_num){
				foreach($industry_ispay_1_img_logo as $one){
					foreach($industry_ispay_1_img_logo as $k=>$one){
						$industry_ispay_1_img_55[$k]=$one;
						$industry_ispay_1_img_55[$k]["s_55_1"]=$one["s_581_1"];
					}
				}
			}
			
		}
		
		/**********************************页面中涉及的项目查询结束*************************************/
		
		$this->assign('industry_ispay_1',$industry_ispay_1);
		$this->assign('industry_ispay_1_img_587',$industry_ispay_1_img_587);
		$this->assign('industry_ispay_1_img_307',$industry_ispay_1_img_307);
		$this->assign('industry_ispay_1_img_logo',$industry_ispay_1_img_logo);
		$this->assign('industry_ispay_1_img_55',$industry_ispay_1_img_55);
		$this->assign('industry_ispay_1_img_421',$industry_ispay_1_img_421);
		
		$this->assign('industry_name',$industry_name);
		$this->assign('industry_pid',$industry_pid);
		$this->assign('industry',$industry);
		$this->display();
	}
	
    /**
     * 列表页面
     */
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
     
    //项目资讯
    public function xmzx(){
        $deflag = 'l,q'; 
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
        if($deCid<17 && $deCid!==14){
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

        //$redislbn->redisNew->del($industry.'_'.$pageUrl);
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
        if($industry=='news'){
            $where2['total'] = array('gt',20);
        }else{
            $where2['catalog_id'] = $cid;
        }
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
        $pagehou = explode('/',I('server.REQUEST_URI'));
        $show = $pageObj->show();// 分页显示输出
        //模板输出
        $this->assign('list_top',$list_top);
        $this->assign('connPage',$getPage); 
        $this->assign('pagehou',$pagehou[3]);
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
        
        $this->buildHtml($industry.'.html', DOCUMENT_ROOT_DIR."/list/$pageUrl/",$pageUrl);
        $this->assign('page',$show);
    }
} 















