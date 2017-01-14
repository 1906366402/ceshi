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
        return($publicArray);
    } 
    
    /**
     * tag标签调用方法(tag,id,addtime)
     * row="10,20"
     * order="addtime desc,total desc,id desc,count desc"
     */
    public function getTagListHa($tag){
        $taglistModel = new Model('seo_tagindex');
        $row = $this->row($tag['row']);        //行数
        //排序
        if(isset($tag['order'])){
            $order = $tag['order'];
        }else{
            $order = 'total desc';
        }
        $taglistArray = $taglistModel->field('id,tag,addtime')->order($order)->limit($row)->select();
        return $taglistArray;
    }
    
    /**
     * 问答标签调用方法
     */
    public function getQaList($pararr){
		$tag=json_decode($pararr,true);
		$askModel = new Model('qa_ask');
        $row = $this->row($tag['row']);        //行数
		//排序
		if(isset($tag['order'])){
		    $order = $tag['order'];
		}else{
		    $order = 'hits desc,ctime desc';
		}
		$askArrayStart = $askModel->field('ask_content,ctime,industry,askid')->order($order)->limit($row)->select();
        $askArray = array();
		foreach($askArrayStart as $key=>$two){
            if(!empty($two['ask_content'])){
                $askArray[$key]['content'] = $two['ask_content'];
                $askArray[$key]['ctime'] = $two['ctime'];
                $askArray[$key]['hits'] = $two['hits'];
                $askArray[$key]['askid'] = $two['askid'];
                $askArray[$key]['url'] = $this->getIndustry($two);
            }
		}
		return $askArray;
    }
    
    
    //获取问答URL
    function getIndustry($two){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        $id = $two['askid'];
        $industryList = json_decode($redislbn->redis->hget('classindustry','keyarray'),true);
        $industryPathName = $industryList[$two['industry']]['pathname'];
        $url = "http://www.liansuo.com/qa/$industryPathName/$id/";
        return $url;
    }
    
    //行数判断
    function row($row){
        $row = isset($row)?$row:10;
        return $row;
    }
    
    
    
}
?>