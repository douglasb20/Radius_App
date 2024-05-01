<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialRadiusSchema extends AbstractMigration
{
  public function up(): void
  {
    $this->execute(strstr(file_get_contents(realpath(__DIR__ . '/../../' . 'schema_radius.sql')), "#--//@UNDO", true));
  }

  public function down(): void
  {
    $this->execute(strstr(file_get_contents(realpath(__DIR__ . '/../../' . 'schema_radius.sql')), "#--//@UNDO"));
  }
}
