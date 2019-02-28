<?php

namespace api\modules\v1\models;

use Yii;
use yii\web\ServerErrorHttpException;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "file_list".
 *
 * @property int $id
 * @property string $name
 * @property string $type mime type
 * @property int $size
 */
class FileList extends \yii\db\ActiveRecord
{
    const FILE_DIRECTORY = '/file_list/';

    const EXCLUDE_FILES_EXT = array('.', '..', '.DS_Store', '.git', '.svn');
    const SELECTED_TABLE_FIELDS = ['name', 'type', 'size'];

    // create from file system in table?
    const CREATE_IF_NOT_FOUND = true;

    // no file at directory - clear data in db?
    const TRUNCATE_IF_NO_FILES = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['size'], 'integer'],
            [['name', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['id']);
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'size' => 'Size',
        ];
    }

    /**
     * @return string
     */
    public static function getAbsolutePath() {
        return Yii::getAlias('@api').'/web'.self::FILE_DIRECTORY;
    }


    /**
     * @return array|bool|int|string|ActiveRecord[]
     */
    public static function getFileList() {
        $records = self::find();

        $files_count = self::checkInFs(true);
        if ($files_count == 0) {
            if ($records->count() > 0 && self::TRUNCATE_IF_NO_FILES === true) {
                $clear_db = Yii::$app->db->createCommand()->truncateTable(self::tableName())->execute();
                if (!$clear_db) {
                    Yii::info((string)__METHOD__ . ' cant clear table ', 'notifi');
                }
            }

            return Yii::t('app', 'Directory is empty now...');
        }

        if ($records->count() !== $files_count) {
            $files_fs = self::checkInFs();
            if ($files_fs) {
                $files_db = self::checkInDb();
            }
        }

        $list = $records->select(self::SELECTED_TABLE_FIELDS)->all();
        if (isset($files_db) && !empty($files_db)) {
            $list = $files_db;
        }

        return $list ? $list : Yii::t('app', 'Directory is empty now...');
    }


    /**
     * Get list of files from the directory
     *
     * @param bool $only_count_files
     * @return array|bool|int|string
     * @throws ServerErrorHttpException
     */
    public static function checkInFs($only_count_files = false) {
        try {
            // #TODO readdir mb
            $fs_files = @scandir(self::getAbsolutePath());
            if ((bool)$fs_files === false || !is_array($fs_files)) {
                return false;
            }

            $fs_files = array_diff($fs_files, self::EXCLUDE_FILES_EXT);
            if (count($fs_files) == 0) {
                return 0;
            } elseif ($only_count_files === true) {
                return count($fs_files);
            }

            // check from file system side
            if (self::CREATE_IF_NOT_FOUND === true) {
                foreach ($fs_files as $file) {
                    if (is_dir(self::getAbsolutePath().$file)) {
                        continue;
                    }

                    $db_file = FileList::findOne(['name' => $file]);
                    if (!$db_file) {
                        $create = self::createFile($file);
                        if (!$create) {
                            throw new Exception(Yii::t('app', 'Create error. Sorry. Try later'));
                        }
                    }
                }
            }

            return $fs_files;

        } catch (Exception $e) {
            Yii::info((string)__METHOD__, 'notifi');
            throw new ServerErrorHttpException(Yii::t('app', 'Server error. Sorry. Try later'));
        }
    }


    /**
     * Get file list from table.
     *
     * @return array|int|string the file list
     */
    public static function checkInDb() {
        $db_list = self::find()->asArray()->all();
        if (count($db_list) == 0) {
            return $db_list;
        }

        $result = [];
        foreach ($db_list as $row) {
            if (empty($row['name'])) {
                continue;
            }

            // check from data base side
            $exists = file_exists(self::getAbsolutePath().$row['name']);
            if ($exists !== true) {
                $exists = self::findOne(['id' => $row['id']])->delete();
            } else {
                array_push($result, array_combine(self::SELECTED_TABLE_FIELDS,
                    array($row['name'], $row['type'], $row['size'])));
            }
        }

        return count($result) > 0 ? $result : '';
    }


    /**
     * Create the record about file.
     *
     * @param $file
     * @return bool
     */
    public static function createFile($file) {
        $filename = !empty($file['name']) ? $file['name'] : $file;
        $exists = FileList::find()->where(['name' => $filename])->one();
        if (!$exists) {
            $db_file = new FileList();
        } else {
            $db_file = $exists;
        }

        $db_file->setAttributes([
            'name' => $filename,
            'type' => !empty($file['type']) ? $file['type'] : mime_content_type(self::getAbsolutePath() . $file),
            'size' => !empty($file['size']) ? $file['size'] : filesize(self::getAbsolutePath() . $file),
        ]);

        if (!$db_file->save()) {
            $log_text = 'Create file from checkInFs method failed. '.(string)__METHOD__;
            Yii::info($log_text, 'notifi');
            return false;
        }

        return $db_file;
    }
}
