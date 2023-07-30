<?php

declare(strict_types=1);

namespace APIv1;

use Notebook\Notebook;
use RuntimeException;

class NotebookListResource
{
    /**
     * @param Notebook[] $notebooks
     * @param int $currentPage
     * @param int $lastPage
     * @param int $total
     */
    public function __construct(
        protected readonly array $notebooks,
        protected readonly int $currentPage,
        protected readonly int $lastPage,
        protected readonly int $total,
    ) {
    }

    public function toJson(): string
    {
        $meta = [
            'last_page'    => $this->lastPage,
            'current_page' => $this->currentPage,
            'count'        => count($this->notebooks),
            'total'        => $this->total,
        ];

        $data = array_map(
            fn($notebook) => (new NotebookResource($notebook))->toArray(),
            $this->notebooks
        );

        $result = json_encode(['meta' => $meta, 'data' => $data]);

        if (false === $result) {
            throw new RuntimeException();
        }

        return $result;
    }
}
