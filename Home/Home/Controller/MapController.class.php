<?php
namespace Home\Controller;

use Think\Controller;
use Think\Model;

/**
 * 新闻页面
 */

/*
 * SiteMap接口类
 */
class MapController extends Controller
{

    private static $baseURL = ''; 
    // URL地址
    private static $newsMobileUrl = 'http://m.liansuo.com/news/';
    // 问答移动版地址
    private static $newsPcUrl = "http://www.liansuo.com/news/";
    // 问答pc地址
    private static $newsZonePcUrl = "http://www.xxx.cn/ask/jingxuan/";
    // 问答精选Pc链接
    private static $newsZoneMobileUrl = "http://m.xxx.cn/ask/jx/";
    // 问答精选移动版链接
    // 问答setmaps
    public function newsSetMap()
    {	
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 10000; // 数据库每次取数量
        $maxXml = 10000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_news_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/news-liansuo-com/liansuo.com.news.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('news_arc');
		$list=$newsm->field('newsid,url,outsideurl,ctime,member_id')->where(' flag=2 and (iscreatehtml>0 or is_jump =\'1\' ) and newsid >'.$maxid)->order('newsid asc')->limit($psize)->select();
		//echo $newsm->getLastSQL();die;
		//print_r($list);die; 
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		$xml .= $this->newsMapPcUrl(array('lastmod'=>date('Y-m-d'),'pcurl'=>'http://www.liansuo.com/news/')); // 移动版			
		$xml .= $this->newsMapMobileUrl(array('lastmod'=>date('Y-m-d'),'mobielurl'=>'http://m.liansuo.com/news/')); // pc版
        foreach ($list as $k => $qinfo) {
			if(stripos($qinfo['url'],'http://.liansuo.com')!==false){
				continue;
			}
			if(stripos($qinfo['url'],'liansuo.com')===false&&stripos($qinfo['outsideurl'],'liansuo.com')===false){
				continue;
			}
            if ($k == 0)
               $indexArr['minid'] = $qinfo['newsid'];
            $qinfo['lastmod'] = substr($qinfo['ctime'], 0, 10);
           
            //$qinfo['pcurl'] = self::$newsPcUrl . $qinfo['newsid'] . '.html'; // pc版链接
			if(!empty($qinfo['outsideurl'])){
				//print_r($qinfo);die();
				 $qinfo['pcurl'] = $qinfo['outsideurl']; // pc版链接
				 $qinfo['mobielurl'] = $qinfo['outsideurl']; // pc版链接
			}else{
				 $qinfo['pcurl'] = $qinfo['url']; // pc版链接
				 if($qinfo['member_id']){
					 $qinfo['mobielurl'] = 'http://m.liansuo.com/p/'. $qinfo['member_id'] . '/'.$qinfo['newsid'].'.html'; // 移动版链接
				 }else{
					 $qinfo['mobielurl'] = 'http://m.liansuo.com/news/'.$qinfo['newsid'] . '.html'; // 移动版链接
				 }				 
			}
			//$qinfo['pcurl'] = $qinfo['url']; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
            $xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['newsid'];
		//print_r($maxid);die; 
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/news-liansuo-com/0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/news-liansuo-com/0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function unitMSetMap()  
    {
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 5000; // 数据库每次取数量
        $maxXml = 5000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_unit_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/unit-liansuo-com/unti.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('news_keywords');
		$list=$newsm->field('aid as id')->where('aid>'.$maxid)->order('id asc')->limit($psize)->select();
		//echo $newsm->getLastSQL();die;
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		$xml .= $this->newsMapMobileUrl(array('mobielurl'=>'http://m.liansuo.com/unit/')); // 移动版
        $xml .= $this->newsMapPcUrl(array('pcurl'=>'http://www.liansuo.com/unit/')); // pc版
        foreach ($list as $k => $qinfo) {		
			if ($k == 0){
               $indexArr['minid'] = $qinfo['id'];
			}
			$qinfo['pcurl'] = 'http://www.liansuo.com/unit/'.$qinfo['id'].'.html'; // pc版链接
			$qinfo['mobielurl'] = 'http://m.liansuo.com/unit/'.$qinfo['id'].'.html'; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
            $xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['id'];		
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/unit-liansuo-com/m_0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/unit-liansuo-com/m_0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function qaMSetMap()  
    {
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 3000; // 数据库每次取数量
        $maxXml = 3000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_qa_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/qa-liansuo-com/qa.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('qa_ask');
		$list=$newsm->field('askid')->where('askid>'.$maxid)->order('askid asc')->limit($psize)->select();
		//echo $newsm->getLastSQL();die;
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		$xml .= $this->newsMapMobileUrl(array('mobielurl'=>'http://m.liansuo.com/qa/index.html')); // 移动版
        $xml .= $this->newsMapPcUrl(array('pcurl'=>'http://www.liansuo.com/qa/index.html')); // pc版
        foreach ($list as $k => $qinfo) {		
			if ($k == 0){
               $indexArr['minid'] = $qinfo['askid'];
			}
			$qinfo['pcurl'] = 'http://www.liansuo.com/qa/'.$qinfo['askid'].'.html'; // pc版链接
			$qinfo['mobielurl'] = 'http://m.liansuo.com/qa/'.$qinfo['askid'].'.html'; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
            $xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['askid'];		
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/qa-liansuo-com/m_0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/qa-liansuo-com/m_0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function pMSetMap()  
    {
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 10000; // 数据库每次取数量
        $maxXml = 10000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_p_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/p-liansuo-com/p.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('member_ent_brandinfo');
		$list=$newsm->field('memberid')->where("updatetime <>'0000-00-00 00:00:00' and memberid>".$maxid." and cindex>0 and ypurl like 'http://www.liansuo.com/p/%' ")->order('memberid asc')->limit($psize)->select();	
		//echo $newsm->getLastSQL();die; 
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		//$xml .= $this->newsMapMobileUrl(array('mobielurl'=>'http://m.liansuo.com/qa/index.html')); // 移动版
        //$xml .= $this->newsMapPcUrl(array('pcurl'=>'http://www.liansuo.com/qa/index.html')); // pc版
        foreach ($list as $k => $qinfo) {		
			if ($k == 0){
               $indexArr['minid'] = $qinfo['askid'];
			}
			$qinfo['pcurl'] = 'http://www.liansuo.com/p/'.$qinfo['memberid'].'/'; // pc版链接
			$qinfo['mobielurl'] = 'http://m.liansuo.com/p/'.$qinfo['memberid'].'/'; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
            $xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['memberid'];		
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/p-liansuo-com/m_0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/p-liansuo-com/m_0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function baodaoSetMap()  
    {
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 10000; // 数据库每次取数量
        $maxXml = 10000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_baodao_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/baodao-liansuo-com/p.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('member_ent_brandinfo');
		$list=$newsm->field('memberid')->where("updatetime <>'0000-00-00 00:00:00' and memberid>".$maxid." and cindex>0 and ypurl like 'http://www.liansuo.com/baodao/%' ")->order('memberid asc')->limit($psize)->select();	
		//echo $newsm->getLastSQL();die; 
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		//$xml .= $this->newsMapMobileUrl(array('mobielurl'=>'http://m.liansuo.com/qa/index.html')); // 移动版
        //$xml .= $this->newsMapPcUrl(array('pcurl'=>'http://www.liansuo.com/qa/index.html')); // pc版
		//print_r($list);die;
        foreach ($list as $k => $qinfo) {		
			if ($k == 0){
               $indexArr['minid'] = $qinfo['askid'];
			}
			$qinfo['pcurl'] = 'http://www.liansuo.com/baodao/'.$qinfo['memberid'].'.html'; // pc版链接
			$qinfo['mobielurl'] = 'http://m.liansuo.com/baodao/'.$qinfo['memberid'].'.html'; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
            $xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['memberid'];		
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/baodao-liansuo-com/m_0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/baodao-liansuo-com/m_0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	 public function newsMSetMap() 
    {	
		header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 10000; // 数据库每次取数量
        $maxXml = 10000; // xml写入记录数量 
        $where = array();
        // 读取索引文件
        $index = APP_PATH .'../../Web/sitemap/cahe_m_news_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH.'../../Web/sitemap/news-liansuo-com/m.liansuo.com.news.xml';
		//echo $index;die;
		//echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        //$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		$newsm=  M('news_arc');
		$list=$newsm->field('newsid,url,outsideurl,ctime')->where(' flag=2 and (iscreatehtml>0 or is_jump =\'1\' ) and newsid >'.$maxid)->order('newsid asc')->limit($psize)->select();
		//echo $newsm->getLastSQL();die;
		//print_r($list);die; 
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
       // $start .= "</urlset>";
		$xml .= $this->newsMapPcUrl(array('lastmod'=>date('Y-m-d'),'pcurl'=>'http://www.liansuo.com/news/')); // 移动版			
		$xml .= $this->newsMapMobileUrl(array('lastmod'=>date('Y-m-d'),'mobielurl'=>'http://m.liansuo.com/news/')); // pc版
        foreach ($list as $k => $qinfo) {
			if(stripos($qinfo['url'],'http://.liansuo.com')!==false){
				continue;
			}
			if(stripos($qinfo['url'],'liansuo.com')===false&&stripos($qinfo['outsideurl'],'liansuo.com')===false){
				continue;
			}
            if ($k == 0)
               $indexArr['minid'] = $qinfo['newsid'];
            $qinfo['lastmod'] = substr($qinfo['ctime'], 0, 10);
           
            //$qinfo['pcurl'] = self::$newsPcUrl . $qinfo['newsid'] . '.html'; // pc版链接
			if(!empty($qinfo['outsideurl'])){
				//print_r($qinfo);die();
				 $qinfo['pcurl'] = $qinfo['outsideurl']; // pc版链接
				 $qinfo['mobielurl'] = $qinfo['outsideurl']; // pc版链接
			}else{
				 $qinfo['pcurl'] = $qinfo['url']; // pc版链接
				 $qinfo['mobielurl'] = self::$newsMobileUrl . $qinfo['newsid'] . '.html'; // 移动版链接
			}
			//$qinfo['pcurl'] = $qinfo['url']; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版
           //$xml .= $this->newsMapPcUrl($qinfo); // pc版
			//echo $xml;die;
        }		
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['newsid'];
		//print_r($maxid);die; 
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/news-liansuo-com/m_0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {
                    $newContent .= $v;
                }				
				//echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH .'../../Web/sitemap/news-liansuo-com/m_0' . $filename . '.xml';  
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function tagSetMap()
    {
        header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        $maxid = 0; // 索引文件最大id
        $minid = 0; // 索引文件最小id
        $psize = 5000; // 数据库每次取数量
        $maxXml = 5000; // xml写入记录数量
        $where = array();
        // 读取索引文件
        $index = APP_PATH . '../../Web/sitemap/cahe_tag_Index.txt';
        // 关联setmaps路径
        $askXml = APP_PATH . '../../Web/sitemap/tag-liansuo-com/liansuo.com.tag.xml';
        // echo $index;die;
        // echo $askXml;die;
        if (! file_exists($index)) {
            $fp = fopen("$index", "w+");
            if (! is_writable($index)) {
                die("文件:" . $index . "不可写，请检查！");
            }
            fclose($fp);
        } else {
            // index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
            $fp = file($index);
            $string = $fp[count($fp) - 1]; // 显示最后一行
            $arr = explode(',', $string);
        }
        // 索引文件数量是否小于$maxXml
        // 如果为第一次运行
        if (! $arr[1]) {
            $bs = 1;
            $filename = 1;
        } else {
            if ($arr && $arr[3] < $maxXml) {
                $filename = $arr[0];
                $psize = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
                $bs = 0;
            } else {
                $filename = $arr[0] + 1;
                $bs = 1;
            }
        }
        $maxid = empty($arr[1]) ? 0 : $arr[1];
        $minid = empty($arr[2]) ? 0 : $arr[2];
        echo "文件名称：" . $filename . ".xml" . "<br/ >";
        echo "最大id:" . $maxid . "<br />";
        echo "最小id:" . $minid . "<br />";
        echo "xml写入最大记录：" . $maxXml . "<br />";
        echo "数据库每次读取数量：" . $psize . "<br />";
        // $list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
        $newsm = M('seo_tagindex');
        $list = $newsm->field('id')
            ->where(' id > ' . $maxid)
            ->order('id asc')
            ->limit($psize)
            ->select();
       // echo $newsm->getLastSQL();
       // die();
        //print_r($list);die;
        if (count($list) <= 0) {
            echo 1;
            exit();
        }
        $record = $arr[3] + count($list); // 索引文件写入记录数
        $indexArr = array(
            'filename' => $filename,
            'maxid' => $maxid,
            'minid' => $minid,
            'maxXml' => $record
        );
        $start = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $start .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
        // $start .= "</urlset>";
		$xml .= $this->newsMapPcUrl(array('lastmod'=>date('Y-m-d'),'pcurl'=>'http://www.liansuo.com/tag/')); // 移动版			
		$xml .= $this->newsMapMobileUrl(array('lastmod'=>date('Y-m-d'),'mobielurl'=>'http://m.liansuo.com/tag/')); // pc版
        foreach ($list as $k => $qinfo) {
            $qinfo['pcurl'] = 'http://www.liansuo.com/tag/'.$qinfo['id'].'/'; // pc版链接
            $qinfo['mobielurl'] = 'http://m.liansuo.com/tag/'.$qinfo['id'].'/'; // 移动版链接
           if ($k == 0)
            $indexArr['minid'] = $qinfo['id'];
            $qinfo['lastmod'] = date('Y-m-d');
            // $qinfo['pcurl'] = $qinfo['url']; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版			
			$xml .= $this->newsMapPcUrl($qinfo); // pc版
                                                     // echo $xml;die;
        }
        $maxid = end($list);
        $indexArr['maxid'] = $maxid['id'];
        // print_r($maxid);die;
        // 更新索引文件
        if ($bs == 0) {
            // 更新最后一行
            $txt = file($index);
            $txt[count($txt) - 1] = $indexArr['filename'] . ',' . $indexArr['maxid'] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
            $str = join($txt);
            if (is_writable($index)) {
                if (! $handle = fopen($index, 'w')) {
                    echo "不能打开文件 $index";
                    exit();
                    exit();
                }
                if (fwrite($handle, $str) === FALSE) {
                    echo "不能写入到文件 $index";
                    exit();
                    exit();
                }
                echo "成功地写入文件$index";
                fclose($handle);
            } else {
                echo "文件 $index 不可写";
                exit();
            }
            fclose($index);
        } elseif ($bs == 1) {
            // 新加入一行
            $fp = fopen($index, 'a');
            $num = count($list);
            $string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
            if (fwrite($fp, $string) === false) {
                echo "追加新行失败。。。";
                exit();
            } else {
                echo "追加成功<br />";
                // 更新sitemap索引文件
                $xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
                $xmlData .= "<sitemapindex>" . chr(10);
                $xmlData .= "</sitemapindex>";
                if (! file_exists($askXml))
                    file_put_contents($askXml, $xmlData);
                $fileList = file($askXml);
                $fileCount = count($fileList);
                $setmapxml = "http://sitemap.liansuo.com/tag-liansuo-com/0{$filename}.xml"; // 正常问题链接
                $txt = $this->setMapIndex($setmapxml);				
                $fileList[$fileCount - 1] = $txt . "</sitemapindex>";
                $newContent = '';
                foreach ($fileList as $v) {					
                    $newContent .= $v;
                }
                //echo $askXml;die;
                if (! file_put_contents($askXml, $newContent))
                    exit('无法写入数据');
                echo '已经写入文档' . $askXml;
            }
            fclose($fp);
        }
        $filename = APP_PATH . '../../Web/sitemap/tag-liansuo-com/0' . $filename . '.xml';
        // 更新到xml文件中,增加结尾
        if (! file_exists($filename))
            file_put_contents($filename, $start);
        $xmlList = file($filename);
        $xmlCount = count($fileList);
        $xmlList[$xmlCount - 1] = $xml . "</urlset>";
        $newXml = '';
        foreach ($xmlList as $v) {
            $newXml .= $v;
        }
        if (! file_put_contents($filename, $newXml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function listSetMap()
    {
        header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        // $list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
        $newsm = M('news_category');    
		$typearr=array('hydt','czxx','rwzf','jdal','xmzx','rcjy','xzcb','jmzn');
        $templist = $newsm->field('categorydir')
            ->where(' categorydir <>\'\' and categorydir<>"zhanhui" ')            
            ->select();
		$list=array();
		$i=0;
		foreach($templist as $one){
			foreach($typearr as $two){
				$list[$i]['pcurl']='http://www.liansuo.com/'.$one['categorydir']."/$two/";
				$list[$i]['mobielurl']='http://m.liansuo.com/'.$one['categorydir']."/$two/";
				$i++;
			}
		}
        $xml = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
        // $start .= "</urlset>";
		///首页
        foreach ($list as $k => $qinfo) {
            $qinfo['pcurl'] = str_replace(array('//','http:/'),array('/','http://'),$qinfo['pcurl']); // pc版链接
            $qinfo['mobielurl'] = str_replace(array('//','http:/'),array('/','http://'),$qinfo['mobielurl']); // 移动版链接                    
            $qinfo['lastmod'] = date('Y-m-d');
            // $qinfo['pcurl'] = $qinfo['url']; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版			
			$xml .= $this->newsMapPcUrl($qinfo); // pc版
                                                     // echo $xml;die;
        }
        $xml.='</urlset>'; 
		$filename = APP_PATH . '../../Web/sitemap/list-liansuo-com/list.xml';
        if (! file_put_contents($filename, $xml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
	public function projectSetMap()
    {
        header('Content-type:text/html;charset=utf-8');
        // 获取问题列表
        // $list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$templist=$Model->query('SELECT a.`memberid`,a.ypurl FROM `ls_member_ent_brandinfo` as a left join ls_member_ent_info as b on a.memberid=b.memberid left join ls_member_base as c on a.memberid=c.memberid where  c.`delstatus`=\'1\' and b.status=1 order by a.memberid asc');  
		//die();
		$list=array();
		$i=0;
		//print_r($templist);die;
		foreach($templist as $one){			
				$list[$i]['pcurl']='http://www.liansuo.com/p'.$one['memberid']."/";
				$list[$i]['mobielurl']='http://m.liansuo.com/p'.$one['memberid']."/";
				$i++;			
		}
        $xml = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
        // $start .= "</urlset>";
		///首页
        foreach ($list as $k => $qinfo) {
            $qinfo['pcurl'] = str_replace(array('//','http:/'),array('/','http://'),$qinfo['pcurl']); // pc版链接
            $qinfo['mobielurl'] = str_replace(array('//','http:/'),array('/','http://'),$qinfo['mobielurl']); // 移动版链接                    
            $qinfo['lastmod'] = date('Y-m-d');
            // $qinfo['pcurl'] = $qinfo['url']; // pc版链接
            $xml .= $this->newsMapMobileUrl($qinfo); // 移动版			
			$xml .= $this->newsMapPcUrl($qinfo); // pc版
                                                     // echo $xml;die;
        }
        $xml.='</urlset>'; 
		$filename = APP_PATH . '../../Web/sitemap/list-liansuo-com/project.xml';
        if (! file_put_contents($filename, $xml))
            exit("写入数据错误");
        else
            echo "写入数据成功<br />";
    }
    // 问答移动版xml
    private function newsMapMobileUrl($data)
    {
        $xml = '';
        if (is_array($data) && ! empty($data)) {
            $xml .= "<url>" . chr(10);
            $xml .= '<loc>' . $data['mobielurl'] . '</loc>' . chr(10); // 移动版链接
            $xml .= "<mobile:mobile type=\"mobile\"/>" . chr(10);
            if ($data['lastmod'])
                $xml .= '<lastmod>' . $data['lastmod'] . '</lastmod>' . chr(10);
            $xml .= '<changefreq>daily</changefreq>' . chr(10);
            $xml .= '<priority>0.8</priority>' . chr(10);
            $xml .= "</url>" . chr(10);
			//echo $xml;die;
            return $xml;
        }
    }
    // 问答pc版xml
    private function newsMapPcUrl($data)
    {
        $xml = '';
        if (is_array($data) && ! empty($data)) {
            $xml .= '<url>' . chr(10);
            $xml .= '<loc>' . $data['pcurl'] . '</loc>' . chr(10); // pc版链接
            if ($data['lastmod'])
                $xml .= '<lastmod>' . $data['lastmod'] . '</lastmod>' . chr(10);
            $xml .= '<changefreq>daily</changefreq>' . chr(10);
            $xml .= '<priority>0.8</priority>' . chr(10);
            $xml .= '</url>' . chr(10);
            return $xml;
        }
    }
    // setmaps索引文件
    private function setMapIndex($filename)
    {
        $xml = '';
        $xml .= "<sitemap>" . chr(10);
        $xml .= "<loc>{$filename}</loc>" . chr(10);
        $xml .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>" . chr(10);
        $xml .= "</sitemap>" . chr(10);
        return $xml;
    }
	public function listc(){
		die;
	}
}
?> 