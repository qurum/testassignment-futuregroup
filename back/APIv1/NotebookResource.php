<?php

declare(strict_types=1);

namespace APIv1;

use Notebook\Notebook;
use RuntimeException;

class NotebookResource
{
    public function __construct(readonly protected Notebook $notebook)
    {
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toArray(): array
    {
        $result['fullname']  = $this->notebook->fullname;
        $result['company']   = $this->notebook->company;
        $result['phone']     = $this->notebook->phone;
        $result['email']     = $this->notebook->email;
        $result['birthdate'] = $this->notebook->birthdate?->format(DATE_RFC3339);
        $result['photo']     = $this->notebook->photo;

        return $result;
    }

    public function toJson(): string
    {
        $result = json_encode($this->toArray());

        if (false === $result) {
            throw new RuntimeException();
        }

        return $result;
    }
}
