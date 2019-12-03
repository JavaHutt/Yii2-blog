<?php
namespace app\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImageUpload extends Model
{
    public $image;

    public function rules() 
    {
        return [
            [['image'], 'required'],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];        
    }
    
    /**
     * Загрузить файл
     * @param UploadedFile $file файл
     * @param type $currentImage
     * @return type
     */
    public function uploadFile(UploadedFile $file, $currentImage)
    {
        $this->image = $file;

        if ($this->validate()) {
            $this->deleteCurrentImage($currentImage);

            return $this->saveImage();
        }
    }
    
    /**
     * Получить путь до папки
     * @return string
     */
    private function getFolder()
    {
        return Yii::getAlias('@web') . 'uploads/';
    }
    
    /**
     * Сгенерировать имя файла
     * @return string
     */
    private function generateFilename()
    {
        return strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension);
    }

    /**
     * Удалить текущее изображение
     * @param string $currentImage текущее изображение
     */
    public function deleteCurrentImage($currentImage)
    {
        if ($this->fileExists($currentImage)) {
            unlink($this->getFolder() . $currentImage);
        }
    }
    
    /**
     * Проверка на существование файла
     * @param string $currentImage текущее изображение
     * @return boolean
     */
    public function fileExists($currentImage)
    {
        if (!empty($currentImage) && $currentImage != null) 
        {
            return file_exists($this->getFolder() . $currentImage);
        }
    }
    
    /**
     * Сохранить изображение
     * @return string
     */
    public function saveImage()
    {
        $filename = $this->generateFilename();
        $this->image->saveAs($this->getFolder() . $filename);

        return $filename;
    }
}