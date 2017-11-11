<?php
namespace app\comm\controller;
use \think\Db;
use \think\Cookie;
class Brand
{
    public function brand()
    {

        //接值
        $data=request()->get();
        //print_r($data);die;
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
        $sql=DB::query("select DISTINCT b.id,b.name from shop_category_extend as ca inner join shop_goods as go on ca.goods_id = go.id inner join shop_brand as b on b.id = go.brand_id where ca.category_id in ( $id ) and go.is_del = 0 and go.brand_id != 0  limit 10");
        // print_r($sql);die;
        // $sql=DB::table("shop_category")->where("parent_id=$id")->select();
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
