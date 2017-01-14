<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\Member_ent_brandinfoModel;
use Think\Model;

/**
 * 我在测试getDocComment()函数
 */
class FiftyController extends Controller {

    /**
     * 2016年连锁50强首页
     */
    public function index(){
        echo $this->buildHtml('index.html', $_SERVER['DOCUMENT_ROOT'].'/2016/voting/','index');
    }
    
    /**
     * 提名品牌
     */
    public function tmpp(){
        $redislbn = A('Common/Comment');
        $redislbn->getRedis();
        
        $model = new Model('member_ent_info');
        
        $priceArray = $model->query("select a.*,b.* from ls_member_ent_brandinfo as a,ls_member_ent_info as b where 
            a.memberid in (157961,163879,147092,188809,149535,93014,138705,31139,155767,135342) and a.memberid=b.memberid");
        
        $welArray = $model->query("select a.*,b.* from ls_member_ent_brandinfo as a,ls_member_ent_info as b where 
            a.memberid in (161923,149183,112651,185811,186097,143153,175387,1473,23302,1086) and a.memberid=b.memberid");
        
        
        $mouthArray = $model->query("select a.*,b.* from ls_member_ent_brandinfo as a,ls_member_ent_info as b where 
            a.memberid in (153429,45929,148553,112824,112655,136346,26122,179145,174855,138940) and a.memberid=b.memberid");
        
        $newArray = $model->query("select a.*,b.* from ls_member_ent_brandinfo as a,ls_member_ent_info as b where 
            a.memberid in (162469,102847,138704,64610,158093,102480,100437,79870,181871,146881) and a.memberid=b.memberid");
        
        $this->assign('priceArray',$priceArray);
        $this->assign('welArray',$welArray);
        $this->assign('mouthArray',$mouthArray);
        $this->assign('newArray',$newArray);
        $this->display();
        echo $this->buildHtml('tmpp.html', $_SERVER['DOCUMENT_ROOT'].'/2016/voting/','tmpp');
    }
    
    /**
     * 活动介绍
     */
    public function introducte(){ 
        echo $this->buildHtml('introducte.html', $_SERVER['DOCUMENT_ROOT'].'/2016/voting/','introducte');
    }
    
    /**
     * 空方法
     */
    public function _empty(){
        header('HTTP/1.1 404 Not Found');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
    
    /**
     * 异步处理投票数量 
     */
    public function voteNy(){
        $voteModel = new Model('member_liansuo_vote');
        if(isset($_POST['memberid']) && !empty($_POST['memberid'])){
            $ip = $_SERVER['REMOTE_ADDR']."\n\r";
            $file = DOCUMENT_ROOT_DIR.'/vote_ny.txt';
            $arrFile = file($file); 
            $arrFile = str_replace(array("\n\r","","\r","\n"),array('','','',''),$arrFile);
           // if($_SERVER['REMOTE_ADDR']!=='43.227.255.50'){
                $i=1;
                foreach($arrFile as $one){
                    if($_SERVER['REMOTE_ADDR'] == $one){
                        $i++;       
                        if($i>3){
                            echo "亲，明天见!";die;  
                        }
                    } 
                }  
           // }
            
            file_put_contents($file,$ip,FILE_APPEND); 
            $memberid = I('post.memberid');
            $where['memberid'] = $memberid;
            $notArray = $voteModel->where($where)->find();
            if($notArray){ 
                $data2['num'] = $notArray['num']+1;
                $data2['date'] = date('Y-m-d H:i:s');
                $where2['memberid'] = $memberid;
                $updateArray = $voteModel->data($data2)->where($where2)->save();
                if($updateArray){
                    $isArray = $voteModel->where($where2)->find();
                    echo $countVote = $isArray['num'];
                }else{
                    echo "失败"; 
                }
            }else{
                $data['memberid'] = $memberid;
                $data['num'] = 1;
                $data['date'] = date('Y-m-d H:i:s');
                $insertArray = $voteModel->data($data)->add();
                if($insertArray){
                    echo "1";
                }else{
                    echo "失败"; 
                }
            }
        }

//         $arr[0] = $countVote;
//         $arr[1] = $countAll;

        $file = $_SERVER['DOCUMENT_ROOT'].'/searchtest.liansuo.com/Public/js/home/fifty/vote_ny.js';
        file_put_contents($file, "$(\"#numNy_2\").html(4);");  //用于清空文件
        $allArray = $voteModel->field('memberid,num')->select();
        foreach($allArray as $one){
            $memberid2 = $one['memberid'];
            $num2 = $one['num'];
            $a = file_put_contents($file, "$(\"#numNy_$memberid2\").html($num2);",FILE_APPEND);
        }
        $countAll = $voteModel->sum('num');
        file_put_contents($file, "$(\"#numNy_count\").html($countAll);",FILE_APPEND);

    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}