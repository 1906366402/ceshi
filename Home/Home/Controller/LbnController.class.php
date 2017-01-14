<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class LbnController extends Controller {
    
    
    /**
     * 空方法
     */
    public function _empty(){
        
    }
    
    
    public function zhimeng(){
        $contents = file_get_contents('http://www.pm25.com/city/beijing.html');
        $pattern = '/<div class="citydata_banner">.*<!--主体内容-->/s';
        $a = preg_match($pattern, $contents,$array);
        $arr = str_replace(array('/news/385.html','href="/news/386.html"','<a class="cbor_morelink" href="/protection.html" target="_blank">更多防护&gt;&gt;</a>'),array('','',''),$array[0]);
        echo $arr;
    }
    
    
    
    /**
     * 修改首页海报链接
     */
    public function hbupdate(){}
    
    
    /**
     * 新年
     */
    public function newYear(){
        $memberid = I('get.id');
        $model = new Model('member_ent_brandinfo');
        $where['memberid'] = $memberid;
        $array = $model->where($where)->find();
        $brandname = $array['brandname'];
        $this->assign('brandname',$brandname);
        echo $this->buildHtml('newyear.html', $_SERVER['DOCUMENT_ROOT'].'/2016/newYear/','newyear');
        //$this->display();
    }
    
    
    /**
     * @刘郅网站bug阿里云getDocComment()函数不起作用。
     */
    public function dazhi(){
        $act   = new \Think\Page(555,20);
        $func  = new \ReflectionMethod($act,'show');//获取类中方法的注释
        $func  = new \ReflectionClass($act); //获取类的注释
        $tmp   = $func->getDocComment();   //获取注释调用 的方法
        var_dump($tmp);
    }
     
    /**
     *@正盛需求：根据项目名称调取对应行业
     */
    public function index(){
        echo "66666666666";die;
        $model = new Model();
		$arr = file("/data/web/test.com/aaa.txt");
		$arr = array_slice($arr,400,500);
		foreach($arr as $a){
			$a = str_replace(array("\r","\n","\r\n"),"",$a);
			$array = $model->query('select a.memberid,a.subindustry,b.id,b.industryname,c.memberid,c.brandname from ls_member_ent_info as a,ls_class_industry as b,ls_member_ent_brandinfo as c where  c.brandname="'.$a.'" and a.subindustry=b.id and a.memberid=c.memberid');
			echo $array[0]['industryname']."<br/>";
		}
    }
    
    /*
     * 寅初需求：调出全部SEO关键字
     */
    public function seokeyword(){
        $model = new Model('seo_words');
        $where['id'] = array('lt',20000);
        $array = $model->field('url')->where($where)->select();
        foreach($array as $one){
            echo $one['url'].'<br/>'; 
        }
    }
    
    /**
     * 金棒需求。
     */
    public function seokeywordee(){
        $model = new Model('member_ent_brandinfo');
        $where['mancheck'] = 1;
        $where['orderid'] = array('ELT',0);
        $array = $model->field('brandname,memberid,dir_name')->where($where)->select();
        foreach($array as $one){
            echo $one['memberid'] .','. $one['brandname'].','.$one['dir_name'].'<br/>';
        }
    }
    
    /**
     * 寅初需求：重命名文件夹
     */
    public function remove(){
        $a = $_SERVER['DOCUMENT_ROOT'].'/html/website/jiameng/99999';
        $array = file('bao.txt');
        foreach($array as $one){
            $ddd = str_replace(array("\r","\n","\r\n",' '),"",$one);
            $two = $a.$ddd;
            $three = $a.'111'.$ddd;
            $four = rename($two,$three);
            var_dump($four);
        }
    }
    
    /**
     * 金棒需求：重命名文件夹
     */
    public function renamejb(){
        $a = dirname(__FILE__);
        $dir = $a.'\\yidong\\';
        $array = scandir($dir);
        foreach($array as $one){
            if($one!=='.' && $one!=='..'){
                $dir = $a.'\\yidong\\';
                $dir = $dir."$one";
                echo $dir;
                $bb = rename($dir.'\index.html',$dir.'\jiameng.html');//重命名文件
                unlink($dir.'\jiameng2.html');//删除
            }
        }        
    }
    
    /**
     * 删除双重文件夹下的某几个文件
     */
    public function delDir(){
        $dir = $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/Web/p/';
        $array = scandir($dir);
        foreach($array as $key=>$one){
            $dir = $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/Web/p/';
            $dir = $dir.$one;
            if($one!=='.' && $one!=='..'){
                $array2 = scandir($dir);
                foreach($array2 as $two){
                    if($two=='scqj.html'||$two=='index.html' ||$two=='cpzs.html' ||$two=='jmys.html' ||$two=='jmlc.html' || $two=='lsdt.html' ||$two=='jyfx.html'||$two=='ppxw.html'||$two=='lxfs.html'){
                        $dirrr = $dir.'/'.$two;
                        echo $dirrr.'<br/>';
                        $b = unlink($dirrr);
                        var_dump($b);
                    }
                }
            }
        }
    }
    
    /**
     * 删除所有项目下的新闻
     */
    public function delProjectNews(){
        $dir = $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/m_Web/p/';
        $array = scandir($dir);
        foreach($array as $key=>$one){
            $dir = $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/Web/p/';
            $dir = $dir.$one;
            if($one!=='.' && $one!=='..'){
                $array2 = scandir($dir);
                foreach($array2 as $two){
                    if($two!=='..' && $two!=='.' && $two!=='lsrw.html' && $two!=='scqj.html' && $two!=='index.html' && $two!=='jiameng.html' && $two!=='cpzs.html' && $two!=='jmys.html' && $two!=='jmlc.html' && $two!=='lsdt.html' && $two!=='jyfx.html' && $two!=='ppxw.html' && $two!=='lxfs.html'){
                        $dirrr = $dir.'/'.$two;
                        echo $dirrr.'<br/>';
                        $b = unlink($dirrr);
                        var_dump($b);
                    }
                }
            }
        }
    }
    /**
     * 批量更新数据库数据 
     */
    public function updateCheck(){
        $model = new Model('member_ent_brandinfo');
        $arcModel = new Model('news_arc');
//         $infomodel = new Model('member_ent_info');
//         $where3['isupload'] = array('gt',0);
//         $array = $infomodel->field('memberid')->where($where3)->select();
//         $arrayid = array();
//         foreach($array as $one){
//             $arrayid[] = $one['memberid'];
//         }
        $where2['newsid'] = array('gt',581500);
        $array = $arcModel->field('newsid,member_id,url')->where($where2)->limit(10000,10000)->select();  
        foreach($array as $one){
            if(!empty($one['member_id'])){
                $data['url'] = "http://www.liansuo.com/p/".$one['member_id']."/".$one['newsid'].".html";
                $where['newsid'] = $one['newsid'];
                $beifen = $arcModel->data($data)->where($where)->save();
                var_Dump($beifen);       
            }
        }
    }
    
    
    /**
     * 查询出所有再投行业的pathname
     */
    public function pathname(){
        $arcModel = new Model('news_category');
        $where['listpage'] = array('eq',1);
        $where['isurl'] = array('eq',1);
        $array = $arcModel->field('categorydir')->where($where)->select();
        echo $arcModel->getLastSql();die;
        foreach($array as $key=>$one){
            if(!empty($one)){
                $pathname .=$one['categorydir'].'|';
                echo $key.'<br/>';
            }
        }
        echo $pathname;
    }
    
    
    /**
     * 更新项目的ypurl
     */
    public function updateurl(){
        $model = new Model('member_ent_brandinfo');
        $where2['orderid'] = array('gt',0);
        $array = $model->field('memberid,ypurl,ypurlyuanshi')->where($where2)->limit(20000)->select();
        foreach($array as $one){
            $memberid[] = $one['memberid'];
        }       
        $arcModel = new Model('news_arc');
        $where3['member_id'] = array('in',$memberid);
        $arcArray = $arcModel->where($where3)->limit(40000,10000)->select();
        
        foreach($arcArray as $one){
            if(!empty($one['urlbeifen'])){
                echo $one['url'].' '.$one['urlbeifen'].'<br/>';
            } 
//             if(!empty($one['ypurl']) && empty($one['mancheck'])){
//                 $data['ypurl'] = "http://www.liansuo.com/baodao/".$one['memberid'].".html";
//                 $where['memberid'] = $one['memberid'];
//                 $beifen = $model->data($data)->where($where)->save();
//                 var_Dump($beifen);                
//             }
        }
    }
    
    
    /**
     * 写入.htaccess文件
     */
    public function writeFile(){
        foreach($array as $key=>$one){
            if(strpos($one['ypurlyuanshi'],'/v-') && !empty($one['ypurlyuanshi'])){
                $dir = $_SERVER['DOCUMENT_ROOT']."/html/website/yp/".$one['dir_name']."/.htaccess";
                $dirrr = $_SERVER['DOCUMENT_ROOT']."/html/website/yp/".$one['dir_name'];
                if(is_dir($dirrr) && !empty($one['dir_name'])){
                    $file = fopen($dir,'w+');
                     echo $dir.'<br/>';
                    $txt = "RewriteEngine On
    RewriteRule ^$ http://www.liansuo.com/p/".$one['memberid']."/ [L,R=301]
    RewriteRule ^scqj.html$ http://www.liansuo.com/p/".$one['memberid']."/scqj.html$1 [L,R=301]
    RewriteRule ^jmys.html$ http://www.liansuo.com/p/".$one['memberid']."/jmys.html$1 [L,R=301]
    RewriteRule ^lrfx.html$ http://www.liansuo.com/p/".$one['memberid']."/jyfx.html$1 [L,R=301]
    RewriteRule ^tpzs.html$ http://www.liansuo.com/p/".$one['memberid']."/cpzs.html$1 [L,R=301]
    RewriteRule ^lxfs.html$ http://www.liansuo.com/p/".$one['memberid']."/lxfs.html$1 [L,R=301]";
                    $a = fwrite($file, $txt);
                    //$b = unlink($dir);
                    fclose($file);
                    //die;
                }
            }else{

            }
        }
    }
    
    
    /**
     * 查询项目logo是否存在
     */
    public function selectlogo(){
        $model = new Model('member_ent_info');
        $array = $model->query("select a.memberid,a.delstatus,b.memberid,b.logo from ls_member_base a,ls_member_ent_info b
             where a.memberid=b.memberid and a.delstatus <> 0 order by a.memberid ");
        foreach($array as $one){
            $file = $_SERVER['DOCUMENT_ROOT'].'/img-liansuo-com/html/images/'.$one['logo'].'';
            if(file_exists($file)){
            }else{
                echo 'http://www.liansuo.com/top10/'.$one['memberid'].'.html'.'<br/>';
            }
        }
    } 
    
    
    /**
     * 更新项目新闻
     */
    public function projectnews(){
        $model = new Model('news_arc');
        $array = $model->query("select a.newsid,a.member_id from ls_news_arc a,ls_member_base b where a.member_id <> '' and a.member_id=b.memberid and b.delstatus <> 0 group by member_id  "); 
        $arr = array();
        foreach($array as $one){
            $arr[] = $one['newsid'];
        }
        $arr = array_slice($arr,0,1);
        foreach($arr as $two){
            $a = file_get_contents("https://manager.liansuo.com/index.php?action=293&newsid=".$two);
            if($a){
                echo "successsssssssssssss".'<br/>';
            }else{
                echo 'error';
            }
        }
    }
    
    /**
     * 修改数据库字段
     */
    public function updateDB(){
//         $model = new Model('member_ent_brandinfo');
//         $where3['orderid'] = array('gt',0);
//         $array = $model->field('memberid,brandname,hburl')->where($where3)->select();
//         foreach($array as $one){
//             echo $one['memberid'].','.$one['brandname'].','.$one['hburl'].'<br/>';
//         }
//         die;
        $model = new Model('member_ent_brandinfo');
        $array = $model->field('memberid,dir_name,ypurlyuanshi')->limit(20000)->select(); 
        //二级域名   
//         foreach($array as $key=>$one){
//             if(!strpos($one['ypurlyuanshi'],'www') && !empty($one['ypurlyuanshi'])){
//                 $dir = $_SERVER['DOCUMENT_ROOT']."/html/website/".$one['dir_name']."/.htaccess";
//                 $dirrr = $_SERVER['DOCUMENT_ROOT']."/html/website/".$one['dir_name']; 
//                 if(is_dir($dirrr) && !empty($one['dir_name'])){
//                 $file = fopen($dir,'w+');
//                 echo $dir.'<br/>';
//                 $txt = "RewriteEngine On
// RewriteRule ^$ http://www.liansuo.com/p/".$one['memberid']."/ [L,R=301]
// RewriteRule ^scqj.html$ http://www.liansuo.com/p/".$one['memberid']."/scqj.html$1 [L,R=301]
// RewriteRule ^jmys.html$ http://www.liansuo.com/p/".$one['memberid']."/jmys.html$1 [L,R=301]
// RewriteRule ^lrfx.html$ http://www.liansuo.com/p/".$one['memberid']."/jyfx.html$1 [L,R=301]
// RewriteRule ^tpzs.html$ http://www.liansuo.com/p/".$one['memberid']."/cpzs.html$1 [L,R=301]
// RewriteRule ^lxfs.html$ http://www.liansuo.com/p/".$one['memberid']."/lxfs.html$1 [L,R=301] 
// RewriteRule ^jiameng.html$ http://www.liansuo.com/p/".$one['memberid']."/jiameng.html$1 [L,R=301]";
//                 $a = fwrite($file, $txt);
//                 //$b = unlink($dir);  
//                 fclose($file);
//                 } 
//             }else{
//             }
//         }
        
         // html/website/yp/   v-xxxx
//         foreach($array as $key=>$one){
//             if(strpos($one['ypurlyuanshi'],'/v-') && !empty($one['ypurlyuanshi'])){
//                 $dir = $_SERVER['DOCUMENT_ROOT']."/html/website/yp/".$one['dir_name']."/.htaccess";
//                 $dirrr = $_SERVER['DOCUMENT_ROOT']."/html/website/yp/".$one['dir_name'];
//                 if(is_dir($dirrr) && !empty($one['dir_name'])){
//                     $file = fopen($dir,'w+');
//                      echo $dir.'<br/>';
//                     $txt = "RewriteEngine On
//     RewriteRule ^$ http://www.liansuo.com/p/".$one['memberid']."/ [L,R=301]
//     RewriteRule ^scqj.html$ http://www.liansuo.com/p/".$one['memberid']."/scqj.html$1 [L,R=301]
//     RewriteRule ^jmys.html$ http://www.liansuo.com/p/".$one['memberid']."/jmys.html$1 [L,R=301]
//     RewriteRule ^lrfx.html$ http://www.liansuo.com/p/".$one['memberid']."/jyfx.html$1 [L,R=301] 
//     RewriteRule ^tpzs.html$ http://www.liansuo.com/p/".$one['memberid']."/cpzs.html$1 [L,R=301]
//     RewriteRule ^lxfs.html$ http://www.liansuo.com/p/".$one['memberid']."/lxfs.html$1 [L,R=301]
//     RewriteRule ^jiameng.html$ http://www.liansuo.com/p/".$one['memberid']."/jiameng.html$1 [L,R=301]";
//                     $a = fwrite($file, $txt); 
//                     //$b = unlink($dir); 
//                     fclose($file); 
//                     //die; 
//                 }
//             }else{
//             }
//         }
        
/*         foreach($array as $key=>$one){
            if(strpos($one['ypurl'],'/p-') && !empty($one['ypurl'])){
                $dir = $_SERVER['DOCUMENT_ROOT']."/html/hbsite/".$one['dir_name']."/.htaccess";
                $file = fopen($dir,'a+');
                echo $dir;die;
                $txt = "RewriteEngine On
RewriteRule ^$ http://www.liansuo.com/p/".$one['memberid']."/ [L,R=301]
RewriteRule ^scqj.html$ http://www.liansuo.com/p/".$one['memberid']."/scqj.html$1 [L,R=301]
RewriteRule ^jmys.html$ http://www.liansuo.com/p/".$one['memberid']."/jmys.html$1 [L,R=301]
RewriteRule ^lrfx.html$ http://www.liansuo.com/p/".$one['memberid']."/jyfx.html$1 [L,R=301]
RewriteRule ^tpzs.html$ http://www.liansuo.com/p/".$one['memberid']."/cpzs.html$1 [L,R=301]
RewriteRule ^lxfs.html$ http://www.liansuo.com/p/".$one['memberid']."/lxfs.html$1 [L,R=301]";
                $a = fwrite($file, $txt);
                var_Dump($a);
                //$b = unlink($dir);
                die;
            }else{
                echo "dd";
            }
        }   */
    }
}        