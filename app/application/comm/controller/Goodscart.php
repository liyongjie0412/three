<?php
/**
 * Created by PhpStorm.
 * User: lll
 * Date: 2017/11/10
 * Time: 11:48
 */
namespace app\comm\controller;
use think\Api;
use think\Db;
class Goodscart
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
    /**
     * [goodscart description]
     * @param $cartstr   字符串
     * @param $id         用户id
     * @return [type] [description]
     */
    public function goodscart($id="372",$content="")
    { 
      $cartValue=json_decode(str_replace(array('&','$'),array('"',','),$content),true);
      // $cartValue=Array ('goods'=> Array ( ),'product' => Array ('852' => 76,'351' => 1) ) ;
      // print_r($cartValue);die;
      $buyInfo=$this->cartFormat($cartValue,$id);
          // print_r($buyInfo);die;
      $this->errorMsg(0,$buyInfo);
    }
    public function num($id="372",$count,$pro_id,$content=""){
   $cartValue=json_decode(str_replace(array('&','$'),array('"',','),$content),true);
      foreach ($cartValue['product'] as $k => $v) {
         if($k==$pro_id){
          $cartValue['product'][$k]=$count;
         }else{
          $cartValue['product'][$pro_id]=$count;
         }
      }
      $buyInfo=$this->cartFormat($cartValue,$id);
      $buyInfo['jsonstr']=str_replace(array('"',','),array('&','$'),json_encode($cartValue));
      $this->errorMsg(0,$buyInfo);
    }
  /**
   * [errorMsg description]
   * @param  [type] $status [description]
   * @param  string $data   [description]
   * @return [type]         [description]
   */
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
    /**
     * @param $cartValue
     * @param $id
     * @return array
     */
        public function cartFormat($cartValue,$id="")
    {
         $cartExeStruct = array('goods' => array('id' => array(), 'data' => array() ),'product' => array( 'id' => array() , 'data' => array()),'count' => 0,'sum' => 0);
        $result = $cartExeStruct;
        $prices = 'sell_price';
        $goodsIdArray = array();
                    if(isset($cartValue['product']) && $cartValue['product'])
                    {
                        $productIdArray          = array_keys($cartValue['product']);
//                        print_r($productIdArray);die;
                        $result['product']['id'] = $productIdArray;

                        $productObj     = DB::name('products');
                        $productData    = $productObj->where('id in ('.join(",",$productIdArray).')','id,goods_id,'.$prices.'')->select();
                       // print_r($productData);die;
                        foreach($productData as $proVal)
                        {
                            $result['product']['data'][$proVal['id']] = array(
                                'id'         => $proVal['id'],
                                'type'       => 'product',
                                'goods_id'   => $proVal['goods_id'],
                                'count'      => $cartValue['product'][$proVal['id']],
                                "$prices" => $proVal[$prices],
                            );
                            if(!in_array($proVal['goods_id'],$goodsIdArray))
                            {
                                $goodsIdArray[] = $proVal['goods_id'];
                            }
                            //购物车中的种类数量累加
                            $result['count'] += $cartValue['product'][$proVal['id']];
                        }
                       // print_r($result);die;
                    }

       // print_r($result);die;
                    if($goodsIdArray)
                    {
                        // echo 1;die;
                        $goodsArray = array();

                        $goodsObj   = DB::name('goods');
                        $goodsData  = $goodsObj->where('id in ('.join(",",$goodsIdArray).')','id,name,img,'.$prices.'')->select();
                        // print_r($goodsData);die;
                        foreach($goodsData as $goodsVal)
                        {
                            $goodsArray[$goodsVal['id']] = $goodsVal;
                        }

                        foreach($result['goods']['data'] as $key => $val)
                        {
                            if(isset($goodsArray[$val['goods_id']]))
                            {
                                $result['goods']['data'][$key]['img']        = 0;
                                $result['goods']['data'][$key]['name']       = $goodsArray[$val['goods_id']]['name'];
                                $result['goods']['data'][$key][$prices] = $goodsArray[$val['goods_id']][$prices];

                                //购物车中的金额累加
                                $result['sum']   += $goodsArray[$val['goods_id']][$prices] * $val['count'];
                            }
                        }

                        foreach($result['product']['data'] as $key => $val)
                        {
                            if(isset($goodsArray[$val['goods_id']]))
                            {
                                $result['product']['data'][$key]['img']  = 0;
                                $result['product']['data'][$key]['name'] = $goodsArray[$val['goods_id']]['name'];

                                //购物车中的金额累加
                                $result['sum']   += $result['product']['data'][$key][$prices] * $val['count'];
                            }
                        }
                    }

       return  $this->goodsCount($result,$id);
    }

    /**
     * @param $buyInfo
     * @param $id
     * @param string $promo
     * @param string $active_id
     * @return array|string
     */
        public function goodsCount($buyInfo,$id,$promo='',$active_id='')
    {
        $this->sum           = 0;       //原始总额(优惠前)
        $this->final_sum     = 0;       //应付总额(优惠后)
        $this->weight        = 0;       //总重量
        $this->reduce        = 0;       //减少总额
        $this->count         = 0;       //总数量
        $this->promotion     = array(); //促销活动规则文本
        $this->proReduce     = 0;       //促销活动规则优惠额
        $this->point         = 0;       //增加积分
        $this->exp           = 0;       //增加经验
        $this->freeFreight   = array(); //商家免运费
        $this->tax           = 0;       //商品税金
        $this->seller        = array(); //商家商品总额统计, 商家ID => 商品金额

        // $user_id      =265;
        // $group_id     ="";
        $goodsList    = array();
        $productList  = array();
        $prices = 'sell_price';

/*Product 拼装商品优惠价的数据*/
if(isset($buyInfo['product']['id']) && $buyInfo['product']['id'])
{
    // echo 1;die;
    //购物车中的货品数据
    $productIdStr = join(',',$buyInfo['product']['id']);
    $productObj   = Db::table('shop_products')->alias("pro")->join("goods go","go.id = pro.goods_id");
    $productObj->where('pro.id in ('.$productIdStr.') and go.id = pro.goods_id');
    $productObj->field('pro.'.$prices.',pro.weight,pro.id as product_id,pro.spec_array,pro.goods_id,pro.store_nums,pro.products_no as goods_no,go.name,go.point,go.exp,go.img,go.seller_id');
    // print_r($productObj);die;
    $productList  = $productObj->select();
    // print_r($productList);die;
    //开始优惠情况判断
    foreach($productList as $key => $val)
    {
        // echo $val['product_id'];die;
        // print_r($buyInfo);die;
        //检查库存
        if($buyInfo['product']['data'][$val['product_id']]['count'] <= 0 || $buyInfo['product']['data'][$val['product_id']]['count'] > $val['store_nums'])
        {

            return "货品：".$val['name']."购买数量超出库存，请重新调整购买数量";
        }
        $productList[$key]['count']  = $buyInfo['product']['data'][$val['product_id']]['count'];
        $current_sum_all             = $productList[$key][$prices]  * $productList[$key]['count'];

        //全局统计
        $this->weight += $val['weight'] * $productList[$key]['count'];
        $this->point  += $val['point']  * $productList[$key]['count'];
        $this->exp    += $val['exp']    * $productList[$key]['count'];
        $this->sum    += $current_sum_all;
        $this->count  += $productList[$key]['count'];

    }
}

$this->final_sum = $this->sum - $this->reduce - $this->proReduce;
return array(
    'final_sum'  => $this->final_sum,
    'promotion'  => $this->promotion,
    'proReduce'  => $this->proReduce,
    'sum'        => $this->sum,
    'goodsList'  => array_merge($goodsList,$productList),
    'count'      => $this->count,
    'reduce'     => $this->reduce,
    'weight'     => $this->weight,
    'point'      => $this->point,
    'exp'        => $this->exp,
    'tax'        => $this->tax,
    'fani'       => $this->getGroupPrice($id,$this->final_sum),
);
}

    /**
     * @param $id
     * @param string $price
     * @return float|string
     */
    public function getGroupPrice($id,$price="")
    {
        // echo $id;die
        // 1,根据用户id查出用户VIP等级;
        // $user=DB::name("member")->where("user_id=".$id)->find();
        // $pr="";
        // if($user['level_id']!=0){
        // // 2，查出vip返利比率
        // $fanli=DB::name("rebate")->where("vip_or_seller=2")->select();
        // // 3,根据会员折扣率计算商品返利
        // foreach ($fanli as $key => $value) {
        //     // echo $fanli[$key]['vip_or_seller'];
        //     if($value['pebate_lv']==$user['level_id']){
        //       $pr=$price*$value['rebate']/100;
        //         continue;
        //     }
        // }
        // }
        return 0;
    }

}
