<?php

declare(strict_types=1);

use APIv1\NotebookController;
use APIv1\NotebookControllerInterface;
use Notebook\NotebookRepositoryInterface;

return [
    'SQLiteConnection'                 =>
        DI\autowire(SQLite3::class)
            ->constructor($_ENV['NOTEBOOKS_DB']),
    NotebookRepositoryInterface::class =>
        DI\autowire(\Notebook\NotebookSQLite3Repository::class)
            ->constructor(DI\get('SQLiteConnection')),
    NotebookControllerInterface::class =>
        DI\autowire(NotebookController::class)
            ->constructorParameter('apiUrl', $_ENV['API_URL']),
];
