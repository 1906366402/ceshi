<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 空控制器
 */
class EmptyController extends Controller {
    
    public function index(){
        header('HTTP/1.1 404 Not Fount');
        include('/data/www/www.liansuo.com/header_footer/error.html');die;
    }
    
}