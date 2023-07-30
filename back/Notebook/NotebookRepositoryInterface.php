<?php

declare(strict_types=1);

namespace Notebook;

use Exception;
use ReflectionException;

interface NotebookRepositoryInterface
{
    public const ITEMS_PER_PAGE = 15;

    public function save(Notebook $notebook): Notebook;
    public function delete(Notebook $notebook): void;

    /**
     * @throws NotFoundException
     */
    public function get(int $id): Notebook;

    public function count(): int;

    /**
     * @param int $page
     * @param int $perPage
     * @return Notebook[]
     */
    public function page(int $page, int $perPage = self::ITEMS_PER_PAGE): array;
}
