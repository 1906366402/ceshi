<?php
namespace Home\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Controller;
use Think\Model;

class TagController extends Controller {

    /**
     * tag首页 
     */
    public function index(){
        $commPinyin = A('Common/Comment');
        $tagModel = new Model();
        $cache = S(array('type'=>'file','prefix'=>'think','expire'=>86400));
        //S('tagPage',null);  //删除缓存
        if(S('tagPage')){
            echo S('tagPage');
        }else{
            $tagArray2 = $tagModel->query('select id,tag from ls_seo_tagindex order by id desc limit 3000');
            foreach($tagArray2 as $one){ 
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'A'){$tagArrayA[$a]['tag'] = $one['tag'];$tagArrayA[$a]['id'] = $one['id'];$a++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'B'){$tagArrayB[$b]['tag'] = $one['tag'];$tagArrayB[$b]['id'] = $one['id'];$b++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'C'){$tagArrayC[$c]['tag'] = $one['tag'];$tagArrayC[$c]['id'] = $one['id'];$c++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'D'){$tagArrayD[$d]['tag'] = $one['tag'];$tagArrayD[$d]['id'] = $one['id'];$d++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'E'){$tagArrayE[$e]['tag'] = $one['tag'];$tagArrayE[$e]['id'] = $one['id'];$e++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'F'){$tagArrayF[$f]['tag'] = $one['tag'];$tagArrayF[$f]['id'] = $one['id'];$f++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'G'){$tagArrayG[$g]['tag'] = $one['tag'];$tagArrayG[$g]['id'] = $one['id'];$g++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'H'){$tagArrayH[$h]['tag'] = $one['tag'];$tagArrayH[$h]['id'] = $one['id'];$h++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'I'){$tagArrayI[$i]['tag'] = $one['tag'];$tagArrayI[$i]['id'] = $one['id'];$i++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'J'){$tagArrayJ[$g]['tag'] = $one['tag'];$tagArrayJ[$g]['id'] = $one['id'];$g++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'K'){$tagArrayK[$k]['tag'] = $one['tag'];$tagArrayK[$k]['id'] = $one['id'];$k++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'L'){$tagArrayL[$l]['tag'] = $one['tag'];$tagArrayL[$l]['id'] = $one['id'];$l++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'M'){$tagArrayM[$m]['tag'] = $one['tag'];$tagArrayM[$m]['id'] = $one['id'];$m++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'N'){$tagArrayN[$n]['tag'] = $one['tag'];$tagArrayN[$n]['id'] = $one['id'];$n++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'O'){$tagArrayO[$o]['tag'] = $one['tag'];$tagArrayO[$o]['id'] = $one['id'];$o++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'P'){$tagArrayP[$p]['tag'] = $one['tag'];$tagArrayP[$p]['id'] = $one['id'];$p++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'Q'){$tagArrayQ[$q]['tag'] = $one['tag'];$tagArrayQ[$q]['id'] = $one['id'];$q++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'R'){$tagArrayR[$r]['tag'] = $one['tag'];$tagArrayR[$r]['id'] = $one['id'];$r++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'S'){$tagArrayS[$s]['tag'] = $one['tag'];$tagArrayS[$s]['id'] = $one['id'];$s++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'T'){$tagArrayT[$t]['tag'] = $one['tag'];$tagArrayT[$t]['id'] = $one['id'];$t++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'U'){$tagArrayU[$u]['tag'] = $one['tag'];$tagArrayU[$u]['id'] = $one['id'];$u++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'V'){$tagArrayV[$v]['tag'] = $one['tag'];$tagArrayV[$v]['id'] = $one['id'];$v++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'W'){$tagArrayW[$w]['tag'] = $one['tag'];$tagArrayW[$w]['id'] = $one['id'];$w++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'X'){$tagArrayX[$x]['tag'] = $one['tag'];$tagArrayX[$x]['id'] = $one['id'];$x++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'Y'){$tagArrayY[$y]['tag'] = $one['tag'];$tagArrayY[$y]['id'] = $one['id'];$y++;}
                if(substr($commPinyin->get_letter($one['tag']),0,1) == 'Z'){$tagArrayZ[$z]['tag'] = $one['tag'];$tagArrayZ[$z]['id'] = $one['id'];$z++;}
            }
            $this->assign('tagArrayA',$tagArrayA);$this->assign('tagArrayZ',$tagArrayZ);
            $this->assign('tagArrayB',$tagArrayB);$this->assign('tagArrayC',$tagArrayC);
            $this->assign('tagArrayD',$tagArrayD);$this->assign('tagArrayE',$tagArrayE);
            $this->assign('tagArrayF',$tagArrayF);$this->assign('tagArrayG',$tagArrayG);
            $this->assign('tagArrayH',$tagArrayH);$this->assign('tagArrayI',$tagArrayR);
            $this->assign('tagArrayJ',$tagArrayJ);$this->assign('tagArrayK',$tagArrayK);
            $this->assign('tagArrayL',$tagArrayL);$this->assign('tagArrayM',$tagArrayM);
            $this->assign('tagArrayN',$tagArrayN);$this->assign('tagArrayO',$tagArrayO);
            $this->assign('tagArrayP',$tagArrayP);$this->assign('tagArrayQ',$tagArrayQ);
            $this->assign('tagArrayR',$tagArrayR);$this->assign('tagArrayS',$tagArrayS);
            $this->assign('tagArrayT',$tagArrayT);$this->assign('tagArrayU',$tagArrayU);
            $this->assign('tagArrayV',$tagArrayV);$this->assign('tagArrayW',$tagArrayW);
            $this->assign('tagArrayX',$tagArrayX);$this->assign('tagArrayY',$tagArrayY);
            echo $tagPage =  $this->fetch();
            
            S('tagPage',$tagPage,86400);
        }
    }
    
    
    
    /**
     * tag列表页
     */
    public function taglist(){
        $member_ent_brandinfoModel = new Member_ent_brandinfoModel();  //实例化模型
        $redislbn = A('Common/Comment');
        $redislbn->getRedisNew();
        $taglistModel = new Model('seo_tagindex');
        $newsid = I('get.newsid');
        $page = I('get.page');
        $taglistArrayIndex = $taglistModel->query("select aid from ls_seo_taglist where tid=".$newsid." order by aid desc");
        
        $where['id'] = $newsid;
        $tagindex = $taglistModel->field('tag,id,catalog_id')->where($where)->find();
        $where2['catalog_id'] = $tagindex['catalog_id'];
        $where2['flag'] = array('in',array(1,2));
        $where2['iscreatehtml'] = array('in',array(1,2));
        
        $arclistArray = new Model('news_arc');
        $taglistArrayPush = $arclistArray->field('newsid aid')->where($where2)->order('ctime desc')->limit(50)->select();
        
        $taglistArray2 = array_merge($taglistArrayIndex,$taglistArrayPush);
        foreach($taglistArray2 as $one){
            $taglistArray3[] = 'news_'.$one['aid'];
        }
        //去除redis中没有的新闻
        foreach($taglistArray3 as $three){
            if($redislbn->redisNew->exists($three)){
                $taglistArray4[] = $three; 
            }
        }
        $countPage = count($taglistArray4);
        $pagenum = empty($page)?0:10*($page-1);
        $taglistArraySlice = array_slice($taglistArray4,$pagenum,10);
        $taglistArrayRedis = $redislbn->redisNew->getMultiple($taglistArraySlice);
        foreach($taglistArrayRedis as $two){
            $taglistArray[] = json_decode($two,true);
        }
        
        //热门标签
        if($redislbn->redisNew->exists('tagArrayrmbq')){
            $tagArrayrmbq = json_decode($redislbn->redisNew->get('tagArrayrmbq'),true);
        }else{
            $tagArrayrmbq = $taglistModel->field('id,tag')->order('total desc')->limit(130)->select();
            shuffle($tagArrayrmbq);
            $tagArrayrmbq = array_slice($tagArrayrmbq,0,13);
            
            $redislbn->redisNew->setex('tagArrayrmbq',259200,json_encode($tagArrayrmbq));
        }
        //热门推荐
        $tag_rmtj = $member_ent_brandinfoModel->newsad('tag_rmtj',811,0,6);     
        //专题推荐
        $taglist_zttj = $member_ent_brandinfoModel->news_source('taglist_ztbd',759,4,$source="{pic:213*134}");       //专题报道
        //数据分页
        $pageObj = new \Think\Page($countPage,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
        $pageObj->setConfig('prev', '上一页');
        $pageObj->setConfig('next', '下一页');
        $pageObj->setConfig('last', '尾页');
        $pageObj->setConfig('first', '首页');
        $page_tpl = urlencode('[PAGE]');
        $pageObj->rollPage=5;
        $pageObj->url = 'http://www.liansuo.com/tag/'.$newsid.'/p'.$page_tpl.'.html';
        $show = $pageObj->show();// 分页显示输出
        $this->assign('page',$show); 
        $this->assign('connPage',$page);
        $this->assign('newsid',$newsid);
        $this->assign('tag_rmtj',$tag_rmtj);
        $this->assign('tagindex',$tagindex);
        $this->assign('taglistArray',$taglistArray);
        $this->assign('taglist_zttj',$taglist_zttj);
        $this->assign('tagArrayrmbq',$tagArrayrmbq);
        $this->display();
    }
} 