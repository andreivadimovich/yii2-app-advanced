<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\NotSupportedException;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $uploadFile;

    public function rules()
    {
        return [
            ['uploadFile', 'file',
                'skipOnEmpty' => false,
                // file types that can be uploaded
                'extensions' => ['png', 'jpg', 'txt', 'doc', 'rar', 'zip', 'pdf', 'gif'],
                'checkExtensionByMimeType' => false,
                'maxSize'=> 1024 * 1024 * 8, // 8.3MB
                'tooBig' => 'Sorry, the file limit is 8MB',
            ]
        ];
    }

    /**
     * @return bool
     */
    public function upload($file_name = false)
    {
        if ($this->validate()) {
            if (isset($this->uploadFile->baseName) && !empty($this->uploadFile->name)) {
                if (!$file_name) {
                    $ext = explode('.', $this->uploadFile->name);
                    $ext = array_pop($ext);

                    $file_name = str_replace('.'.$ext, '', $this->uploadFile->name) . '.' .$ext;
                }

                $this->uploadFile->saveAs(\api\modules\v1\models\FileList::getAbsolutePath() . $file_name);
            } else {
                if (!$file_name) {
                    $file_name = $this->uploadFile->baseName . '.' . $this->uploadFile->extension;
                }

                $this->uploadFile->saveAs(\api\modules\v1\models\FileList::getAbsolutePath() . $file_name);
            }

            return true;
        } else {
            Yii::info((string)__METHOD__, 'notifi');
            return false;
        }
    }
}