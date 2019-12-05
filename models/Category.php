<?php

namespace app\models;

use Yii;
use app\models\Article;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 * 
 * @property Article[] $article
 */
class Category extends \yii\db\ActiveRecord
{
    /**ccccc
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'title' => 'Заголовок',
        ];
    }
    
    /**
     * Связь со статьями
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasMany(Article::className(), ['category_id' => 'id']);
    }
    
    /**
     * Полный список категорий
     * @return array
     */
    public static function getCategoryList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'title');
    }
}
