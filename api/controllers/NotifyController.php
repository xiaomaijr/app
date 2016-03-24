<?php
/**
 * Created by PhpStorm.
 * User: zhangxiao-pc
 * Date: 2016/3/21
 * Time: 17:45
 */

namespace api\controllers;


use common\models\Escrow;
use common\models\EscrowAccount;
use common\models\MemberInfo;
use common\models\Members;
use common\models\MembersStatus;
use common\models\NameApply;
use common\models\Notify;
use yii\web\Controller;

class NotifyController extends Controller
{
    /**
     * 注册绑定回调接口
     */
    public function actionBind(){
        if($_POST['ResultCode']=='88'){
            $loan = new Escrow();
            $verify =  $loan->registerVerify($_POST);
            if($verify){
                $str = '';
                $user = Members::find()->where("user_name='{$_POST['LoanPlatformAccount']}'")->one();
                $data = array(
                    'type' => 0,
                    'account'=>$_POST['AccountNumber'],
                    'mobile' => $_POST['Mobile'],
                    'email' => $_POST['Email'],
                    'real_name' => $_POST['RealName'],
                    'id_card'  => $_POST['IdentificationNo'],
                    'uid' => $user['id'],
                    'platform' => 0,
                    'platform_marked' => $_POST['PlatformMoneymoremore'],
                    'qdd_marked' => $_POST['MoneymoremoreId'],
                    'invest_auth' => 0,
                    'repayment' => 0,
                    'secondary_percent' => 0,
                    'add_time' => time(),
                );
                $objEscAcc = new EscrowAccount();
                if($objEscAcc->add($data)){
                    $str = "SUCCESS";
                }else{
                    echo 'ERROR';
                    return;
                }

                //更新用户信息和状态
                $userid = $user['id'];//用户id
                $member['user_phone'] = $_POST['Mobile'];
                $member['user_email'] = $_POST['Email'];
                Members::updateAll($member, ['id' => $userid]);

                //用户详情
                $member_info['idcard'] = $_POST['IdentificationNo'];
                $member_info['real_name'] = $_POST['RealName'];
                $member_info['cell_phone'] = $_POST['Mobile'];
                $member_info['up_time'] = time();
                $b = MemberInfo::getCountByCondition(['uid' => $userid]);
                if ($b == 1) {
                    MemberInfo::updateAll($member_info, ['uid' => $userid]);
                } else {
                    $member_info['uid'] = $userid;
                    $objMemInfo = new MemberInfo();
                    $objMemInfo-> add($member_info);
                }

                $data_apply['idcard'] = $_POST['IdentificationNo'];
                $data_apply['up_time'] = time();
                $data_apply['uid'] = $userid;
                $data_apply['status'] = 0;
                NameApply::add($data_apply);//实名认证更新
                MembersStatus::add(['uid' => $userid, 'id_status' => 1]);//会员认证状态更新
                $notifyData = [
                    'data_md5' => md5($_POST),
                    'notify_url' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                    'data' => json_encode($_POST),
                    'type' => '绑定账号',
                ];
                Notify::add($notifyData);
                //用户状态
//                setMemberStatus($userid, 'phone', 1, 10, '手机');
//                setMemberStatus($userid, 'email', 1, 9, '邮箱');
                echo $str;exit;
            }
        }
    }
}