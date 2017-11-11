<?php
/**
*Created by sublime.
*user:wy
*date:2017-11-09
*商品分类
*/
namespace app\comm\controller;
use think\Db;
class Sel
{
    public function sel()
    {
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
        $id=$data['id'];
        $sql=DB::table("shop_category")->where("parent_id=$id")->select();
       foreach($sql as $k => &$v)
       {
            $v['images']=substr($v['images'],2);
            $len=$v['images'];
            $v['images']=substr($v['images'],0,$len-2);
       }
        // print_r($sql);die;
        // print_r($sql['4']['images']);die;
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

