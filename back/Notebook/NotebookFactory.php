<?php

declare(strict_types=1);

namespace Notebook;

use DateTimeImmutable;
use DomainException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class NotebookFactory
{
    /**
     * @param array<string, string|null> $data
     * @return Notebook
     * @throws ValidationException
     */
    public static function fromArray(array $data): Notebook
    {
        foreach (['fullname', 'company', 'email', 'phone', 'birthdate', 'photo'] as $key) {
            if (!isset($data[$key])) {
                $data[$key] = null;
            }
        }

        v::stringType()->length(3)->check($data['fullname']);
        v::nullable(v::stringType())->check($data['company']);
        v::email()->check($data['email']);
        v::phone()->check($data['phone']);
        v::nullable(v::dateTime(DATE_RFC3339))->check($data['birthdate']);
        v::nullable(v::stringType())->check($data['photo']);

        $birthdate = isset($data['birthdate'])
            ? DateTimeImmutable::createFromFormat(DATE_RFC3339, $data['birthdate'])
            : null;

        if (false === $birthdate) {
            throw new DomainException();
        }

        return new Notebook(
            fullname : (string)$data['fullname'],
            company  : $data['company'],
            phone    : (string)$data['phone'],
            email    : (string)$data['email'],
            birthdate: $birthdate,
            photo    : $data['photo']
        );
    }
}
