<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class NewsController extends Controller {
    
    /**
     * 新闻页面
     */
    public function news(){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();  //实例化模型
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
        $id = I('get.newsid');
        
        
        $news = json_decode($redislbn->redisNew->get('news_'.I('get.newsid')),true);
        if(!$redislbn->redisNew->exists('news_'.I('get.newsid'))){
            header("Location:http://www.liansuo.com/error.html");die;
        }
		///获取news的栏目
		$tempcatstr=$redislbn->redis->hget('category_lib','category');
		$tempcaarr=json_decode($tempcatstr,true);
		if(isset($tempcaarr[$news['catalog_id']])){
			 $this->assign('category',$tempcaarr[$news['catalog_id']]);
		}
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', $news['member_id']), true);
     
        if(empty($brandinfo) && !empty($news['member_id'])){
            $brandMember = 'jiangzi';
        }
        if(empty($news['member_id'])){
            $merge = $member_ent_brandinfoModel->getcatalogid($news['catalog_id']);
            $industry = $merge['industry'];
            $subindustry = $merge['subindustry'];
            $industryhasub = $subindustry['s_industry'];
            $industryha = $industry['s_industry'];

           
            
        }else{
            $allindustryTemp = json_decode($redislbn->redis->hget('classindustry', 'keyarray'), true);
            $industry = $allindustryTemp[$brandinfo['industry']];
            $subindustry = $allindustryTemp[$brandinfo['subindustry']];
            $industryhasub = empty($news['member_id'])?0:$brandinfo['subindustry'];
            $industryha = empty($news['member_id'])?0:$brandinfo['industry'];
            if(empty($subindustry)){
                $subindustry = $industry;
                $industry = array();
            }
        }
        
        
        
        
        $emptyHy = array('hanbao','fushi','ganxishebei','piyiyanghu','zuyu','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu',
            'jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','xianglaxia','canju','spjq','kaoya','baozi','pisa','chuanchuanxiang','zhongcan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan');
        	
        if(in_array($subindustry['pathname'],$emptyHy)){
            $subindustry['pathname'] = '';
        }
        
        $deflag = explode(',',$news['deflag']);
        
       
        
        if($news['deflag']=='e'){
            $newsDeflag['name'] = '加盟指南';
            $newsDeflag['ping'] = 'jmzn';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif($news['deflag']=='g'){
            $newsDeflag['name'] = '选址筹备';
            $newsDeflag['ping'] = 'xzcb';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif($news['deflag']=='n'){
            $newsDeflag['name'] = '经典案例';
            $newsDeflag['ping'] = 'jdal';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif($news['deflag']=='m'){
            $newsDeflag['name'] = '人物专访';
            $newsDeflag['ping'] = 'rwzf';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif($news['deflag']=='x'){
            $newsDeflag['name'] = '参展信息';
            $newsDeflag['ping'] = 'czxx';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif(in_array('i',$deflag) || in_array('k',$deflag) || in_array('o',$deflag)){
            $newsDeflag['name'] = '日常经营';
            $newsDeflag['ping'] = 'rcjy';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif(in_array('i',$deflag) || in_array('q',$deflag)){
            $newsDeflag['name'] = '项目资讯';
            $newsDeflag['ping'] = 'xmzx';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }elseif(in_array('q',$deflag)||in_array('u',$deflag)){
            $newsDeflag['name'] = '行业动态';
            $newsDeflag['ping'] = 'hydt';
            $newsDeflag['pathname'] = $subindustry['pathname'];
        }
        
        
        $hyurl = "/p".$brandinfo['memberid']."/";
        $xwurl = "/p".$brandinfo['memberid']."/ppxw/";
        $allindustryTemp = json_decode($redislbn->redis->hget('classindustry', 'keyarray'), true);
        $news_ztbd = $member_ent_brandinfoModel->news_source('news_ztbd',759,3,$source="{pic:240*64}");       //专题报道
        $news_lsrw = $member_ent_brandinfoModel->news_source('news_lsrw',745,4,$source='{pic1:115*105}');     //连锁人物
        $news_xgyd = $member_ent_brandinfoModel->tjyd($news['keywords'],$news['newsid'],$news['catalog_id']); //相关阅读
        $news_tjyd = $member_ent_brandinfoModel->news_name('news_tjyd',$news['catalog_id'],0,100,$news['member_id']);            //推荐阅读
        $news_rdjj = $member_ent_brandinfoModel->news_name('news_rdjj',$news['catalog_id'],0,150,$news['member_id']);            //热点聚焦
        $news_sszx = $member_ent_brandinfoModel->news_name('news_sszx',$news['catalog_id'],0,200,$news['member_id']);            //实时资讯
        $ad_yi = $member_ent_brandinfoModel->newsad('ad_yi',1045,0,3);                                        //热点聚焦下方广告
        $ad_er = $member_ent_brandinfoModel->newsad('ad_er',905,0,3);                                         //排行榜下方广告
        $news_top = $member_ent_brandinfoModel->phb($industryhasub,10);                                       //排行榜
        $foot_ppdh = $member_ent_brandinfoModel->phb($industryha,44);                                         //连锁品牌导航
        $strtotag = $member_ent_brandinfoModel->strtotag($news['newsid']);                                    //相关阅读出tag处理
        
        /* 将开头有空格的新闻替换掉空格 */
		$news['content'] = preg_replace('/<p>\s*<\/p>/', '', $news['content']);
		$news['content'] ='<style>.j_news_text p{text-indent:2em;}</style>'.preg_replace('/>　　([\S])/i','>$1', $news['content']);	
		$exurl = explode('/',$_SERVER['SCRIPT_URL']);
        if(stripos($news['url'],$_SERVER['HTTP_HOST'])===false){
            if(stripos("http://news.liansuo.com/news",$_SERVER['HTTP_HOST'])){
                header("Location:http://www.liansuo.com/news/$exurl[2]");
            }else{
                header("Location:http://www.liansuo.com/error.html");
                exit();
            }
        }
        
	    $this->assign('news',$news);
        $this->assign('hyurl',$hyurl);
        $this->assign('xwurl',$xwurl);
        $this->assign('brandMember',$brandMember);
        $this->assign('brandinfo',$brandinfo);
        $this->assign('news_top',$news_top);
        $this->assign('foot_ppdh',$foot_ppdh);
        $this->assign('news_xgyd',$news_xgyd);
        $this->assign('news_tjyd',$news_tjyd); 
        $this->assign('news_ztbd',$news_ztbd);
        $this->assign('news_lsrw',$news_lsrw);
        $this->assign('news_rdjj',$news_rdjj);
        $this->assign('news_sszx',$news_sszx);
        $this->assign('strtotag',$strtotag);
        $this->assign('ad_yi',$ad_yi);
        $this->assign('ad_er',$ad_er);
        $this->assign('newsDeflag',$newsDeflag);
        
        //echo "<pre>";var_dump($subindustry);die;
        $this->assign('industry',$industry);
        $this->assign('subindustry',$subindustry);
        $this->display();
    }
} 