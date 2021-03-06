<?php
/**
 * Created by PhpStorm.
 * User: zhangxiao-pc
 * Date: 2016/2/29
 * Time: 15:28
 */

namespace api\controllers;


use common\models\ApiBaseException;;
use common\models\ApiErrorDescs;
use common\models\ApiUtils;
use common\models\Product;
use common\models\Products;
use common\models\TimeUtils;

class ProductController extends ApiBaseController
{
    /*
     *获取产品列表
     */
    public function actionList(){
        try{
            $request = $_REQUEST;
            if(!empty($request['access_token'])&&!empty($request['user_id'])){
                $this->checkAccessToken($request['access_token'], $request['user_id']);
            }
            $type = ApiUtils::getIntParam('type', $request, 1);
            $timer =  new TimeUtils();
            //获取产品列表
            $timer->start('product_list');
            $config = $this->_getConfByType($type);
//            $objPro = new Product($config);
            $objPro = Product::factory($config);
            $productList = $objPro->getList($request, ['borrow_status'  =>  2]);
            $timer->stop('product_list');
            //获取用户已投资产品
//            if(!empty($request['user_id'])){
//                $timer->start('user_invest_product');
//                $userProIds = $objPro->getUserList($request['user_id'], $ids);
//                $timer->stop('user_invest_product');
//            }
//            $list = [];
//            foreach($productList as $key => $val){
//                $val['flag'] = empty($userProIds) || !in_array($key, $userProIds)?0:1;
//                $list[] = $val;
//            }

            $ret = [
                'code' => ApiErrorDescs::SUCCESS,
                'message' => 'success',
                'result' => [
                    'type' => $type,
                    'list' =>  $productList
                ]
            ];
        }catch(ApiBaseException $e){
            $ret = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        header('Content-type: application/json');
        echo json_encode($ret);

        $this->logApi(__CLASS__, __FUNCTION__, $ret);
        \Yii::$app->end();
    }

    private function _getConfByType($type){
        $config = [
//            'type' => $type,
        ];
        switch ($type){
            case 1 :
                $typeConfig = [
                    'class' =>  'common\models\BorrowInfo'
                ];

//                $typeConfig = [
//                    'listModelName' => 'common\models\BorrowInfo',
//                    'userModelName' => 'common\models\BorrowInvest',
//                    'listParams' => 'id, borrow_name, borrow_interest_rate, borrow_duration, borrow_money, borrow_min, repayment_type, has_borrow',
//                    'userParams' => 'borrow_id',
//                    'listCondition' => ['borrow_status'  =>  2],
//                    'userCondition' => ['investor_uid' => '', 'borrow_id' => ''],
//                    'listIndex' => 'id',
//                    'userIndex' => 'borrow_id',
//                ];
                break;
            case 3 :
                $typeConfig = [
                    'class' => '\Fund',
                ];
            default :
                break;
        }
        return $config = array_merge($config, $typeConfig);
    }

    public function actionFundDetail(){
        try{
            $request = $_REQUEST;
            $id = ApiUtils::getIntParam('id', $request);
            $result = [
                'code' => ApiErrorDescs::SUCCESS,
                'message' => 'success',
                'result' => \Fund::getDetail($id),
            ];
        }catch(ApiBaseException $e){
            $result = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        header('Content-type: application/json');
        echo json_encode($result);

        $this->logApi(__CLASS__, __FUNCTION__, $result);
        \Yii::$app->end();
    }
}