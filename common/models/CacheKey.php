<?php
/**
 * Created by PhpStorm.
 * User: zhangxiao-pc
 * Date: 2016/2/26
 * Time: 18:35
 */

namespace common\models;


class CacheKey
{
    private static $redisKeys = [
        'lzhborrowinfo_getlist' => [       //首页推荐产品列表
            'key_name' =>  'borrowinfo_list',
            'expire' => 3*60
        ],
        'product_borrowinfo_getlist' => [  //产品页网贷产品列表
            'key_name' =>  'product_borrowinfo_list',
            'expire' => 3*60
        ],
        'xmjrcodekey' => [               //图形验证码
            'key_name' => 'xiaomaijr_verify_code',
            'expire' => 30*60
        ],
        'get_message_limit' => [               //请求短信验证码接口频率限制
            'key_name' => 'get_message_limit',
            'expire' => 1*60
        ],
        'get_message_code' => [               //短信验证码有效期
            'key_name' => 'get_message_code',
            'expire' => 3*60
        ],
        'reset_passwd' => [               //重置登录密码唯一识别码有效期
            'key_name' => 'reset_passwd_unique',
            'expire' => 3*60
        ],
    ];
    /*
     * 获取reids keyName及其生命周期
     * @param $keyName string self::$redisKeys key
     * @param $append string if the $keyName not empty, the $keyName eq $keyName . $flag . $append
     * @param $flag string
     * return array ['key_name' => string , 'expirt' => 3 *100]
     */
    public static function getCacheKey($append = '', $keyName = '', $flag = '_'){
        if(empty($keyName)){
            $trace = debug_backtrace();
            $className = $trace[1]['class']?$trace[1]['class']:'';
            $funcName = $trace[1]['function']?$trace[1]['function']:'';
            if(($pos = strrpos($className, '\\'))){
                $className = substr($className, $pos+1);
            }
            $keyName = strtolower($className) . $flag . strtolower($funcName);
        }

        if(empty(self::$redisKeys[$keyName])){
            throw new ApiBaseException(ApiErrorDescs::ERR_REDIS_KEY_NOE_EXISTS);
        }
        $cacheInfo = self::$redisKeys[$keyName];
        if($append){
            $cacheInfo['key_name'] .= $flag . $append;
        }
        if(!isset($cacheInfo['expire'])){
            $cacheInfo['expire'] = 0;
        }
        return $cacheInfo;
    }
}