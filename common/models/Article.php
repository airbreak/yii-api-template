<?php

namespace common\models;

//use http\Url;
use Yii;
use yii\web\Link;
use yii\web\Linkable;
use yii\helpers\Url;

/**
 * This is the model class for table "article".
 *
 * @property int $id ID
 * @property string $title 标题
 * @property string $content 内容
 * @property int $category_id 分类
 * @property int $status 状态
 * @property int $created_by 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 最后修改时间
 */
class Article extends \yii\db\ActiveRecord implements Linkable
{
    /**
     * 文章状态
     */

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'title', 'content'], 'required'],
            [['id', 'category_id', 'status', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 512],
            [['created_by'], 'exist', 'skipOneError'=>true,'targetClass'=>Adminuser::className(), 'targetAttribute' => ['created_by' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getCreatedBy()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'created_by']);
    }
    public function fields()
    {
        return [
            'id',
            'title',
            '内容'=>'content',
            'status' => function($model) {
                return $model->status === self::STATUS_DRAFT?'草稿':'已发布';
            },
//            'createdBy'
            'createdBy' => function($model) {
                return $model ->createdBy->realname;
            }
        ];
//        $fields = parent::fields();
//        unset($fields['created_at'], $fields['id']);
//        return $fields;
    }
    public function getLinks()
    {
        return [
          Link::REL_SELF=>Url::to(['article/view', 'id' => $this -> id] , true),
            'edit' => Url::to(['article/update','id' => $this->id], true),
            'index' => Url::to(['article'], true)
        ];
    }
}
