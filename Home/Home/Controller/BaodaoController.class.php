<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;


//未付费项目
class BaodaoController extends Controller {
    
    /**
     * 未付费项目首页聚合
     */
    public function index(){
		$class_industry = M("class_industry");
		
		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan','shuijinghua'";
		
		//调取导航栏大行业父类
		$industry_class = $class_industry->where('parentid = 0 AND pathname NOT IN('.$null_class.')')->field('id,industryname,pathname')->select();
		
		foreach($industry_class as $key=>$v){
			$industry_class[$key]["subclass"] = $class_industry->where('parentid = '.$v["id"].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
			
		}
		//去除掉子行业没内容的
		/*foreach($industry_class as $key=>$v){
			if(!count($industry_class[$key]["subclass"])){
				array_splice($industry_class,$key,1);
			}
		}*/
		
		
		$this->assign('industry_class',$industry_class);
        echo $this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/baodao/','index');
		//$this->display();
   }

    /**
     * 未付费的项目模板
     */
    public function nopay(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
		
        //调取redis中的项目信息
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', I('get.memberid')), true);

        $idid = $brandinfo['memberid'];
        //判断是否上传过海报
//         if($brandinfo['mancheck'] == 1 || $brandinfo['mancheck'] == 3){
//             header("Location:http://www.liansuo.com/p/$idid/",TRUE,301);die;
//         }
        
		$class_industry = M("class_industry");
		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan','shuijinghua'";
		//调取导航栏大行业父类
		$industry_class = $class_industry->where('parentid = 0 AND pathname NOT IN('.$null_class.')')->field('id,industryname,pathname')->select();
		
		foreach($industry_class as $key=>$v){
			$industry_class[$key]["subclass"] = $class_industry->where('parentid = '.$v["id"].' AND pathname NOT IN('.$null_class.') AND pathname<>""')->field('id,industryname,pathname')->select();
		}
		//去除掉子行业没内容的
		/*foreach($industry_class as $key=>$v){
			if(!count($industry_class[$key]["subclass"])){
				array_splice($industry_class,$key,1);
			}
		}*/
        if(!$brandinfo){
            header("Location:http://www.liansuo.com/error.html",true,301);die;
        }
        $allindustryTemp = json_decode($redislbn->redis->hget('classindustry', 'keyarray'), true);
        $industry = $allindustryTemp[$brandinfo['industry']];
        $subindustry = $allindustryTemp[$brandinfo['subindustry']];
        /* 将开头有空格的新闻替换掉空格 */
        $brandinfo['projectintro'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['projectintro']);
		$brandinfo['projectintro'] = preg_replace('/<p><br \/><\/p>/', '', $brandinfo['projectintro']);
		$brandinfo['pc_contents1'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents1']);
		$brandinfo['pc_contents1'] = preg_replace('/<p><br \/><\/p>/', '', $brandinfo['pc_contents1']);
		$brandinfo['pc_contents2'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents2']);
		$brandinfo['pc_contents2'] = preg_replace('/<p><br \/><\/p>/', '', $brandinfo['pc_contents2']);
		$brandinfo['pc_contents3'] = preg_replace('/<p>\s*<\/p>/', '', $brandinfo['pc_contents3']);
		$brandinfo['pc_contents3'] = preg_replace('/<p><br \/><\/p>/', '', $brandinfo['pc_contents3']);

        //去html标签适配tkd的d
        $description=strip_tags($brandinfo['projectintro']);
        $brandinfo['projectintro'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $brandinfo['projectintro']);
        $this->assign('brandinfo',$brandinfo);
		
        $this->assign('cidd',empty($brandinfo['subindustry'])?$brandinfo['industry']:$brandinfo['subindustry']);
        $this->assign('industry',$industry);
        $this->assign('subindustry',$subindustry);
        $this->assign('description',$description);
		$this->assign('industry_class',$industry_class);
        echo $this->buildHtml(I('get.memberid').'.html', DOCUMENT_ROOT_DIR.'/baodao/','nopay');
		//$this->display('Baodao/nopay');
    }
	
	function baodaolist(){
		//$redislbn = A('Common/Comment');
        //$redislbn->getRedisNew();
		$taglistModel = new Model('seo_tagindex');
		
		$null_class="'hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj','zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu','jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan','shuijinghua'";
		$industry = I('get.industry');
		$class_industry = M("class_industry");
		
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
		/*foreach($industry_class as $key=>$v){
			if(!count($industry_class[$key]["subclass"])){
				array_splice($industry_class,$key,1);
			}
		}*/
		
		
		
		//热门标签
       /*if($redislbn->redisNew->exists('tagArrayrmbq')){
            $tagArrayrmbq = json_decode($redislbn->redisNew->get('tagArrayrmbq'),true);
        }else{
            $tagArrayrmbq = $taglistModel->field('id,tag')->where("catalog_id =".$industry_pid['id'])->order('total desc')->limit(130)->select();
            shuffle($tagArrayrmbq);
            $tagArrayrmbq = array_slice($tagArrayrmbq,0,13);
            
            $redislbn->redisNew->setex('tagArrayrmbq',259200,json_encode($tagArrayrmbq));
        }
		*/
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
      	$this->assign('industry_pid',$industry_pid);
      	$this->assign('industry_name',$industry_name);
      	$this->assign('industry_class',$industry_class);
      	//$this->buildHtml($industry.'.html', DOCUMENT_ROOT_DIR.'/baodao/','baodaolist');
		$this->display();
	}
	
	/**
	 * 空方法
	 */
	public function _empty(){
	    header('HTTP/1.1 404 Not Found');
	    include('/data/www/www.liansuo.com/header_footer/error.html');die;
	}
	
	
}