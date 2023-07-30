<?php


use Phinx\Seed\AbstractSeed;

class NotebookRepositoryTestSeeder extends AbstractSeed
{
    protected const TABLE_NAME = 'notebooks';
    public function run(): void
    {
        $data = require($_ENV['NOTEBOOKS_TEST_DB_ASSETS']);
        $this
            ->table(self::TABLE_NAME)
            ->truncate();
        $this
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->saveData();
    }
}
