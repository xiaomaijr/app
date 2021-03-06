<?php

namespace common\models;

use common\models\RedisActiveRecord;
use Yii;

/**
 * This is the model class for table "lzh_ad".
 *
 * @property string $id
 * @property string $content
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $add_time
 * @property string $title
 * @property integer $ad_type
 * @property integer $platform
 */
class Ad extends RedisActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lzh_ad';
    }

    public static $tableName = "lzh_ad";

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'start_time', 'end_time', 'title'], 'required'],
            [['start_time', 'end_time', 'add_time', 'ad_type', 'platform'], 'integer'],
            [['content'], 'string', 'max' => 5000],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'add_time' => 'Add Time',
            'title' => 'Title',
            'ad_type' => 'Ad Type',
            'platform' => 'Platform',
        ];
    }

    public function insertEvent(){
        $cache = self::getCache();
        $cache->hDel(self::$tableName, 'id:' . $this->id);
    }

    public function updateEvent(){
        $cache = self::getCache();
        $cache->hDel(self::$tableName, 'id:' . $this->id);
    }

    public function deleteEvent(){
        $cache = self::getCache();
        $cache->hDel(self::$tableName, 'id:' . $this->id);
    }

    public static function getAppBanners($id){
        $info = self::get($id);
        $banners = json_decode($info['content'],true);
        foreach($banners as &$banner){
            $banner['img'] = \Yii::$app->request->getHostInfo() . $banner['url'];
        }
        return $banners;
    }
}
