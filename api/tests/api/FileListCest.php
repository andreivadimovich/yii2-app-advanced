<?php

namespace api\tests\api;

use \api\tests\ApiTester;
use common\fixtures\FileListFixture;
use yii\db\Exception;
use api\modules\v1\models\FileList;

class FileListCest
{
    const EXISTS_ID = 2;
    const URL_PATH ='/v1/file-lists';

    public function _before(ApiTester $I)
    {
        try {
            $I->haveFixtures([
                'file_list' => [
                    'class' => FileListFixture::className(),
                    'dataFile' => codecept_data_dir() . 'file_list_data.php'
                ],
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function index(ApiTester $I)
    {
        $I->sendGET(self::URL_PATH);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function view(ApiTester $I)
    {
        $I->sendGET(self::URL_PATH.'/0asdasd');
        $I->sendGET(self::URL_PATH.'/'.self::EXISTS_ID);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
        $I->seeHttpHeader('X-Pagination-Total-Count', 2);
    }

    public function option(ApiTester $I)
    {
        $I->sendOPTIONS(self::URL_PATH.'/'.self::EXISTS_ID);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
    }

}
