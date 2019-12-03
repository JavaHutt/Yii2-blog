<?php

namespace app\models;

use Yii;
use app\models\ImageUpload;
use app\models\ArticleTag;
use app\models\Comment;
use app\models\Category;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $title Заголовок
 * @property string $description Описание
 * @property string $content Контент
 * @property string $data Дата
 * @property string $image Изображение
 * @property int $viewed Просмотры
 * @property int $user_id
 * @property int $status Статус
 * @property int $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 * @property Category $category
 */
class Article extends \yii\db\ActiveRecord
{
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
            [['title'], 'required'],
            [['title', 'description', 'content'], 'string'],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['data'], 'default', 'value' => date('Y-m-d')],
            [['title'], 'string', 'max' => 255]
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'title'       => 'Заголовок',
            'description' => 'Описание',
            'content'     => 'Контент',
            'data'        => 'Дата',
            'image'       => 'Изображение',
            'viewed'      => 'Просмотры',
            'user_id'     => 'User ID',
            'status'      => 'Статус',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Связь с тегами
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::className(), ['article_id' => 'id']);
    }

    /**
     * Связь с комментариями
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']);
    }
    
    /**
     * Связь с категорией
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Получение изображения
     * @return string
     */
    public function getImage()
    {
        return ($this->image) ? '@web/uploads/' . $this->image : '@web/no-image.png';
    }
    
    /**
     * Сохранение изображения
     * @param string $filename имя файла
     * @return void
     */
    public function saveImage($filename)
    {
        $this->image = $filename;
        return $this->save(false);
    }
    
    /**
     * Удаление изображения
     * @return void
     */
    public function deleteImage()
    {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }
    
    /**
     * Обработка перед удалением
     * @return void
     */
    public function beforeDelete()
    {
        $this->deleteImage();
    }
    
    public function saveCategory($category)
    {
        $category = Category::findOne($category);
        
        if ($category) {
            $this->link('category', $category);
            return true;
        }
    }
}
