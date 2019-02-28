<?php

namespace api\tests\api;

use \api\tests\ApiTester;
use common\fixtures\FileListFixture;
use yii\db\Exception;

class FileListCest
{
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
        $I->sendGET('/v1/file-lists');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }

    public function view(ApiTester $I)
    {
        $I->sendGET('/v1/file-lists/0');

        $I->sendGET('/v1/file-lists/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson();

        $I->seeHttpHeader('X-Pagination-Total-Count', 2);
    }
}
