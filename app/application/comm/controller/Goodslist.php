<?php
namespace app\comm\controller;
use think\Api;
use think\Db;
class Goodslist
{
    public function goodslist($cat_id="")
    {  
       $cat_id=$this->catChild(158);
       print_r($cat_id);die;
    }
    // cat_id
     public static function catChild($catId, $level = 1)
    {
        if ($level == 0) {
            return $catId;
        }
        
        $temp = array();
        $result = array(
            $catId
        );
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
        public function cartFormat($cartValue)
    {
//        print_r($cartValue);die;
        //初始化结果
        $result = $this->cartExeStruct;

        $goodsIdArray = array();

        $user_rank = ISafe::get('user_rank');
        if($user_rank==1||$user_rank==2){
            $prices = 'sell_price';
        }else{
            $prices = 'market_price';
        }
//        print_r($goodsIdArray);die;
                    if(isset($cartValue['goods']) && $cartValue['goods'])
                    {
//                        print_r($cartValue);die;
                        $goodsIdArray = array_keys($cartValue['goods']);

                        $result['goods']['id'] = $goodsIdArray;
                        foreach($goodsIdArray as $gid)
                        {
                            $result['goods']['data'][$gid] = array(
                                'id'       => $gid,
                                'type'     => 'goods',
                                'goods_id' => $gid,
                                'count'    => $cartValue['goods'][$gid],
                            );

                            //购物车中的种类数量累加
                            $result['count'] += $cartValue['goods'][$gid];
                        }
                    }
//        print_r($cartValue);die;
                    if(isset($cartValue['product']) && $cartValue['product'])
                    {
                        $productIdArray          = array_keys($cartValue['product']);
//                        print_r($productIdArray);die;
                        $result['product']['id'] = $productIdArray;

                        $productObj     = new IModel('products');
                        $productData    = $productObj->query('id in ('.join(",",$productIdArray).')','id,goods_id,'.$prices.'');
//                        print_r($productData);die;
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
//                        print_r($result);die;
                    }
                    if($goodsIdArray)
                    {
                        $goodsArray = array();

                        $goodsObj   = new IModel('goods');
                        $goodsData  = $goodsObj->query('id in ('.join(",",$goodsIdArray).')','id,name,img,'.$prices.'');
                        foreach($goodsData as $goodsVal)
                        {
                            $goodsArray[$goodsVal['id']] = $goodsVal;
                        }

                        foreach($result['goods']['data'] as $key => $val)
                        {
                            if(isset($goodsArray[$val['goods_id']]))
                            {
                                $result['goods']['data'][$key]['img']        = Thumb::get($goodsArray[$val['goods_id']]['img'],120,120);
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
                                $result['product']['data'][$key]['img']  = Thumb::get($goodsArray[$val['goods_id']]['img'],120,120);
                                $result['product']['data'][$key]['name'] = $goodsArray[$val['goods_id']]['name'];

                                //购物车中的金额累加
                                $result['sum']   += $result['product']['data'][$key][$prices] * $val['count'];
                            }
                        }
                    }

        return $result;
    }
}
