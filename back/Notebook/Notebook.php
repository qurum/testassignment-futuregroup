<?php

declare(strict_types=1);

namespace Notebook;

use DateTimeImmutable;

/**
 * @property-read int|null $id
 */
class Notebook
{
    protected ?int $id = null;

    public function __construct(
        public string $fullname,
        public ?string $company,
        public string $phone,
        public string $email,
        public ?DateTimeImmutable $birthdate,
        public ?string $photo,
    ) {
    }

    public function __get(string $property): ?int
    {
        return match ($property) {
            'id'    => $this->id,
            default => null
        };
    }

    public function fillFrom(Notebook $notebook): Notebook
    {
        $this->fullname  = $notebook->fullname;
        $this->email     = $notebook->email;
        $this->photo     = $notebook->photo;
        $this->company   = $notebook->company;
        $this->birthdate = $notebook->birthdate;
        $this->phone     = $notebook->phone;

        return $this;
    }
}
