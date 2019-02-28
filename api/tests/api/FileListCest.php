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
            print_r($e->getMessage());
            die();
        }
    }

    public function index(ApiTester $I)
    {
        $I->sendGET('/v1/file-lists');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['title' => 'First Post'],
            ['title' => '2222 Second Post'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 3);
    }


/*
    public function indexWithAuthor(ApiTester $I)
    {
        $I->sendGET('/posts?expand=author');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'title' => 'First Post',
                'author' => [
                    'username' => 'erau',
                ],
            ]
        ]);
    }

    public function search(ApiTester $I)
    {
        $I->sendGET('/posts?s[title]=First');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['title' => 'First Post'],
        ]);
        $I->dontSeeResponseContainsJson([
            ['title' => 'Second Post'],
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

    public function view(ApiTester $I)
    {
        $I->sendGET('/posts/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'title' => 'First Post',
        ]);
    }

    public function viewNotFound(ApiTester $I)
    {
        $I->sendGET('/posts/15');
        $I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(ApiTester $I)
    {
        $I->sendPOST('/posts', [
            'title' => 'New Post',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPOST('/posts', [
            'title' => 'New Post',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'user_id' => 1,
            'title' => 'New Post',
        ]);
    }

    public function updateUnauthorized(ApiTester $I)
    {
        $I->sendPATCH('/posts/1', [
            'title' => 'New Title',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function update(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/posts/1', [
            'title' => 'New Title',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'id' => 1,
            'title' => 'New Title',
        ]);
    }

    public function updateForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendPATCH('/posts/2', [
            'title' => 'New Title',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(ApiTester $I)
    {
        $I->sendDELETE('/posts/1');
        $I->seeResponseCodeIs(401);
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/posts/1');
        $I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(ApiTester $I)
    {
        $I->amBearerAuthenticated('token-correct');
        $I->sendDELETE('/posts/2');
        $I->seeResponseCodeIs(403);
    }
*/
}
