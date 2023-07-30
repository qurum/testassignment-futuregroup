<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableNotebooks extends AbstractMigration
{
    public function change(): void
    {
        $notebooks = $this->table('notebooks');
        $notebooks
            ->addColumn('fullname', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('company', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('phone', 'string', ['limit' => 15, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('birthdate', 'datetime', ['null' => true])
            ->addColumn('photo', 'string', ['limit' => 255, 'null' => true])
            ->create();
    }
}
