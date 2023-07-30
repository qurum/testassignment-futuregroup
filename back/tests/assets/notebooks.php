<?php

declare(strict_types=1);

return [
    [
        'id'        => 1,
        'fullname'  => 'John Doe',
        'company'   => 'Test Company',
        'phone'     => '+49 69 1234 5678',
        'email'     => 'example@example.com',
        'birthdate' => (new DateTimeImmutable('1970-01-01 10:00:00'))->format(DATE_RFC3339),
        'photo'     => 'uploads/001.jpg',
    ],
    [
        'id'        => 2,
        'fullname'  => 'Jane Roe',
        'company'   => null,
        'phone'     => '+74950010203',
        'email'     => 'example2@example.com',
        'birthdate' => null,
        'photo'     => null,
    ],
];
