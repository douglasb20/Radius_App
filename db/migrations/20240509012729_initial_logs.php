<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialLogs extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('logs');
    $table
      ->addColumn('operator_id', 'integer',  ['null' => false])
      ->addTimestamps('event_date', false)
      ->addColumn('description', 'string', ['limit' => 255, "null" => false])
      ->addIndex(
        'event_date',
        [
          'unique' => false,
          'name' => 'event_date_idx',
          'order' => ['event_date' => 'ASC']
        ]
      )
      ->create();
  }
}
