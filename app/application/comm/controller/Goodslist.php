<?php
namespace app\comm\controller;
use think\Api;
use think\Db;
class Goodslist 
{
    //定义错误码
    public $errordata=[
                 0=>"成功",
                 1000=>"access_token错误",
                 2000=>"参数错误",
                 3000=>"非法请求",
                 4000=>"其他错误"
                 ];
    //存储jsonpcallback
    public $callback;
    
    public function __construct(){
        $callback=input("get.callback");
       // var_dump($callback);die;
        if(empty($callback))
        {
            $this->errorMsg(3000);
        }
        $this->callback=$callback;
        //var_dump($access_token);die;
        $access_token=input("get.access_token");
        if($access_token!="84d2acebe4a6")
        {
           $this->errorMsg(1000);
        }
    }
    
    //根据类型取得对应商品列表1:最新商品 2:特价商品 3:热卖排行 4:推荐商品
    public function goodslist(){
         $state=input("get.state");
         $limit=input("get.limit",10);
         $goods_arr=DB::name("commend_goods as co")
             ->join("shop_goods go","co.goods_id = go.id")
             ->where("co.commend_id = $state and go.is_del = 0 AND go.id is not null")
             ->field("go.img,go.sell_price,go.name,go.id,go.market_price,go.search_words")
             ->limit($limit)
             ->order("sort asc,id desc")->select();
          //var_dump($goods_arr);die;
          $this->errorMsg(0,$goods_arr);
             
    }


    //根据分类取推荐商品
    public function goodslistcategory(){

        $category_id=input("get.category_id","");
        $limit=input("get.limit",10);
        $reset=input("get.reset",0);
        //var_dump($categroy_id);die;
        $state=input("get.state","");
         if(empty($category_id))
         {
            $this->errorMsg(2000); 
         }
         $category_id=$this->catChild($category_id);
         //var_dump($state);die;
        if(empty($state))
         {
             $all_data=DB::name("category_extend as ca")
             ->join("shop_goods go","go.id = ca.goods_id")
             ->where("ca.category_id in ($category_id) and go.is_del = 0")
             ->field("go.img,go.sell_price,go.name,go.id,go.market_price,go.search_words")
             ->limit($reset,$limit)
             ->order("go.sort asc,go.id desc")->select();
         }
         else
         {

         $all_data=DB::name("category_extend as ca")
             ->join("shop_goods go","go.id = ca.goods_id")
             ->join("shop_commend_goods co","co.goods_id = go.id")
             ->where("ca.category_id in ($category_id) and co.commend_id = $state and go.is_del = 0 and go.store_nums>0") 
             ->field("DISTINCT go.id,go.img,go.sell_price,go.name,go.market_price,go.description,go.search_words,go.sort")
             ->limit($reset,$limit)
             ->order("go.sort asc,go.id desc")->select();
              //var_dump($all_data);die; 
        }
           
        $this->errorMsg(0,$all_data);
    }
    
    //根据品牌获取推荐商品列表1:最新商品 2:特价商品 3:热卖排行 4:推荐商品
    public function goodslistbrand(){
        $brand_id=input("get.brand_id","");
        $reset=input("get.reset",0);
        $limit=input("get.limit",10);
       // var_dump($brand_id);die;
        $state=input("get.state","");
        
         if(empty($brand_id))
         {
            $this->errorMsg(2000); 
         }
         
         //获取所有品盘数据
         $brand_id=$this->getpin($brand_id);
        // var_dump($brand_id);die;
         if(empty($state))
         {
            $all_data=DB::name("goods")->where("brand_id in ($brand_id)")->limit($reset,$limit)->select();
            //var_dump($all_data);die;
         }
         else
         {
            $all_data=DB::name("commend_goods as co")
             ->join("shop_goods go","co.goods_id = go.id")
             ->where("co.commend_id = $state and go.is_del = 0 AND go.id is not null and go.brand_id in ($brand_id)")
             ->field("go.img,go.sell_price,go.name,go.id,go.search_words,go.market_price")
             ->limit($reset,$limit)
             ->order("sort asc,id desc")->select();
         }
         // var_dump($all_data);die; 
        $this->errorMsg(0,$all_data);
    }


    //错误提示
    public function errorMsg($status,$data=""){
      $errordata=array(
            "status"=>$status,
            "message"=>$this->errordata[$status],
        );
      if($status==0&&!empty($data))
      {
        $errordata['data']=$data;
      }
      $errordata=json_encode($errordata,JSON_UNESCAPED_UNICODE);
      //var_dump($errordata);die;
      echo $this->callback."(".$errordata.")"; die;
    }

     //根据分类获取品牌
     public function getpin($category_id){
        $all_data=DB::name("category_extend as ca")
             ->join("goods go","ca.goods_id = go.id")
             ->join("brand b","b.id = go.brand_id")
             ->where("ca.category_id in ('$category_id') and go.is_del = 0 and go.brand_id != 0")
             ->field("DISTINCT b.id,b.name")
             ->select();
         if(empty($all_data))
         {
            return $category_id;
         }
         else{
            $pin_str=$category_id;
            foreach ($all_data as $key => $value) {
                $pin_str.=",".$value['id'];
            }
            
             return $pin_str;
         }  
     }

     //根据分类获取子id
     public function catChild($catId, $level = 1)
    {
        if ($level == 0) {
            return $catId;
        }
        
        $temp = array();
        $result = array(
            $catId
        );
        //var_dump($result);die;
        $catDB = DB::name('category');
        while (true) {
            $id = current($result);
            if (! $id) {
                break;
            }
            $temp = $catDB->where('parent_id = ' . $id)->select();
            // print_r($temp);die;
            foreach ($temp as $key => $val) {
                if (! in_array($val['id'], $result)) {
                    $result[] = $val['id'];
                }
            }
            next($result);
        }
        return join(',', $result);
    }

    public function tellist(){
        $category_id=input("get.category_id");
        $phone_start=input("get.phone_start","");
        $phone_catename=input("get.phone_catename");
        //var_dump($state)

        $where="1=1";
        if(!empty($category_id))  $where.=" and phone_poerator ='$category_id'";
        if(!empty($phone_start))  $where.=" and phone_start ='$phone_start'";
        if(!empty($phone_catename))  $where.=" and phone_catename ='$phone_catename'";
        $data=DB::name("good_catetell")
             ->where($where)
             ->limit(10)
             ->select();
        //var_dump($all_data);die;
        $this->errorMsg(0,$data);
        
    }


}
