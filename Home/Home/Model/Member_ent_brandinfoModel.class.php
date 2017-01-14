<?php
namespace Home\Model;
use Think\Model;
class Member_ent_brandinfoModel extends Model{
      protected $tableName = '';
 // 如果没有在配置文件中设置表前缀，就应该设置为protected $trueTableName = 'crm28_client';
    //private $Redis = NULL;
	private $redislbn=NULL;
	private $repeat = array();
    public function getPublic()
    {
        $redislbn = A('Common/Comment'); // 实例化Common模块下的Comment控制器
        $redislbn->getRedis();
        $redislbn->getRedisNew(); // 调取redis方法
        $a = $redislbn->redisNew->keys('*');
        return $a;
    }

    /**
     * 标签使用的方法
     */
    function getProjectList($tagp, $content)
    {
        $tag = json_decode($tagp, true); // /所有的参数
		$this->redislbn = A('Common/Comment');
		if (!$this->redislbn->redis) { // /判断redis实例，防止多次实例
			$this->redislbn->getRedis();
        }
		if(!$this->redislbn->redisNew){			
			$this->redislbn->getRedisNew();
		}
        $where = ' a.status=1 and b.cindex >0 AND a.industry IN (SELECT id FROM `ls_class_industry` WHERE `pathname`<>"" AND `industryname`<>"其他") AND a.subindustry IN (SELECT id FROM `ls_class_industry` WHERE `pathname`<>"" AND `industryname`<>"其他")';
        // /缓存读取
        $lckey = 'ls_plist_cache_' . md5($tagp);
        if (isset($tag['cache']) && ! isset($tag['stop'])) {
            if (strpos($tag['cache'], ',') !== false) {
                $cache = explode(',', $tag['cache']); // 要缓存起来的字段，循环体里面用到的
            } else {
                echo '缓存需要设置你需要的字段';
                die();
            }           
            if ($this->redislbn->redisNew->exists($lckey)) { // 判断缓存是否存在
                $tempstr = $this->redislbn->redisNew->get($lckey);
                $temparr = json_decode($tempstr, true);				
                return $temparr;
            }
        }
        // /排行榜
        if (isset($tag['mounth'])) {
            $tempidarr = $this->setPaihang($tag['cid'], $tag['mounth']);
        }
        // /行业id不限制大行业和小行业
        if (isset($tag['cid'])) {
            $where .= ' and (a.industry in(' . $tag['cid'] . ') or a.subindustry in(' . $tag['cid'] . '))';
        }
		///投在条件
		if(isset($tag['money'])&&!empty($tag['money'])){
			if(strpos($tag['money'],'-')!==false){//投资额度为横线
				$moneyarr=explode('-',$tag['money']);
				$where.=' and b.joinlinemin <'.$moneyarr[1].' and b.joinlinemax>'.$moneyarr[0];
			}else{
				echo '投资额度错误';
			}
		}
		
		//所属地区
		if(isset($tag['address'])&&!empty($tag['address'])){
			if ($tag['address'] == 'qita'|| $tag['address'] == '其它') {
				$where .= " and b.cradle = ''";
				$tag['address'] = '其它';
			} else {
				$where .= " and b.cradle like '%".$tag['address']."%'";
			}
		}
        // /显示的数量
        $row = 10;
        if (isset($tag['row'])) {
            $row = $tag['row'];
        }
        // order 排序
        if(isset($tag['order'])){
            $order = $tag['order'];
        }else{
            $order = " orderid desc,cindex desc ";
        }
        
        if(isset($tag['upload'])){
            $where .=" and a.isupload=0 ";
        }
        
        //调取付费项目 
        if (isset($tag['ispay'])) {
            if($tag['ispay'] == 1){
                $where .=" and b.orderid>0 ";
            }else{
                $where .=" and b.orderid=0 ";
            }
        }
		
		   // 不允许出现重复项目
        if (isset($tag['norepeat'])) {
            if (count($this->repeat) > 0) {               
                $where .= ' and b.memberid not in(' . join(',', $this->repeat) . ') ';
                //echo $where;die;
            }
            
        }
		
		//查出的项目随机展示（测试..）
		/*if (isset($tag['rand'])) {
			//查出范围创建随机id
			$tmpsql="select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where $where";
			$randid = $this->query($tmpsql);
			 if (strpos($row, ',') == false) {
				 $rownum=$row;
            }else{
				$row_num=explode(",",$row);
				$rownum=$row_num[1];
			}
			if($rownum<count($randid)){
				$new_randid = array_slice($randid,0,count($randid)-$rownum);
			}
			shuffle($new_randid);
			$randid=$new_randid[0]['memberid']?$new_randid[0]['memberid']:0;
			
			$where .='AND b.memberid>='. $randid;
		}*/
		
        // /排行榜的项目新闻id
        if (! isset($tempidarr)) {
			$sql="select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where $where order by $order limit " . $row;
			$tempiarr = $this->query($sql); 
            $tempidarr = array();
            foreach ($tempiarr as $one) {
                $tempidarr[] = $one['memberid'];
            }
        }
        

		
		if(isset($tag['stop'])){
			echo $sql;die;
		}
        $temparr = $this->redislbn->redis->hmget('ls_member_ent_brandinfo', $tempidarr);
        $i = 1;
        $resultarr = array();
        foreach ($temparr as &$one) {
		
            $one = json_decode($one, true);
            $one['title'] = $one['brandname'];
			if(empty($one['title'])){
				 continue;
			}
            $one['oid'] = $i;
            $i ++;
            if (isset($cache)) { // 判断需要缓存的数据
                $cache[] = 'news_id';
                $cache[] = 'oid';
                $cache[] = 'url';
                $two = array();
                foreach ($one as $key => $val) {
                    if (in_array($key, $cache)) {
                        $two[$key] = $val;
                    }
                }
                $one = $two;
            }
			if (isset($tag['norepeat'])) {
				$this->repeat[] = $one['memberid']; // 记录重复id

			}
			if (!isset($tag['ispay'])) {
				$one["ypurl"]=str_replace("baodao","p",$one["ypurl"]);
			}
            $resultarr[] = $one;
        }
        // /数据缓存
        if (isset($tag['cache'])) {
            $cachetime = 86400; // 默认缓存一天
            if (isset($tag['cachetime'])) {
                $cachetime = intval($tag['cachetime']);
                $cachetime = $tag['cachetime'] < 86400 * 7 ? $cachetime : 86400 * 7;
            }
            $this->redislbn->redisNew->set($lckey, json_encode($resultarr));
            $this->redislbn->redisNew->expire($lckey, $cachetime);
        }
        return $resultarr;
    }

    /**
     * 标签使用的方法
     */
    function setPaihang($industry, $mounth = '')
    {
        $news_phb = M('news_phb');
        $mounth = empty($mounth) ? date('Y-m') : $mounth;
        $temparr = $news_phb->where('industry=' . $industry . ' and yandm=\'' . $mounth . '\'')
            ->order('id desc')
            ->limit(1)
            ->find();
        if (! $temparr) { // /没有读取
            $tempiarr = $news_phb->query("select b.memberid from ls_member_ent_brandinfo as b left join ls_member_ent_info as a on a.memberid=b.memberid where b.cindex>0 and a.industry=$industry or subindustry=$industry order by orderid desc,cindex desc limit 10");
            // echo $news_phb->getLastSQL();die;
            $allid = array();
            foreach ($tempiarr as $one) {
                $allid[] = $one['memberid'];
            }
            $allidstr = join(',', $allid);
            $news_phb->data(array(
                'industry' => $industry,
                'yandm' => $mounth,
                'allxm' => $allidstr
            ))->add();
            // echo $news_phb->getLastSQL();die;
        } else {
            // print_r($temparr);die;
            $allid = explode(',', $temparr['allxm']);
        }
        return $allid;
    }
    //品牌新闻重写
    function ppxwcx($memberid,$start,$pgnum){
        $redislbn  = A('Common/Comment');
        $redislbn->getRedisNew();
        if($redislbn->redisNew->exists($memberid)){
            $newsNum = json_decode($redislbn->redisNew->get($memberid));
            $ppxwArray1 = $redislbn->redisNew->getMultiple(array_slice($newsNum,$start,$pgnum));
            $ppxwArray = array();
            foreach($ppxwArray1 as $one){
                $ppxwArray[] = json_decode($one,true);
            }
        }else{
            $newsModel = new Model();
            $ppxwArrayID1=$newsModel->query("select newsid from ls_news_arc where  member_id=" . $memberid . " and iscreatehtml in (1,2) and flag in(1,2)   order by newsid desc");
            $ppxwArrayID = array();
            foreach($ppxwArrayID1 as $one){
                $ppxwArrayID[] = 'news_'.$one['newsid'];
            }
            $redislbn->redisNew->setex($memberid,86400,json_encode($ppxwArrayID));
            $ppxwArray1 = $redislbn->redisNew->getMultiple(array_slice($ppxwArrayID,$start,$pgnum));
            $ppxwArray = array();
            foreach($ppxwArray1 as $one){
                $ppxwArray[] = json_decode($one,true);
            }
            $newsArc = implode('',$ppxwArray);
            if(empty($newsArc)){
                $arcArray = $newsModel->query("select * from ls_news_arc where  member_id=" . $memberid . " and iscreatehtml in (1,2) and flag in(1,2) order by newsid desc");
                foreach($arcArray as $one){
                    $redislbn->redisNew->setex('news_'.$one['newsid'],86400,json_encode($one));
                }
                $ppxwArray1 = $redislbn->redisNew->getMultiple(array_slice($ppxwArrayID,$start,$pgnum));
                $ppxwArray = array();
                foreach($ppxwArray1 as $one){
                    $ppxwArray[] = json_decode($one,true);
                }
            }
        }
        return $ppxwArray;
    }

    //根据小行业取出项目精品推荐
    function jptj($industry=0,$length=10){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        if($redislbn->redisNew->exists('jptjlbn_'.$industry)){
            $jptjArray = json_decode($redislbn->redisNew->get('jptjlbn_'.$industry),true);
        }else{
            $model = new Model();
            $jptjArray = $model->query("select a.memberid,a.cindex,a.brandname,a.projectbrief,a.joinlinemin,a.joinlinemax,b.memberid,b.logo,b.isupload,b.industry,b.subindustry
                from ls_member_ent_brandinfo as a,ls_member_ent_info as b where
                a.memberid = b.memberid and b.industry=$industry order by a.cindex desc limit 0,50");
            $redislbn->redisNew->setex('jptjlbn_'.$industry,86400,json_encode($jptjArray));
        }
        return $jptjArray;
    }
    
    //新闻页面相关阅读
    function tjyd($string,$newsid,$catalog_id){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //$redislbn->redisNew->del('newstjyd_'.$newsid);
        if(!$redislbn->redisNew->exists('newstjyd_'.$newsid)){
            $model = new Model('news_arc');
            if(!empty($string)){
                $string=str_replace('，', ',', $string);
                $string=str_replace(' ', ',', $string);
                $temparr=explode(',', $string);
                $string=array();
                $where['keywords'] =array('like','%'.$temparr[0].'%');
                $where['iscreatehtml'] = 1;
                $where['flag'] = array('neq',0);
                $where['newsid'] = array('neq',591059);
                $arcArray = $model->field('newsid')->where($where)->order('newsid desc')->limit(8)->select();
                foreach($arcArray as $one){
                    $array[] = $one['newsid'];
                }
                shuffle($array);
            }
            if(count($array)<8){
                if(!empty($temparr[1])){
                    $where2['keywords'] =array('like','%'.$temparr[1].'%');
                    $where2['iscreatehtml'] = 1;
                    $where2['flag'] = array('neq',0);
                    $arcArray = $model->field('newsid')->where($where2)->order('newsid desc')->limit(8)->select();
                    //echo $model->getLastSql();
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
            }
            $redislbn->redisNew->setex('newstjyd_'.$newsid,86400,json_encode($array));
            $arrayNews = array();
            foreach($array as $one){
                $arrayNews[] = 'news_'.$one;
            }
            //var_Dump($arrayNews);
            $tjydArray2 = $redislbn->redisNew->getMultiple($arrayNews);
            $tjydArray = array();
            foreach($tjydArray2 as $one){
                $tjydArray[] = json_decode($one,true);
            }
            //var_Dump($tjydArray);die;
        }else{
            $array = json_decode($redislbn->redisNew->get('newstjyd_'.$newsid),true);
            $arrayNews = array();
            foreach($array as $one){
                $arrayNews[] = 'news_'.$one;
            }
            $tjydArray2 = $redislbn->redisNew->getMultiple($arrayNews);
            $tjydArray = array();
            foreach($tjydArray2 as $one){
                $tjydArray[] = json_decode($one,true);
            }
        }
        return $tjydArray;
        
    }
    
    //news碎片
    function news_name($newname,$catalog_id,$start,$length,$source,$member_id){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $redislbn->getRedis(); 
        //$redislbn->redisNew->del($newname.'_'.$catalog_id); 
        if($redislbn->redisNew->exists($newname.'_'.$catalog_id)){
            $newsid = json_decode($redislbn->redisNew->get($newname.'_'.$catalog_id),true);
            $news_name1 = $redislbn->redisNew->getMultiple($newsid);
            $news_name = array();
            foreach($news_name1 as $two){
                $news_name[] = json_decode($two,true);
            }
            shuffle($news_name);
        }else{
            $model = new Model();
            $newsModel1 = $model->query("select newsid,member_id from ls_news_arc where catalog_id=$catalog_id and flag!=0 and iscreatehtml!=0
            order by ctime desc limit $start,$length");
            $newsModel = array();
            foreach($newsModel1 as $one){
                if($redislbn->redis->hexists('ls_member_ent_brandinfo', $one['member_id'])===false && $one['member_id'] !=='0'){
                }else{
                    $newsModel[] = 'news_'.$one['newsid'];
                }
            }
            $redislbn->redisNew->setex($newname.'_'.$catalog_id,86400,json_encode($newsModel));
            $news_name1 = $redislbn->redisNew->getMultiple($newsModel);
            $news_name = array();
            foreach($news_name1 as $two){
                $news_name[] = json_decode($two,true);
            }
            shuffle($news_name);
        }
        
        return $news_name;
    }
    
    //调取资源系统广告图
    function news_source($newname,$catalog_id,$length,$source){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //$redislbn->redisNew->del($newname.'_'.$catalog_id);
        if($redislbn->redisNew->exists($newname.'_'.$catalog_id)){
			//echo $newname.'_'.$catalog_id;
            $news_name = json_decode($redislbn->redisNew->get($newname.'_'.$catalog_id),true);
        }else{
            $source = str_replace ( '{', '', $source );
            $source = str_replace ( '}', '', $source );
            $temparrone = explode ( ':', $source );
            $size = $temparrone[1];
            $model = new Model();
            $sourceArray = $model->query("SELECT a.*,b.* from `ls_source_type` as a,`ls_source_content` as b WHERE source_size='$size' and a.id=b.typeid and b.source_project=1 order by b.id desc limit 0,$length");
            //echo $model->getLastSql(); 
            $newsModel = array();
            foreach($sourceArray as $one){
                $newsModel[] = 'news_'.$one['newsid'];
            }
            $news_name1 = $redislbn->redisNew->getMultiple($newsModel);
            $news_name = array();
            foreach($news_name1 as $two){
                $news_name[] = json_decode($two,true);
            }
            $news_name2 = array();
            foreach($news_name as $key=>&$three){
                $three['source_content'] = $sourceArray[$key]['source_content'];
            }
            $redislbn->redisNew->setex($newname.'_'.$catalog_id,86400,json_encode($news_name));
        }
	     //echo "<pre>";var_dump($news_name);
        return $news_name;
    }
    

    //调取广告
    function newsad($newsadname,$channelID,$start,$length){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        //$redislbn->redisNew->del($newsadname.'_'.$channelID);
        if($redislbn->redisNew->exists($newsadname.'_'.$channelID)){
            $newsModel = json_decode($redislbn->redisNew->get($newsadname.'_'.$channelID),true);
        }else{
            $model = new Model();
            $newsModel = $model->query("SELECT * FROM `ls_ad_banner` WHERE channelID=$channelID order by id desc limit $start,20");
            shuffle($newsModel);
            $newsModel = array_slice($newsModel,$strat,$length);
            $redislbn->redisNew->setex($newsadname.'_'.$channelID,259200,json_encode($newsModel));
        }
        return $newsModel;
    }
    

    //取出加盟聚焦
    function jmjj($memberid){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        if($redislbn->redisNew->exists('jmjjlbn_'.$memberid)){
            $jmjjArray = json_decode($redislbn->redisNew->get('jmjjlbn_'.$memberid),true);
        }else{
            $newsarcModel = new Model('news_arc');
            $where2['member_id'] = $memberid;
            $jmjjArray = $newsarcModel->where($where2)->order('ctime desc')->limit(10)->select();
            $redislbn->redisNew->setex('jmjjlbn_'.$memberid,86400,json_encode($jmjjArray));
        }
        return $jmjjArray;
    }
    

    //取出排行榜
    function phb($industry=0,$length=10){
        $top = A('Common/Comment');
        $top->getRedisNew();
       // var_dump($top->redisNew->del('industryTop_'.$industry));
        if($top->redisNew->exists('industryTop_'.$industry)){
            $phb = $top->redisNew->get('industryTop_'.$industry);
        }else{
            $top->industryTop($industry,$length);
            $phb = $top->redisNew->get('industryTop_'.$industry);
        }
        return json_decode($phb,true);
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
    
    
    //面包屑处理
    function getcatalogid($cataid){
        
//         if(empty($news['member_id'])){
//             $industry['industryname'] = '展会';
//             $industry['pathname'] = 'http://zhanhui.liansuo.com/';
//             $subindustry['industryname'] = '会展新闻';
//             $subindustry['pathname'] = 'http://www.liansuo.com/zhanhui-448-0-0-0-1.html';
//         }
        
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $categoryLib = json_decode($redislbn->redis->hget('category_lib','category'),true);
        $cate2 = $categoryLib[$cataid];
        if(!empty($cate2['parentid'])){
            $cate1 = $categoryLib[$cate2['parentid']];
            $industry['industryname'] = $cate1['categoryname'];
            $industry['pathname'] = $cate1['categorydir'];
            $industry['s_industry'] = $cate1['s_industry'];
        }else{
           // $industry['s_industry'] = $cataid;
        }
        $subindustry['industryname'] = $cate2['categoryname'];
        $subindustry['pathname'] = $cate2['categorydir'];
        $subindustry['s_industry'] = $cate2['b_industry'];
        $merge['industry'] = $industry; 
        $merge['subindustry'] = $subindustry;
        return $merge;
    }
    
    /**
     * 问答页面获取行业
     */
    function getIndustryTotal(){
        $inarr = array ();
        $industryModel = new Model('class_industry');
        $where['parentid'] = 0;
        $where['pathname'] = array('exp'," <> '' ");
        $inids = $industryModel->field('id')->where($where)->select();
        foreach ($inids as $i){
            $arr = array ();
            $pindusinfo = $this->getIndustryInfo ( $i['id'],$classpath );
            $subinfo = $this->getAnyindustry ( $i['id'],$classpath );
            $arr ['prid'] = $pindusinfo ['id'];
            $arr ['prindustryname'] = $pindusinfo ['industryname'];
            $arr ['prpathname'] = $pindusinfo ['pathname'];
            $arr['classpath'] = $pindusinfo['classpath'];
            $arr ['subarr'] = $subinfo;
            $inarr [$i['id']] = $arr;
        }
        return $inarr;
    }
    
    function getIndustryInfo($inid = null,$patharr = null) {
        $arr = array();
        $industryModel = new Model('class_industry');
        $where2['id'] = $inid;
        $where2['pathname'] = array('exp'," <> '' "); 
        $arr = $industryModel->where($where2)->find();
        if ($patharr){
            $arr['classpath'] = $patharr[$aInfo['id']];
        }
        return $arr;
    }
    
    function getAnyindustry($parentid = null, $patharr = null) { 
        $emptyHy = array('hanbao','fushi','ganxishebei','piyiyanghu','index','wjpj','jixiejin','gongjujin','rywj', 'zuyu','wangluoshangcheng','hanzheng','chxu','bojin','jingpinzhubao','huangjin','zuanshi','yiguei','jydq','quanjinghua','yinxiang','daohang','cheshi','cheme','qixiu','ytjj','chongwu','yaodian','yanjiu','slbj','shuma','jienengdeng','huanweiyongpin','kongqi','shuijinghua','jydq','quanjinghua','jineng','yuqi','kaoyu','youyongguan','shouzhuabing','canju','jksp','mixian','zhapaigu','zhajipai','lingshi','kuaicanche','xiannai','lamian','luwei','chunjingshui','zhoupu',
            'jianbing','mianbaodian','xiangguo','shushi','liangpi','fendian','hbyp','jianzhujin','lvshi','anfang','jiuba','chengren','chaoshi','xianglaxia','jixie','maozi','meifa','xiche','jieyouqi','jctm','jiexie','sjjc','canju','spjq','tiaoweipin','sqjx','kaoya','baozi','pisa','ganguo','chuanchuanxiang','zhoncan','twszb','shouzhuabing','qita','xietaoji','xiezi','hxycjm','hxycjm','shuma','dianyingyuan','bdbnxxw','jiedai','licai','shuijinghua','ljclq','meijia','mtss','dcxf','zuche','jieyouq','daohang','qcyp','qcyh','chemei','louti','cainuan','fangchan','tangguo','duorou','yanjing','yuleshebei','zhenggu','dianyingyuan');
        $arr = array ();
        $industryModel = new Model('class_industry');
        $where3['parentid'] = $parentid;
        $where3['pathname'] = array('exp'," <> '' ");
        $industrys = $industryModel->field('id,industryname,pathname')->where($where3)->select();
        if ($patharr) {
            foreach ( $industrys as $one ) {
                if(!in_array($one['pathname'],$emptyHy)){
                    $onearr = array ();
                    $onearr = $one;
                    $onearr ['classpath'] = $patharr [$one ['id']];
                    $arr [] = $onearr;
                }
            } 
        }else {
            foreach($industrys as $one){
                if(!in_array($one['pathname'],$emptyHy)){
                    $onearr = array ();
                    $onearr = $one;
                    $onearr ['classpath'] = $patharr [$one ['id']];
                    $arr [] = $onearr;
                }
            }
            //$arr = $industrys;
        }
        return $arr;
    }
    
    
    /**
     * 根据栏目（行业）id得到相关专题
     * Enter description here ...
     * @param unknown_type $categoryid
     */
    function getSeotaglist($categoryid = NULL,$num = 5) {
        $seotagModel = new Model();
        $where = "1";
        if ($categoryid){
            $where.=" and categoryid=".$categoryid;
        }
        $seolist = $seotagModel->query("select kid,title,url from ls_seo_tag_kws where " . $where . " order by ctime desc limit 0,".$num);
        return $seolist;
    }
    
}
?>