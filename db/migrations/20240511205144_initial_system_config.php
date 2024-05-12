<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSystemConfig extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('system_config');
    $table
      ->addColumn("key", 'string', ['limit' => 50, 'null' => false])
      ->addColumn("value", 'text', ['null' => true, 'default' => null])
      ->create();

    if ($this->isMigratingUp()) {
      $bindSystem = [
        [
          'key' => 'radius_restart',
          'value' => 'docker restart radius-server',
        ],
        [
          'key' => 'radius_log',
          'value' => 'docker logs -f -n 50 radius-server',
        ],
      ];

      $table->insert($bindSystem)->saveData();
    }
  }
}
