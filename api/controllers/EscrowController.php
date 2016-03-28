<?php
/**
 * Created by PhpStorm.
 * User: zhangxiao-pc
 * Date: 2016/3/21
 * Time: 15:49
 */

namespace api\controllers;


use common\models\ApiBaseException;
use common\models\ApiErrorDescs;
use common\models\ApiUtils;
use common\models\Escrow;
use common\models\EscrowAccount;
use common\models\Members;
use common\models\TimeUtils;
use yii\web\Response;

class EscrowController extends UserBaseController
{
    public function behaviors()
    {
        \Yii::$app->response->format=Response::FORMAT_JSON;
        return parent::behaviors(); // TODO: Change the autogenerated stub
    }

    /*
     * 第三方账户开通
     */
    public function actionRegisterBind(){
        try{
            $request = $_REQUEST;
            $userId = ApiUtils::getIntParam('user_id', $request);
            $timer = new TimeUtils();

            $timer->start('user_third_bind');
            $userBind = EscrowAccount::getUserBindInfo($userId);
            $timer->stop('user_third_bind');

            if(empty($userBind['qddBind'])){
                $userInfo = Members::get($userId);
                $objEsc = new Escrow();
                $qddRegParams = $objEsc->registerAccount($userInfo['user_name']);
                if(empty($qddRegParams)){
                    throw new ApiBaseException(ApiErrorDescs::ERR_QDD_REGISTER_PARAMS_ERR);
                }
                $result = [
                    'code' => ApiErrorDescs::SUCCESS,
                    'message' => 'success',
                    'result' => [
                        'params' => http_build_query($qddRegParams),
                        'request_url' => $objEsc->urlArr['register']
                    ],
                ];
            }else{
                throw new ApiBaseException(ApiErrorDescs::ERR_ALREADY_REGISTER_QDD);
            }
        }catch(ApiBaseException $e){
            $result = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        echo json_encode($result);
        $this->logApi(__CLASS__, __FUNCTION__, $result);
        \Yii::$app->end();
    }
}