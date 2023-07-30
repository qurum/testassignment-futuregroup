<?php

declare(strict_types=1);

namespace Notebook;

use DateTimeImmutable;
use DomainException;
use Exception;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use SQLite3;

class NotebookSQLite3Repository implements NotebookRepositoryInterface
{
    public function __construct(
        protected SQLite3 $db,
        protected readonly string $tableName = 'notebooks',
    ) {
    }

    public function save(Notebook $notebook): Notebook
    {
        $params = [
            ':fullname'  => $notebook->fullname,
            ':company'   => $notebook->company,
            ':phone'     => $notebook->phone,
            ':email'     => $notebook->email,
            ':birthdate' => $notebook->birthdate?->format(DATE_RFC3339),
            ':photo'     => $notebook->photo,
        ];

        if (is_null($notebook->id)) {
            $id = $this->insert($params);
            $this->setId($notebook, $id);
        } else {
            $this->update($notebook->id, $params);
        }

        return $notebook;
    }

    /**
     * @throws NotFoundException
     */
    public function get(int $id): Notebook
    {
        /** @var array<string, string|null> $result */
        $result = $this->db->querySingle('SELECT * FROM ' . $this->tableName . ' WHERE id = ' . $id, true);

        if (empty($result)) {
            throw new NotFoundException();
        }

        return $this->notebookFromArray($result);
    }

    public function count(): int
    {
        return (int)$this->db->querySingle('SELECT COUNT(*) as count FROM ' . $this->tableName);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Notebook[]
     */
    public function page(int $page, int $perPage = self::ITEMS_PER_PAGE): array
    {
        if (($perPage < 1) || ($perPage > 50) || ($page < 1)) {
            throw new DomainException();
        }

        if ($page > ceil($this->count() / $perPage)) {
            throw new NotFoundException();
        }

        $notebooks = [];

        try {
            $results = $this->db->query(
                'SELECT * FROM ' . $this->tableName
                . ' LIMIT ' . $perPage
                . ' OFFSET ' . ($page - 1) * $perPage
                . ';'
            );

            if (false === $results) {
                throw new RuntimeException();
            }

            while ($row = $results->fetchArray()) {
                $notebooks[] = $this->notebookFromArray($row);
            }
        } catch (Exception) {
            throw new RuntimeException();
        }

        if (($page !== 1) && empty($notebooks)) {
            throw new NotFoundException();
        }

        return $notebooks;
    }

    public function delete(Notebook $notebook): void
    {
        if (is_null($notebook->id)) {
            return;
        }

        $query = <<<QUERY
            DELETE FROM $this->tableName WHERE id = :id;
        QUERY;

        $statement = $this->db->prepare($query);

        if (false === $statement) {
            throw new RuntimeException('Can\'t prepare SQL statement');
        }

        $statement->bindValue(':id', $notebook->id);
        $statement->execute();
        $this->setId($notebook, null);
    }

    /**
     * @param array<string,string|null> $params
     * @return int
     * @throws Exception
     */
    protected function insert(
        array $params
    ): int {
        $query = <<<QUERY
            INSERT INTO $this->tableName(fullname, company, phone, email, birthdate, photo)
            VALUES (:fullname, :company, :phone, :email, :birthdate, :photo);
        QUERY;

        $statement = $this->db->prepare($query);

        if (false === $statement) {
            throw new RuntimeException('Can\'t prepare SQL statement');
        }

        array_walk($params, fn($value, $key) => $statement->bindValue($key, $value));

        $this->db->exec('BEGIN');
        $statement->execute();
        $id = $this->db->lastinsertrowid();
        $this->db->exec('COMMIT');

        return $id;
    }

    /**
     * @param int $id
     * @param array<string,string|null> $params
     * @return void
     * @throws Exception
     */
    protected function update(
        int $id,
        array $params
    ): void {
        $query = <<<QUERY
            UPDATE $this->tableName
            SET 
                fullname  = :fullname, 
                company   = :company, 
                phone     = :phone, 
                email     = :email, 
                birthdate = :birthdate, 
                photo     = :photo
            WHERE id = :id
        QUERY;

        $statement = $this->db->prepare($query);

        if (false === $statement) {
            throw new RuntimeException('Can\'t prepare SQL statement');
        }

        array_walk($params, fn($value, $key) => $statement->bindValue($key, $value));
        $statement->bindValue(':id', $id);
        $statement->execute();
    }

    protected function setId(Notebook $notebook, ?int $id): void
    {
        try {
            $reflection = new ReflectionProperty($notebook, 'id');
            $reflection->setValue($notebook, $id);
        } catch (ReflectionException) {
            throw new RuntimeException();
        }
    }

    /**
     * @param array<string, string|null> $data
     * @throws RuntimeException
     */
    protected function notebookFromArray(array $data): Notebook
    {
        $birthdate = isset($data["birthdate"])
            ? DateTimeImmutable::createFromFormat(DATE_RFC3339, $data["birthdate"])
            : null;

        if (false === $birthdate) {
            throw new RuntimeException('Wrong birthday value');
        }

        $notebook = new Notebook(
            fullname : $data["fullname"] ?? '',
            company  : $data["company"],
            phone    : $data["phone"] ?? '',
            email    : $data["email"] ?? '',
            birthdate: $birthdate,
            photo    : $data["photo"],
        );
        $this->setId($notebook, (int)$data['id']);

        return $notebook;
    }
}
