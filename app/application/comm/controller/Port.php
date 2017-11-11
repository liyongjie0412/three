<?php
/**
 * Created by PhpStorm.
 * User: lfa
 * Date: 2017/11/8
 * Time: 15:56
 */
namespace app\comm\controller;
use think\Db;

class Port
{
    private $access_token="84d2acebe4a6";

    /**
     * 构造方法
     */
    public function __construct()
    {
     $this->access();
    }

    /**
     * @data 获取access
     */
    private function access(){
        @$access_token =$_REQUEST['access_token'];
        if(empty($this->access_token)){
            $con = json_encode(['status'=>1000,'message'=>"access_token为空"],JSON_UNESCAPED_UNICODE);
            @$call=$_GET['callback'];
            echo $call."(".$con.")";
            die;
        }
        if($access_token !=$this->access_token){
            $con =json_encode(['status'=>1001,'message'=>'access_token错误'],JSON_UNESCAPED_UNICODE);
            @$call=$_GET['callback'];
            echo $call."(".$con.")";
            die;
        }

    }

    /**
     * @data array() status message data
     */
    public function index(){
        @$call=$_GET['callback'];
        @$location=$_GET['location'];
        if($location =="index"){
            $rest = Db::query("select name,link,content from shop_ad_manage where location =0 limit 3");
        }else{
            $rest = Db::query("select name,link,content from shop_ad_manage where location !=0 limit 3");
        }
        if($rest){
            $con =json_encode(['status'=>0,'message'=>'请求成功','data'=>$rest],JSON_UNESCAPED_UNICODE);
        }else{
            $con =json_encode(['status'=>2001,'message'=>'请求失败（没有数据）','data'=>""],JSON_UNESCAPED_UNICODE);
        }
        echo $call."(".$con.")";

    }

}
