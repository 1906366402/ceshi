<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class ChannelController extends Controller {
	
	/*  二级分类 */
	public function index(){	
		///获取要处理的行业名称 		
		$channelname=I('get.channel');
		
		$news_category=M('news_category');
		
		$cataone=$news_category->where("categorydir='$channelname'")->find();
		if(!$cataone){
			die('栏目路径错误');
		}		
		$view=I('get.view');	
		$this->assign('cata',$cataone);
		if($view=='view'){///预览还是生成		
			$this->display($channelname);
		}else{
		
			$this->buildHtml('index.html', DOCUMENT_ROOT_DIR.'/website/'.$channelname,$channelname);
		}
		
		
	}
} 















