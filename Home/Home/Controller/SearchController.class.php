<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Model;

class SearchController extends Controller {
    /**
     * 搜索页面(http://www.liansuo.com/so/)
     */
    public function index(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
        //将行业存入redis
        if($redislbn->redisNew->exists('qa_inarrlbn')){
            $inarr = json_decode($redislbn->redisNew->get('qa_inarrlbn'),true);
        }else{
            $inarr = $member_ent_brandinfoModel->getIndustryTotal();
            $redislbn->redisNew->setex('qa_inarrlbn',259000,json_encode($inarr));
        }
        $this->assign('inarr',$inarr);
        echo $this->buildHtml('index.html', $_SERVER['DOCUMENT_ROOT'].'/search/','index');
    }
    
    
    /**
     * 搜索首页
     */
    public function search(){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();  //实例化模型
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis();
        //url处理
        $url = $_SERVER['REQUEST_URI'];
        $pathinfo = explode('/',$url);
        $hangye = empty($pathinfo[3])?$pathinfo[2]:$pathinfo[3];
        $keyword = empty($_GET['keyword'])?'':$_GET['keyword'];
        $ad_er = $member_ent_brandinfoModel->newsad('ad_er',905,0,3);
        $moneyArray = array('y1','y2','y3','y4','y5','y6','y7');
        if(strstr($url,'y1')){$money = 'y1';$headmoney = '1万元以下';$moneyjs = '0,1';}
        if(strstr($url,'y2')){$money = 'y2';$headmoney = '1万到5万';$moneyjs = '1,2,3,4,5';}
        if(strstr($url,'y3')){$money = 'y3';$headmoney = '5万到10万';$moneyjs = '5,6,7,8,9,10';}
        if(strstr($url,'y4')){$money = 'y4';$headmoney = '10万到20万';$moneyjs = '15,16,17,18,19,20';}
        if(strstr($url,'y5')){$money = 'y5';$headmoney = '20万到50万';$moneyjs = '20,21,22,23,24,25,26';}
        if(strstr($url,'y6')){$money = 'y6';$headmoney = '50万到100万';$moneyjs = '50,51,52,53,54,56,57,58,59';}
        if(strstr($url,'y7')){$money = 'y7';$headmoney = '100万以上';$moneyjs = '100,101,102,103,104,105';}
        if(empty($money)){$hangye = empty($pathinfo[3])?$pathinfo[2]:$pathinfo[3];}else{$hangye = empty($pathinfo[4])?$pathinfo[2]:$pathinfo[3];}
        $moneyarea = array(
            array( 'provice'=>'北京','pinyin'=>'bj' ),  array( 'provice'=>'上海','pinyin'=>'sh'), array('provice'=>'广州', 'pinyin'=>'gz'),
            array('provice'=>'深圳','pinyin'=> 'sz'),  array( 'provice'=>'广东','pinyin'=> 'gd'), array( 'provice'=> '香港', 'pinyin'=>'xg'),  array('provice'=>'浙江','pinyin'=> 'zj'),
            array('provice'=>'美国', 'pinyin'=> 'mg'),  array( 'provice'=> '杭州', 'pinyin'=> 'hz'),  array( 'provice'=> '重庆', 'pinyin'=> 'cq'),  array( 'provice'=>'武汉', 'pinyin'=>'wh'), 
            array('provice'=> '山东',  'pinyin'=>'sd'), array('provice'=>  '法国','pinyin'=> 'fg'), array( 'provice'=>'意大利','pinyin'=>'ydl'), array('provice'=>'江苏','pinyin'=> 'js'), array('provice'=> '中国','pinyin'=> 'zg'),
            array( 'provice'=> '韩国','pinyin'=>'hg'), array('provice'=> '福建','pinyin'=>'fj'),  array('provice'=>  '济南','pinyin'=>'jn'), array( 'provice'=>'台湾',  'pinyin'=>'tw'),
            );
        if($areas=='bj'){
            $areasjss = '北京';
        }elseif($areas=='sh'){$areasjs = '上海';}
        elseif($areas=='gz'){$areasjs = '广州';}
        elseif($areas=='sz'){$areasjs = '深圳';}
        elseif($areas=='gd'){$areasjs = '广东';}
        elseif($areas=='xg'){$areasjs = '香港';}
        elseif($areas=='zj'){$areasjs = '浙江';}
        elseif($areas=='mg'){$areasjs = '美国';}
        elseif($areas=='hz'){$areasjs = '杭州';}
        elseif($areas=='cq'){$areasjs = '重庆';}
        
        
        //从redis中取出行业
        $industryList = json_decode($redislbn->redis->hget('classindustry','keyarray'),true);
        
        $industryDa = array();                                                    //提取大行业
        foreach($industryList as $one){
            if($one['pathname']==$hangye){
                $hangyejs = $one['industryname'];
            }
            if($one['parentid'] == 0){
                $industryDa[] = $one;
            }
        }
        if(!empty($hangye)){                                                      //提取大行业的id来做小行业的检索条件。
            $classModel = new Model('class_industry');
            $whereind['pathname'] = $hangye;
            $classArray = $classModel->field('id,parentid')->where($whereind)->find();
        }
        if($classArray['parentid'] == 0){                                         //提取小行业
            $industryXiao2 = array();
            foreach($industryList as $two){
                $industryXiao = array();
                if($two['parentid'] == $classArray['id']){
                    $industryXiao['industryname'] = $two['industryname'];
                    $industryXiao['pathname']  = $two['pathname'];
                    $industryXiao2[] = $industryXiao;
                }
            }
        }else{
            $industryXiao2 = array();
            foreach($industryList as $two){
                $industryXiao = array();
                if($two['parentid'] == $classArray['parentid']){
                    $industryXiao['industryname'] = $two['industryname'];
                    $industryXiao['pathname']  = $two['pathname'];
                    $industryXiao2[] = $industryXiao;
                }
            }
        }
        //对URL做处理
        if(!in_array($pathinfo[2],$moneyArray)){
            $areas = $pathinfo[2];
        }
        $testtest = array('jpcy','qita','ktv','tese','spjx','spjq','qcyp','csyp','ytjj','jydq','ycjy','jjjc','itjy','yezj','mtss','chxu','qcyh','dcxf','yqtl','jctm','sjjc','slbj','hwyp','zxw','buyi');
        
        if(strlen($hangye)<=4 && !in_array($hangye,$testtest)){$hangye = '';}
        $areasArray = array('ydl');
        if(strlen($areas)>=3 && !in_array($areas,$areasArray)){$areas = '';}
        
        //面包屑
        foreach($industryList as $four){
            if($four['pathname'] == $hangye && $four['pathname'] !==''){
                $headhangye = $four['industryname'];
            }
        }
        $headareas = $areass[$areas];
        ////引入sphinx类  进行sphinx查询
        import("Org.Util.Sphinxapi");
        $spx = new \SphinxClient();
        $spx->SetServer(C('SPHINX_HOST'),C('SPHINX_PORT'));                    //设置searchd主机名和端口
        $spx->SetMatchMode (SPH_MATCH_ANY);                                    //设置全文查询的匹配模式,匹配完整词
        $spx->SetArrayResult(false);
        $page = substr($_SERVER['SCRIPT_URI'],-6,-5);
        $pagesize = 15;                                                        //每页显示的条数
        $start = ($page - 1) * $pagesize > 0 ? ($page - 1) * $pagesize : 0;    //从第几条数据开始
        $spx->SetLimits($start,$pagesize,1000);                                //1000是获取整个结果集限制
        $areasjq = $areas.'@@@';
        if(empty($keyword)){
            $spx->SetSortMode( SPH_SORT_EXTENDED, "cindex DESC" );
            $key = $hangye.$moneyjs.$areasjq;
            if($key == '@@@'){
                $db = new Model('member_ent_brandinfo');
                $idss = $db->field('memberid')->order('cindex desc')->limit($start,15)->select();
            }else{
                $res = $spx->Query($key,'allproject');                              //执行搜索查询 
            }
        }else{
            $redcolor = 1;
            $res = $spx->Query($keyword,'allproject');                              //执行搜索查询
        }
        if(empty($idss)){
            $allProject = $res['matches'];
            $total_num = $res['total_found'];
            $ids = array();
            foreach($allProject as $v){
                $ids[] = $v['attrs']['id'];                                         //取出项目id
            }
        }else{
            $total_num = 1000;
            foreach($idss as $six){
                $ids[] = $six['memberid'];
            }
        }
        $brandinfo1 = $redislbn->redis->hmget('ls_member_ent_brandinfo',$ids);      //根据项目ID号取出所有项目
        //去掉所有空数据项目
        foreach($brandinfo1 as $one){
            if(!empty($one)){
                $brandinfo[] = (array)(json_decode($one));
            }
        }
        //将行业、星级、意向人数、申请加盟人数添加上
        
        foreach($brandinfo as &$three){
            $str = '';
            for($i=1;$i<=rand(4,5);$i++){$str.='★';}
            $three['str'] = $str;
        }
        if($redcolor){
            $redbrand = $brandinfo[0];
        }
        ////执行thinkphp分页操作
        $pageObj = new \Think\Page($total_num,$pagesize);                      //实例化分页类 传入总记录数和每页显示的
        $pageObj->setConfig('prev', '上一页');
        $pageObj->setConfig('next', '下一页');
        $pageObj->setConfig('last', '尾页');
        $pageObj->setConfig('first', '首页');
        $pageObj->rollPage=5;
        $page_tpl = urlencode('[PAGE]');
        if(empty($keyword)){
            $pageObj->url = $_SERVER['SCRIPT_URI'].'p'.$page_tpl.'.html';
        }else{
            $subUrl = substr($_SERVER['SCRIPT_URI'],0,-7);
            $pageObj->url = $subUrl.'p'.$page_tpl.'.html?keyword='.I('get.keyword');
        }
        $showpage = $pageObj->show();                                          //分页显示输
        $this->assign('industryDa',$industryDa);
        $this->assign('industryXiao',$industryXiao2);
        $this->assign('fenyeStr',$showpage);
        $this->assign('moneyarea', $moneyarea);
        $this->assign('headhangye',$headhangye);
        $this->assign('headmoney',$headmoney);
        $this->assign('headareas',$headareas);
        $this->assign('ad_er',$ad_er);
        $this->assign('hangye',$hangye);
        $this->assign('hangyejs',$hangyejs); 
        $this->assign('money',$money); 
        $this->assign('moneyjs',$moneyjs); 
        $this->assign('redbrand',$redbrand);
        $this->assign('brandinfo',$brandinfo);
        //echo "<pre>";var_dump($_SERVER);die;  
        $this->assign('areas',$areas);
        $this->assign('areasjs',$areasjs); 
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