<?php
namespace app\comm\controller;
use think\View;
use think\Db;
use think\Controller;
use think\Session;
class Index extends Controller
{
    public $access_token="84d2acebe4a6";
    public $statusArr=['0'=>'成功','1000'=>'Access_token错误','2001'=>'没有传递Access_token参数','2002'=>'参数错误','3000'=>'非法请求（非jsonp请求）'];
    public function __construct()
    {
    	parent:: __construct();
    	//如果不是jsonp请求
    	$call=isset($_GET['callback'])?$_GET['callback']:"";
    	if(empty($call))
    	{
		 $this->message(3000);  //非法请求
    	}    	
    	//如果没有access_token
    	$access_token=isset($_GET['access_token'])?$_GET['access_token']:"";
    	if(empty($access_token))
    	{
		 $this->message(2001);  //没有传递Access_token参数
    	}
          //验证token
          $this->token();
    }

    //验证token
    public function token()
    {
    	  $access_token=isset($_GET['access_token'])?$_GET['access_token']:"";
    	  if($access_token!=$this->access_token)
    	  {
    	  	return $this->message(1000);   //Access_token错误
    	  }
    	
    }
    //返回信息
    public function message($status,$data="")
    {
    	 $call=isset($_GET['callback'])?$_GET['callback']:"";
    	 $message=array('status'=>$status,'message'=>$this->statusArr[$status],'data'=>$data);
 	 $con=json_encode($message);
  	 echo $call."(".$con.")";die;
    }
    //返回商品信息参数
    public function get_goods()
    {    
           //接收信息
    	 $goods_id=isset($_GET['goods_id'])?$_GET['goods_id']:"";
           //商品信息
    	 $goods=db("goods")->where("id='{$goods_id}'")->find();
           $imgs=db("goods_photo_relation as r")->join("goods_photo p","r.photo_id=p.id","LEFT")->limit(3)->where("goods_id='{$goods_id}'")->select();
           $goods['imgs']=$imgs;
           if($imgs)
           {
               $this->message("0",$goods);
           }
           else
           {
                $this->message(2002);
           }
           
    }
    //返回货品信息参数
    public function get_products()
    {    
        //接收信息
        $goods_id=isset($_GET['goods_id'])?$_GET['goods_id']:"";
        $specJSON=isset($_GET['specJSON'])?$_GET['specJSON']:"";
        //货品信息
        $products=db("products")->where("goods_id='{$goods_id}' and spec_array='{$specJSON}'")->find();
        $this->message("0",$products);

    }
}
