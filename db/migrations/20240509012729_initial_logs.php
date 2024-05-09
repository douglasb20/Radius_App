<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialLogs extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('log');
    $table
      ->addColumn('user_id', 'integer',  ['null' => false])
      ->addTimestamps(null, false)
      ->addColumn('description', 'string', ['limit' => 255, "null" => false])
      ->addIndex(
        'created_at',
        [
          'unique' => false,
          'name' => 'created_at_idx',
          'order' => ['created_at' => 'ASC']
        ]
      )
      ->create();
  }
}
