<?php


namespace Tests\Api;

use Tests\Support\ApiTester;

/**
 * @group api
 */
class NotebookCest
{
    public function _before(ApiTester $I) {}

    // tests
    public function testNotebooksApi(ApiTester $I)
    {
        $notebookAssets = $I->loadDataFromDisk();
        $item           = $notebookAssets[0];
        unset($item['id']);

        $route = $_ENV['API_URL'] . '/api/v1/notebooks';

        $I->amGoingTo('create new notebook');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost($route, json_encode($item));
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($item);
        $I->seeHttpHeader('Location');
        $location = $I->grabHttpHeader('Location');

        $I->amGoingTo('read notebook by id');
        $I->sendGet($location);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($item);

        $I->amGoingTo('read notebooks');
        $I->sendGet($route, ['page' => 1, 'per_page' => 15]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();

        $I->amGoingTo('update notebook');
        $item2 = $notebookAssets[1];
        unset($item2['id']);
        $I->sendPut($location, json_encode($item2));
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($item2);
        $I->sendGet($location);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($item2);

        $I->amGoingTo('delete notebook');
        $item2 = $notebookAssets[1];
        unset($item2['id']);
        $I->sendDelete($location);
        $I->seeResponseCodeIsSuccessful();
    }

    public function testRequired(ApiTester $I)
    {
        $notebookAssets = $I->loadDataFromDisk();
        $item           = $notebookAssets[0];

        $route = $_ENV['API_URL'] . '/api/v1/notebooks';

        $I->amGoingTo('create notebook');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost($route, json_encode([
            'fullname' => $item['fullname'],
            'phone'    => $item['phone'],
            'email'    => $item['email'],
        ]));
        $I->seeResponseCodeIs(201);

        $I->amGoingTo('create notebook without fullname');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost($route, json_encode([
            'phone' => $item['phone'],
            'email' => $item['email'],
        ]));
        $I->seeResponseCodeIs(400);

        $I->amGoingTo('create notebook with invalid phone');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost($route, json_encode([
            'fullname' => $item['fullname'],
            'phone'    => '+70000000000',
            'email'    => $item['email'],
        ]));
        $I->seeResponseCodeIs(400);

        $I->amGoingTo('create notebook without email');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost($route, json_encode([
            'fullname' => $item['fullname'],
            'phone'    => '+70000000000',
        ]));
        $I->seeResponseCodeIs(400);
    }
}
