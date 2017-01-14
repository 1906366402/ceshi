<?php
namespace Home\Model;

use Think\Model;

class News_arcModel extends Model
{

    protected $tableName = '';
    // 如果没有在配置文件中设置表前缀，就应该设置为protected $trueTableName = 'crm28_client';
    private $allenewsid = array();
    // 已经存在
    private $pidarr = NULL;

    private $arcResit = NULL;

    private $repeat = array();
	private $source_content=NULL;
	private $news_deflag=NULL;
	private $seo_tagindex=NULL;
	private $resources_news=NULL;
	
    // 记录重复的id 单词调用不允许重复
    public function getArcList($pararr)
    {
        if (! $this->$arcResit) { // /判断redis实例，防止多次实例
            $this->arcResit = A('Common/Comment');
        }
        $tag = json_decode($pararr, true); // 获取参数
                                           // /判断是否使用缓存
        $lckey = 'ls_news_list_cache_' . md5($pararr);
        if (isset($tag['cache']) && ! isset($tag['stop'])) {
            if (strpos($tag['cache'], ',') !== false) {
                $cache = explode(',', $tag['cache']); // 要缓存起来的字段，循环体里面用到的
            } else {
                echo '缓存需要设置你需要的字段';
                die();
            }
            $this->arcResit->getRedisNew(); // /获取120的redis实例
            if ($this->arcResit->redisNew->exists($lckey)) { // 判断缓存是否存在
                $tempstr = $this->arcResit->redisNew->get($lckey);
                $temparr = json_decode($tempstr, true);
                // 判断需要缓存的数据
                foreach ($temparr as $one) {
                    $this->repeat[] = $one['newsid'];
                }
                return $temparr;
            }
        }
        // print_r($tag);die;
        $where = ' status=1 and (iscreatehtml=1 or j=1)'; // /新闻是生成状态的，跳转新闻可以为不生成的状态
                                                          // /栏目cid读取
        if (isset($tag['cid'])) {
            if (strpos($tag['cid'], ',') === false) {
                $tempstrc = ' cid=' . intval($tag['cid']);
            } else {
                $tempstrc = ' cid in(' . $tag['cid'] . ')';
            }
            $where .= ' and ' . $tempstrc;
            if (isset($tag['source'])) { // 资源只取栏目
                $sourcewhere .= ' and ' . $tempstrc;
            }
        }
        
        // /如果读取项目新闻展示项目id
        if (isset($tag['brandinfo'])) {
            $where .= ' and memberid<> 0 ';
        }
        // /不包含项目新闻
        if (isset($tag['nomid'])) {
            $where .= '  and memberid =0';
        }
        // /获取项目项目的最新新闻 mid=0 所有的且不唯一 mid=1 所有项目的且唯一 mid >0 取这个项目的
        $field = 'newsid,memberid';
        $group = '';
        if (isset($tag['mid'])) {
            if ($tag['mid'] == '1') {
                $field = 'max(newsid),memberid';
                $group = 'memberid';
            } else {
                $where .= '  and memberid =' . intval($tag['mid']);
            }
        }
        // /栏目pid 读取大行业下面的小行业 大行业也可以兼容 ','
        if (isset($tag['pid'])) {
            if (! $this->pidarr) {
                $this->arcResit->getRedis(); // /获取栏目数组不在读取数据库
                $this->pidarr = json_decode($this->arcResit->redis->hget('category_lib', 'parid_arr'), true);
            }
            if (strpos($tag['pid'], ',') !== false) {
                $temppid = explode(',', $tag['pid']);
                $temppidarr = array();
                foreach ($temppid as $one) {
                    $temppidarr = array_merge($temppidarr, $this->pidarr[$one]);
                }
            } else {
                $temppidarr = $this->pidarr[$tag['pid']];
            }
            $where .= ' and cid in(' . join(',', $temppidarr) . ')';
            if (isset($tag['source'])) {
                $sourcewhere .= ' and cid in(' . join(',', $temppidarr) . ')';
            }
        }
        // /栏目类型读取
        if (isset($tag['deflag'])) {
            if (strpos($tag['deflag'], ',') === false) {
                $where .= ' and ' . $tag['deflag'] . '=1 ';
            } else {
                $deflag = explode(',', $tag['deflag']);
                if ($deflag) {
                    $where .= ' and (' . join('=1 or ', $deflag) . '=1)';
                }
            }
        }
        // /行数
        if (isset($tag['row'])) {
            $row = $tag['row'];
        } else {
            $row = 10;
        }
        // /排序
        $order = 'newsid desc';
        if (isset($tag['order'])) {
            if (strpos($tag['order'], 'hits')) { // 按照点击派寻
                $order = ' hits desc';
            }
        }
        // /source 资源读取
        if ($tag['source']) {
            $sourstr = $tag['source'];
            $sourstr = explode(',', $tag['source']);
            $allsw = array();
            foreach ($sourstr as $one) {
                $allsw[] = 's' . $one . '=1';
            }
			if(!$this->resources_news){
				  $this->resources_news = M("resources_news"); // 新闻id
			}
          
            $sourcewhere = empty($sourcewhere) ? '' : $sourcewhere;
            $tempnewsid = $this->resources_news->where(join(' and ', $allsw) . $sourcewhere)
                ->field('newsid')
                ->limit($row)
				->order('newsid desc')
                ->select();
            if ($tempnewsid) {
                $allidarr = array();
                foreach ($tempnewsid as $one) {
                    $allidarr[] = $one['newsid'];
                }
                $where .= ' and newsid in(' . join(',', $allidarr) . ')'; // 读取有资源的新闻，目前只能到栏目合适
				////取出需要的资源内容做准备
				if(!$this->source_content){
					$this->source_content = M("source_content"); // 新闻id         
				}				
				$tempsourarr = $this->source_content->field('max(id),newsid,typeid,source_content')->where(' typeid in ('.join(',',$sourstr).') and newsid in('.join(',',$allidarr).')')->group('newsid,typeid')->select();
				//echo $this->source_content->getLastSQL();
				$newssour=array();
				foreach($tempsourarr as $sone){
					$newssour[$sone['newsid']]['s_'.$sone['typeid'].'_1']=$sone['source_content'];
				}
            }			
        }
        // 实例文章读取
		if(!$this->news_deflag){
			 $this->news_deflag = M("news_deflag"); // 实例化User对象
		}       
        $newsid = $marr = array();
        // /tag相关新闻，直接取数据库
        if (isset($tag['tag'])) {
			if($this->seo_tagindex){
				 $this->seo_tagindex = M("seo_tagindex"); // 新闻id
			}           
            $tagstr = str_replace('，', ',', $tag['tag']);
            $tagstr = str_replace(',', ',', $tag['tag']);
            $tagarr = explode(',', $tagstr);
            $tagarrid = array();
            foreach ($tagarr as $one) {
                $tagarrid[] = " md5tag='" . md5($one) . "' ";
            }
            if ($tagarrid) {
                $temparr = $this->seo_tagindex->field('id')
                    ->where(join(' or ', $tagarrid))
                    ->select();
                if ($temparr) {
                    unset($tagarrid);
                    foreach ($temparr as $two) {
                        $tagarrid[] = $two['id'];
                    }
                    $seo_taglist = M("seo_taglist"); // 新闻id
                    $tempnewsidarr = $seo_taglist->field('aid as newsid,catalog_id as cid')
                        ->where('tid in(' . join(',', $tagarrid) . ')')
                        ->group('newsid')
                        ->order('newsid desc')
                        ->limit($row)
                        ->select();
                    $tempidarr = array();
                    foreach ($tempnewsidarr as $one) {
                        $tempidarr[] = $one['newsid'];
                    }
                    $tempwhere .= ' and newsid in(' . join(',', $tempidarr) . ')';
                    $tempnewsidarr = $this->news_deflag->where($where . $tempwhere)
                        ->field($field.',cid')
                        ->order($order)
                        ->group($group)
                        ->limit($row)
                        ->select();
                    $tempint = count($tempnewsidarr);				
                    if ($row - $tempint > 0) { // 读取的条数不够，拿栏目来补充
                        $row = $row - $tempint; // 下面处理剩余没有读取的条数
                        $cidarr = array();
                        $newsidarrc = array();
                        foreach ($tempnewsidarr as $one) {
                            $newsidarrc[] = $one['newsid'];
							$cidarr[]=$one['cid'];
                        }
                        $where .= ' and newsid not in(' . join(',', $newsidarrc) . ')';
                        if (count($cidarr) > 0) {
                            $where .= ' and cid in(' . join(',', $cidarr) . ')';
                        }
                    }
                }
            }
        }
        // /允许出现重复新闻
        if (! isset($tag['repeat'])) {
            if (count($this->repeat) > 0) {               
                $where .= ' and newsid not in(' . join(',', $this->repeat) . ') ';
                //echo $where;die;
            }
            
        }
        // echo $where;
        // /取出新闻或者tag没有凑够
        if (!isset($tempnewsidarr) || isset($cidarr)) {
            $newsidarr = $this->news_deflag->where($where)
                ->field($field)
                ->order($order)
                ->group($group)
                ->limit($row)
                ->select();
        }		
        if ($tempnewsidarr && isset($newsidarr)) {
            $newsidarr = array_merge($tempnewsidarr, $newsidarr);
        } elseif ($tempnewsidarr) {
            $newsidarr = $tempnewsidarr;
        }     
        foreach ($newsidarr as $one) {
            $newsid[] = 'news_' . $one['newsid'];
            if (isset($tag['brandinfo'])) { // /如果读取项目新闻
                $marr[$one['memberid']] = array();
            }
        }
        // /打印sql
        if ($tag['stop']) {
            if ($this->resources_news) {
                echo $this->resources_news->getLastSQL() . '<br/>';
            }
            echo $this->news_deflag->getLastSQL();
            die();
        }
        // /取得项目数组
        if (isset($tag['brandinfo']) && $marr) {
            $this->arcResit->getRedis(); // /获取栏目数组不在读取数据库
            $tempmarr = $this->arcResit->redis->hmget('ls_member_ent_brandinfo', array_keys($marr));
            foreach ($tempmarr as $two) {
                $two = json_decode($two, true);
                $marr[$two['memberid']] = $two;
            }
        }
        // print_r($newsid);die;
        // /读取新闻内容
        $this->arcResit->getRedisNew(); // /获取120的redis实例
        $temparr = $this->arcResit->redisNew->mget($newsid);
        // print_r($temparr);//die;
        // ///结果处理
        $i = 1;
        unset($one);
        $resultarr = array();
        foreach ($temparr as &$one) {
            $one = json_decode($one, true);
            if (empty($one['title'])) {
                continue;
            }
            $this->repeat[] = $one['newsid']; // 记录重复id
            if (strpos($one['deflag'], 'j') !== false) {
                $one['url'] = $one['outsideurl']; // 兼容跳转新闻
            }
			///资源匹配
			if(isset($newssour)){
				if(isset($newssour[$one['newsid']])){
					$one=array_merge($one,$newssour[$one['newsid']]);
				}				
			}
            if (isset($tag['brandinfo']) && $marr[$one['member_id']]) {
                $one = array_merge($one, $marr[$one['member_id']]);
            }
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
            $one['oid'] = $i;
            $i ++;
            $resultarr[] = $one;
        }
        unset($temparr);
        // /数据缓存
        if (isset($tag['cache'])) {
            $cachetime = 86400; // 默认缓存一天
            if (isset($tag['cachetime'])) {
                $cachetime = intval($tag['cachetime']);
                $cachetime = $tag['cachetime'] < 86400 * 7 ? $cachetime : 86400 * 7;
            }
            $this->arcResit->redisNew->set($lckey, json_encode($resultarr));
            $this->arcResit->redisNew->expire($lckey, $cachetime);
        }
        return $resultarr;
    }
}
?>