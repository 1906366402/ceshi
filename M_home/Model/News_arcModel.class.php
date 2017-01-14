<?php
namespace M_home\Model;
use Think\Model;
class News_arcModel extends Model{
    protected $tableName = '';                //如果没有在配置文件中设置表前缀，就应该设置为protected $trueTableName = 'crm28_client'; 

    
    public function getArcList($pararr){
		print_r(json_decode($pararr,true));die;
		$News_arc = M("news_arc"); // 实例化User对象
		$temparr =$News_arc->order('newsid desc')->limit('200,29')->select();
		return $temparr;        
    }   
}
?>