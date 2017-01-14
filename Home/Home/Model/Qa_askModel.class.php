<?php
namespace Home\Model;

use Think\Model;

class Qa_askModel extends Model
{
    protected $tableName = '';
    
    /**
     * 公共标签调用方法
     */
    public function getModList($pararr){
        $tag=json_decode($pararr,true);
        //调取tag标签
        if($tag['name']=='tag'){
            $publicArray = $this->getTagListHa($tag);
        }
        //调取问答标签
        if($tag['name']=='qalist'){
            $publicArray = $this->getQaList($tag);
        }
        //调取专题名称
        if($tag['name']=='seoword'){
            $publicArray = $this->getSeoWordList($tag);
        }
        //调取newssearch
        if($tag['name']=='newssearch'){
            $publicArray = $this->getNewsSearchList($tag);
        }
        //调取友情链接
        if($tag['name']=='linklist'){
            $publicArray = $this->getFriendLinkList($tag);
        }
        return($publicArray);
    } 
    
    
    /**
     * linklist标签调用方法
     */
    public function getFriendLinkList($tag){
        $row = $this->row($tag['row']);
        $linkModel = new Model('class_links');
        $linkArray2 = $linkModel->field('title,linkurl')->limit($row)->select();
        $i=1;
        $linkArray = array();
        foreach($linkArray2 as $one){
            $one['oid'] = $i;
            $one['url'] = $one['linkurl'];
            $i++;
            $linkArray[] = $one;
        }
        return $linkArray; 
    }
    
    
    /**
     * seoword标签调用方法
     */
    public function getSeoWordList($tag){
        $seo_words=M('news_keywords');
        $row = $this->row($tag['row']);        //行数
        
        //排序
        if(isset($tag['order'])){
            $order = $tag['order'];
        }else{
            $order = 'aid desc';
        }
        
        if(isset($tag['cid'])){
            $where['subindustry'] = $tag['cid'];
            $where['industry'] = $tag['cid'];
            $where["_logic"] = 'or';
            $map['_complex'] = $where;
        }else{
        }
        $keyword2=$seo_words->field('aid as id,if(`keyword`=\'\',`key`,`keyword`) as word,industry,subindustry')->where($map)->limit($row)->order($order)->select();
        if(isset($tag['stop'])){
            echo $seo_words->getLastSql();die; 
        } 
        $i=1;
        foreach($keyword2 as $one){
            $one['url'] = 'http://www.liansuo.com/unit/'.$one['id'].'.html';
            $one['oid'] = $i;
            $i++;
            $keyword3[] = $one;
        }
        return $keyword3; 
    } 
    
    /**
     * newssearch标签调用方法
     */
    public function getNewsSearchList($tag){
        $seo_words=M('news_search');
        $row = $this->row($tag['row']);        //行数
    
        //排序
        if(isset($tag['order'])){
            $order = $tag['order'];
        }else{
            $order = 'aid desc';
        }
    
        if(isset($tag['cid'])){
            $where['subindustry'] = $tag['cid'];
            $where['industry'] = $tag['cid'];
            $where["_logic"] = 'or';
            $map['_complex'] = $where;
        }else{
        }
        $keyword2=$seo_words->field('aid as id,if(`keyword`=\'\',`key`,`keyword`) as word,industry,subindustry')->where($map)->limit($row)->order($order)->select();
        //echo $seo_words->getLastSql();
        if(isset($tag['stop'])){
            echo $seo_words->getLastSql();die; 
        }
        $i=1;
        foreach($keyword2 as $one){
            $one['url'] = 'http://www.liansuo.com/hot/'.$one['id'].'.html';
            $one['oid'] = $i;
            $i++;
            $keyword3[] = $one;
        }
        return $keyword3;
    }
    
    
    
    /**
     * tag标签调用方法(tag,id,addtime)
     * row="10,20"
     * num=字的个数
     * order="addtime desc,total desc,id desc,count desc"
     */
    public function getTagListHa($tag){
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $taglistModel = new Model('seo_tagindex');
        $row = $this->row($tag['row']);        //行数
        //排序
        if(isset($tag['order'])){
            $order = $tag['order'];
        }else{
            $order = 'total desc';
        }
        //字的数量
        if(isset($tag['num'])){
            $num = $tag['num']*3;
        }else{
            $num = 30;
        }
        //$redislbn->redisNew->del('tag_biaoqianlbn');
        if($redislbn->redisNew->exists('tag_biaoqianlbn')){
            $taglistArray = json_decode($redislbn->redisNew->get('tag_biaoqianlbn'),true);
        }else{
            $taglistArray = $taglistModel->field('id,tag,addtime')->order($order)->limit(10000)->select();
            $redislbn->redisNew->setex('tag_biaoqianlbn',959000,json_encode($taglistArray));
        }
        shuffle($taglistArray);
        $taglistArray2 = array_slice($taglistArray,0,$row*10);
        $taglistArray = array();
        $i=1;
        foreach($taglistArray2 as $one){
            if(strlen($one['tag'])<$num){
                $one['oid'] = $i;
                $one['url'] = "http://www.liansuo.com/tag/".$one['id'].'/';
                $taglistArray[] = $one;
                $i++;
            }
        }
		
        $taglistArray2 = array_slice($taglistArray,0,$row);
        return $taglistArray2;
    }
     
    /**
     * 问答标签调用方法
     * total 回答数到达多少
     * pid 项目id
     * cid 行业id
     * order 排序
     * flag=1已解决
     * 
     */
    public function getQaList($tag){
		$askModel = new Model('qa_ask');
        $row = $this->row($tag['row']);        //行数
		//排序
		if(isset($tag['order'])){
		    $order = $tag['order'];
		}else{
		    $order = 'hits desc,ctime desc';
		}
		//回答数量
		if(isset($tag['total'])){
		    $total = $tag['total'];
		}else{
		    $total = 15;
		}
        
		//项目pid
		if(isset($tag['pid'])){
		    //该问题是否解决
		    if(isset($tag['flag'])){
		        $flag = $tag['flag'];
		        $where = "and b.flag=$flag";
		    }
		    //行业
		    if(isset($tag['cid'])){
		        $cid = $tag['cid'];
		        $where = "a.memberid=b.pid and a.delstatus=1 and b.ask_content <> '' and b.req_num>$total and (b.subindustry = $cid OR b.industry = $cid ) ".$where;
		    }else{
		        $where = "a.memberid=b.pid and a.delstatus=1 and b.ask_content <> '' and b.req_num>$total ".$where;
		    }
		    $askArrayStart = $askModel->query("select a.memberid,a.delstatus,b.* from ls_member_base as a,ls_qa_ask as b where
		       $where order by $order limit $row");
		}else{
		    //该问题是否解决
		    if(isset($tag['flag'])){
		        $map['flag'] = $tag['flag'];
		    }
		    //行业
		    if(isset($tag['cid'])){
		        $where['subindustry'] = $tag['cid'];
		        $where['industry'] = $tag['cid']; 
		        $where["_logic"] = 'or';
		        $map['ask_content'] = array('exp','<> ""');
		        $map['req_num'] = array('gt',$total);
		        $map['_complex'] = $where;
		    }else{
		        $map['ask_content'] = array('exp','<> ""');
		        $map['req_num'] = array('gt',$total);
		    }
		    $askArrayStart = $askModel->field('ask_content,ctime,nickname,industry,askid,req_num,pid')->where($map)->order($order)->limit($row)->select();
		}
		//echo "<pre>";var_Dump($askArrayStart);die;
		//echo $askModel->getLastSql();die;
		if(isset($tag['stop'])){
		    echo $askModel->getLastSql();die;
		}
		$askArray = array();
		$i=1;
		foreach($askArrayStart as $key=>$two){
            if(!empty($two['ask_content'])){
                $askArray[$key]['oid'] = $i;
                $askArray[$key]['content'] = $two['ask_content'];
                $askArray[$key]['ctime'] = $two['ctime'];   
                $askArray[$key]['hits'] = $two['hits'];
                $askArray[$key]['id'] = $two['askid'];
                $askArray[$key]['nickname'] = $two['nickname'];
                $askArray[$key]['req_num'] = $two['req_num'];
                $askArray[$key]['flag'] = $two['flag'];
                $askArray[$key]['url'] = $this->getIndustry($two);
                if(isset($tag['pid'])){
                    $brandname = $this->getBrandname($two['pid']);
                    $askArray[$key]['ypurl'] = $brandname['ypurl'];
                    $askArray[$key]['memberid'] = $brandname['memberid'];
                    $askArray[$key]['hburl'] = $brandname['hburl'];
                    $askArray[$key]['brandname'] = $brandname['brandname'];
                    $askArray[$key]['logo'] = $brandname['logo'];
                    $askArray[$key]['brandname'] = $brandname['brandname'];
                }
            }
            $i++;
		}
		return $askArray;
    }
     
    //获取项目信息
    function getBrandname($pid){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $brandinfo = json_decode($redislbn->redis->hget('ls_member_ent_brandinfo', $pid), true);
        return $brandinfo;
    }
    
    //获取问答URL
    function getIndustry($two){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $id = $two['askid'];
        $industryList = json_decode($redislbn->redis->hget('classindustry','keyarray'),true);
        $industryPathName = $industryList[$two['industry']]['pathname'];
        //$url = "http://www.liansuo.com/qa/$industryPathName/$id/";
        $url = "http://www.liansuo.com/qa/$id.html";
        return $url;
    }
    
    //行数判断
    function row($row){
        $row = isset($row)?$row:10;
        return $row;
    }
}
?>