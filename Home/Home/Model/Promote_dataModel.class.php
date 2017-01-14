<?php
namespace Home\Model;

use Think\Model;

class Promote_dataModel extends Model
{

    protected $tableName = '';
	private $adRedis = NULL;
	private $bannerID  = NULL;
	private $resources_memory=NULL;
	
    public function getAdList($pararr){
		
		$tag=json_decode($pararr,true);
		if (! $this->$adRedis) { // /判断redis实例，防止多次实例
			 $this->adRedis = A('Common/Comment');
		}
		//print_r($tag);
		$lckey = 'ls_ad_list_cache_' . md5($pararr);
		if (isset($tag['cache'])) {
            if (strpos($tag['cache'], ',') !== false) {
                $cache = explode(',', $tag['cache']); // 要缓存起来的字段，循环体里面用到的
            } else {
                echo '缓存需要设置你需要的字段';
                die();
            }
            $this->adRedis->getRedisNew(); // /获取120的redis实例
            if ($this->adRedis->redisNew->exists($lckey)) { // 判断缓存是否存在
                $tempstr = $this->adRedis->redisNew->get($lckey);
                $temparr = json_decode($tempstr, true);
				
                return $temparr;
            }
        }
		$this->adRedis->getRedis();//可定需要的redis
		//返回项目的id
		$temparr=array();
		$where='';
		if(isset($tag['row'])){
			$row=$tag['row'];
		}else{
			$row=100;
		}
		if(isset($tag['page'])){
			
		}
		
		
		if(isset($tag['order'])){
		    $order = $tag['order'];
		}else{
		    $order = 'a.id asc';
		}
		
		
		///广告位置ID调用
		//if(isset($tag['cid'])){
		//$promote_data=M('promote_data');
		//$temparr=$promote_data->field('source_id,memberid,auto')->where(' data_id=13 ')->order(' sort_order asc ,id asc')->select();
		if(isset($tag['id'])){//新版用id
			$tempr=$this->adRedis->redis->hget('ls_promote_data',$tag['id']);
			$tempa=json_decode($tempr,true);
			//print_r($tempa);die;
			if(count($tempr)>0){
				foreach($tempa as $one){
					$temparr[$one['memberid']]=$one;
				}
			}			
		}
		//print_r($temparr);die;
		if(isset($tag['cid'])){//原来的系统			
			if(!$this->bannerID){//避免多次实例
				$this->bannerID=M('ad_weburl');
			}
			//delstatus 不能等于 0 或 2
			$tempar=$this->bannerID->query('select b.*,b.member_id as memberid from ls_ad_weburl as a left join ls_ad_banner as b on a.bannerID=b.id where a.channelID='.$tag['cid'].' order by '.$order.' limit '.$row);

			
			if(count($tempar)>0){
				foreach($tempar as $one){
					$temparr[$one['member_id']]=$one;
				}
			}
			unset($one);			
		}
		
		if($tag['source']){
			$swhere=' 1=1 ';
			if(isset($tag['industry'])&&!empty($tag['industry'])){//行业
				$swhere.=' and (industry ='.$tag['industry'].' or subindtstry='.$tag['industry'].')';
			}
			$allsource=explode(',',$tag['source']);
			foreach($allsource as &$s){
				$s='s'.$s.'<>0';
			}			
			$swhere.=' and '.join(' and ',$allsource);
			//echo $swhere;die;
			if(!$this->resources_memory){//防止多次实例
				$this->resources_memory=M('resources_memory');
			}
			
			$row2 = $row-count($temparr);
			//echo $swh;die;
			$temparrm=$this->resources_memory->field('memberid')->where($swhere)->limit($row2)->select();
			//echo $this->resources_memory->getLastSql();die;
			if(count($temparrm)>0){
				foreach($temparrm as $one){
					$temparr[$one['memberid']]=$one; 
				}
			}
		}		
		if(isset($tag['stop'])){
		    //echo $this->resources_memory->getLastSql().'<br/>';
		    echo $this->bannerID->getLastSql();
		    die;
		}
		///读取redis数据
		if(count($temparr)>0){
			//print_r($proarrid);
			$allpro=$this->adRedis->redis->hmget('ls_member_ent_brandinfo',array_keys($temparr));
			$tempresult=array();			
			foreach($allpro as $op){
				$tempone=json_decode($op,true);
				if(count($tempone)>10){//数据项有值
					$tempresult[$tempone['memberid']]=$tempone;
				}				
			}
		}
		$i=1;
		$resultarr=array();
		foreach($temparr as &$one){
			$one['oid']=$i;
			$one=array_merge($one,$tempresult[$one['memberid']]);
			if (isset($cache)) { // 判断需要缓存的数据
                $cache[] = 'memberid';
                $cache[] = 'oid';
                $cache[] = 'hburl';
				$cache[] = 'ypurl';
                $two = array();
                foreach ($one as $key => $val) {
                    if (in_array($key, $cache)) {
                        $two[$key] = $val;
                    }
                }
                $one = $two;
            }
			$resultarr[]=$one;
			$i++;
		}
		
		if (isset($tag['cache'])) {
            $cachetime = 86400; // 默认缓存一天
            if (isset($tag['cachetime'])) {
                $cachetime = intval($tag['cachetime']);
                $cachetime = $tag['cachetime'] < 86400 * 7 ? $cachetime : 86400 * 7;
            }
			$this->adRedis->getRedisNew(); // /获取120的redis实例
            $this->adRedis->redisNew->set($lckey, json_encode($resultarr));
            $this->adRedis->redisNew->expire($lckey, $cachetime);
        }

		unset($temparrm,$tempresult);
		//echo "<pre>";var_Dump($resultarr);
        return $resultarr;
    }
}
?>