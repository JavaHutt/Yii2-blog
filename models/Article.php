<?php

namespace app\models;

use Yii;
use app\models\ImageUpload;
use app\models\ArticleTag;
use app\models\Tag;
use app\models\Comment;
use app\models\Category;
use yii\helpers\ArrayHelper;

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
 * @property Tags[] $tags
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
     * Связь с тегами
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
                        ->via('articleTags');
    }
    
    /**
     * Получение выбранных тегов
     * @return array
     */
    public function getSelectedTags()
    {
        $array = $this->getTags()->select('id')->asArray()->all();
        
        return ArrayHelper::getColumn($array, 'id');
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
     * Сохранение тегов
     * @param array $tags теги
     * @return boolean
     */
    public function saveTags($tags)
    {
        if (is_array($tags)) {
            $this->clearCurrentTags();
            
            foreach ($tags as $tag) {
                $tag = Tag::findOne($tag);
                $this->link('tags', $tag);
                return true;
            }
        }
    }
    
    /**
     * Обнуление текущих тегов
     */
    private function clearCurrentTags()
    {
        ArticleTag::deleteAll(['article_id' => $this->id]);
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
    
    /**
     * Сохранение категории
     * @param int $category_id категория
     * @return boolean
     */
    public function saveCategory($category_id)
    {
        $category = Category::findOne($category_id);
        
        if ($category) {
            $this->link('category', $category);
            return true;
        }
    }
}
