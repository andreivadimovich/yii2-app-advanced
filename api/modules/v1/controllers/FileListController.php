<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use api\modules\v1\models\FileList;
use api\modules\v1\models\UploadForm;
use yii\web\ServerErrorHttpException;

class FileListController extends ActiveController
{
    public $modelClass = '\api\modules\v1\models\FileList';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' =>
                    ['index', 'view', 'create', 'update', 'options'],
                'formats' =>
                    ['application/json' => Response::FORMAT_JSON],

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'  => ['GET', 'HEAD', 'OPTIONS'],
                    'view'   => ['GET', 'OPTIONS', 'HEAD'],
                    'create ' => ['GET', 'POST'],
                    'update' => ['PUT', 'GET'],
                ],
            ],
            [
            'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'view', 'update', 'options'],
                        'allow' => true,
                        'roles' => ['?']
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['options']);
        return $actions;
    }

    /**
     * View files list
     *
     * @return array|bool|int|string|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        return FileList::getFileList();
    }

    /**
     * Upload file
     *
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $model = new UploadForm();
        $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
        if ($model->upload()) {
            $db_save = FileList::createFile((array)$model->uploadFile);
            if (!$db_save) {
                Yii::info((string)__METHOD__." ".$model->getErrors(), 'notifi');
                throw new ServerErrorHttpException(
                    Yii::t('app', 'Error. Upload file error. Please try again later'), 404);
            }

            $last_insert_id = Yii::$app->db->getLastInsertID();

            $info = '';
            if ($last_insert_id == 0) {
                $info = 'File exist. Try other name';
                Yii::info((string)__METHOD__." ".$info, 'notifi');
            } else {
                $info = 'Success. File is uploaded. File URL is http://'.$_SERVER['SERVER_NAME'].'/v1/file-lists/'.$last_insert_id;
            }

            return Yii::t('app', $info);

        } else {
            $ers = '';
            if ($model->getErrors()) {
                $ers = $model->getErrors()['uploadFile']['0'];
            }

            Yii::info((string)__METHOD__." ".$ers, 'notifi');
            throw new ServerErrorHttpException(
                Yii::t('app', 'The file could not be created. Please try again later'), 404);
        }
    }

    /**
     * View file
     *
     * @param $id
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function actionView($id)
    {
        $file = FileList::findOne(['id' => $id]);
        if (!$file || !file_exists(FileList::getAbsolutePath() . $file['name'])) {
            Yii::info('. View file error. Id = '.$id.". ".(string)__METHOD__, 'notifi');
            throw new ServerErrorHttpException(Yii::t('app', 'Error. File not exists'), 404);
        }

        if (!empty($file['name'])) {
            $content = file_get_contents(FileList::getAbsolutePath() . $file['name']);
            if ($content) {
                return Yii::$app->response->sendContentAsFile($content, $file['name']);
            }
        }

        throw new ServerErrorHttpException(Yii::t('app', 'Error. File not exists'), 404);
    }

    /**
     * Update file or info about file in db.
     *
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionUpdate()
    {
        try {
            if (!Yii::$app->request || !Yii::$app->request->get()) {
                throw new ServerErrorHttpException('Update Error. Try again. Set the file ID');
            }

            $get = Yii::$app->request->get();
            if (isset($get['name'])) {
                $get['name'] = preg_replace("/[^a-zа-я_ 0-9]/", "", $get['name']);
            }

            $model = FileList::findOne(['id' => (int)$get['id']]);

            // upload file content to exists file
            $putdata = fopen("php://input", "r");
            $fp = fopen(FileList::getAbsolutePath() . $model->name, "w");
            while ($data = fread($putdata, 1024)) {
                fwrite($fp, $data);
            }
            fclose($fp);
            fclose($putdata);

            // rename in DB
            $old_name = $model->name;
            if (isset($get['name']) && $get['name'] !== $model->name) {
                $ext = explode('.', $old_name);
                $ext = array_pop($ext);

                $model->name = $get['name'] . ".$ext";
            }

            if ($model->save() === false && !$model->hasErrors()) {
                Yii::info((string)__METHOD__, 'notifi');
                throw new Exception('Failed to update the object for unknown reason.');
            }

            // rename in FS
            $old_file = FileList::getAbsolutePath() . $old_name;
            if (file_exists($old_file)) {
                if (!rename($old_file, FileList::getAbsolutePath() . $model->name)) {
                    Yii::info((string)__METHOD__, 'notifi');
                    throw new Exception('Failed to update the object for unknown reason.');
                }
            }

            return Yii::t('app', 'File success updated');

        } catch (\Exception $e) {
            Yii::info((string)__METHOD__, 'notifi');
            throw new ServerErrorHttpException('Update Error. Try again later please');
        }
    }

    /**
     * Get small part of the meta data file.
     *
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionOptions()
    {
        $get = Yii::$app->request->get();
        if (!isset($get['id'])) {
            throw new ServerErrorHttpException('Error. Please set ID of the file');
        }

        $file_db = FileList::findOne(['id' => (int)$get['id']]);

        if (!$file_db) {
            Yii::info((string)__METHOD__, 'notifi');
            throw new ServerErrorHttpException('Error. File is not exists');
        }

        $file_path = FileList::getAbsolutePath() . $file_db->name;

        $info = stat($file_path); #TODO_future read exif data in file
        $info['mtime'] = date('j-m-y h-i-s', $info['mtime']);
        $info['ctime'] = date('j-m-y h-i-s', $info['ctime']);

        return $info;
    }
}









