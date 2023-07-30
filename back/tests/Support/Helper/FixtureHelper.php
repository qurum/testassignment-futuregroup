<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\TestInterface;
use Dotenv\Dotenv;
use Phinx\Console\PhinxApplication;
use SQLite3;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class FixtureHelper extends \Codeception\Module
{
    public function _beforeSuite(array $settings = [])
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
    }

    public function _before(TestInterface $test)
    {
        $this->migrateDatabase();
    }

    /**
     * Run the database migrations.
     */
    public function migrateDatabase(): void
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $output = new NullOutput();

        $input = new StringInput('migrate -e testing');
        $app->run($input, $output);

        $input = new StringInput('seed:run -s NotebookRepositoryTestSeeder -e testing');
        $app->run($input, $output);
    }

    public function loadDataFromDisk(): array
    {
        return require __DIR__ . '/../../assets/notebooks.php';
    }

    public function connectToSqlite3()
    {
        $location = $_ENV['NOTEBOOKS_TEST_DB'];

        return new SQLite3($location);
    }
}
