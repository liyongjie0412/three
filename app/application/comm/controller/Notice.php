<?php
/**
*Created by sublime.
*user:wy
*date:2017-11-09
*商品分类
*/
namespace app\index\controller;
use think\Db;
class Notice
{
    public function Notice()
    {
        // echo 1;die;
        //接值
        $data=request()->get();
        // print_r($data);die;
        //定义固定token
        $access_token="84d2acebe4a6";
        //判断token
        if($data['access_token']!=$access_token)
        {
           $arr['status']=1000;
           $arr['message']="access_token错误";
        }
        $call=$_GET['callback'];
        $sql=DB::table("shop_notice")->where("status=0")->select();
        if(empty($sql))
        {
           $arr['status']=2000;
           $arr['message']="参数错误";
        }
        $arr['status']=0;
        $arr['message']="成功";
        $arr['data']=$sql;
        $json=json_encode($arr,JSON_UNESCAPED_UNICODE);
        echo $call."($json)";
    }
}

