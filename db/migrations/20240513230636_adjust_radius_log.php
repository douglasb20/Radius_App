<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdjustRadiusLog extends AbstractMigration
{
  public function up(): void
  {
    $builder = $this->getUpdateBuilder();
    $builder
      ->update('system_config')
      ->set('value', 'docker logs -n 150 radius-server')
      ->where(['`key`' => 'radius_log'])
      ->execute();
  }

  public function down(): void
  {
    $builder = $this->getUpdateBuilder();
    $builder
      ->update('system_config')
      ->set('value', 'docker logs -fn 50 radius-server')
      ->where(['`key`' => 'radius_log'])
      ->execute();
  }
}
