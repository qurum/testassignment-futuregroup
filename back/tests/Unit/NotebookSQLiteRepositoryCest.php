<?php

namespace Tests\Unit;

use Notebook\Notebook;
use Notebook\NotebookSQLite3Repository;
use Notebook\NotFoundException;
use SQLite3;
use Tests\Support\UnitTester;

class NotebookSQLiteRepositoryCest
{
    public function _before(UnitTester $I) {}

    public function tryToTestReading(UnitTester $I)
    {
        /** @var SQLite3 $sqlite3 */
        $sqlite3    = $I->connectToSqlite3();
        $repository = new NotebookSQLite3Repository($sqlite3);
        $data = $I->loadDataFromDisk();

        $I->amGoingTo('test get($id)');

        foreach ($data as $item) {
            $id       = $item['id'];
            $notebook = $repository->get($id);
            $this->assertArrayNotebookEquals($I, $item, $notebook);
        }

        $I->amGoingTo('test count()');
        $I->assertEquals(count($data), $repository->count());

        $I->amGoingTo('test NotebookRepository::page');

        $notebooks = $repository->page(1,1);
        $I->assertEquals(count($notebooks), 1);
        $this->assertArrayNotebookEquals($I, $data[0], $notebooks[0]);

        $notebooks = $repository->page(2,1);
        $I->assertEquals(count($notebooks), 1);
        $this->assertArrayNotebookEquals($I, $data[1], $notebooks[0]);
    }

    /**
     * @depends tryToTestReading
     */
    public function tryToTestWriting(UnitTester $I)
    {
        /** @var SQLite3 $sqlite3 */
        $sqlite3    = $I->connectToSqlite3();
        $repository = new NotebookSQLite3Repository($sqlite3);
        $notebook = new Notebook(
            fullname : 'Some Fullname',
            company  : 'Some Company',
            phone    : '001-541-754-3011',
            email    : 'example3@example.com',
            birthdate: new \DateTimeImmutable('now'),
            photo    : null
        );

        $I->amGoingTo('test persisting');

        $repository->save($notebook);
        $I->assertNotNull($notebook->id);

        $item = $sqlite3->querySingle('SELECT * FROM notebooks WHERE id=' . $notebook->id, true);
        $I->assertEquals($notebook->fullname, $item['fullname']);
        $I->assertEquals($notebook->company, $item['company']);
        $I->assertEquals($notebook->phone, $item['phone']);
        $I->assertEquals($notebook->email, $item['email']);
        $I->assertEquals($notebook->birthdate?->format(DATE_RFC3339), $item['birthdate']);
        $I->assertEquals($notebook->photo, $item['photo']);

        $I->amGoingTo('test deleting');
        $id = $notebook->id;

        $repository->delete($notebook);
        $count = $sqlite3->querySingle('SELECT COUNT(*) AS count FROM notebooks WHERE id=' . $id,);
        $I->assertEquals(0, $count);
        $I->expectThrowable(NotFoundException::class, fn() => $repository->get($id));
        $I->assertNull($notebook->id);
    }

    protected function assertArrayNotebookEquals(UnitTester $I, array $item, Notebook $notebook)
    {
        $I->assertEquals($item['fullname'], $notebook->fullname);
        $I->assertEquals($item['company'], $notebook->company);
        $I->assertEquals($item['phone'], $notebook->phone);
        $I->assertEquals($item['email'], $notebook->email);
        $I->assertEquals($item['birthdate'], $notebook->birthdate?->format(DATE_RFC3339));
        $I->assertEquals($item['photo'], $notebook->photo);
    }
}
