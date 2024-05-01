<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialOperators extends AbstractMigration
{
  /**
   * Change Method.
   *
   * Write your reversible migrations using this method.
   *
   * More information on writing migrations is available here:
   * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
   *
   * Remember to call "create()" or "update()" and NOT "save()" when working
   * with the Table class.
   */
  public function change(): void
  {
    $table = $this->table('operators');
    $table
      ->addColumn('name', 'string', ['limit' => 75, "null" => false])
      ->addColumn('username', 'string', ['limit' => 50, "null" => false])
      ->addColumn('password', 'string', ['limit' => 90, "null" => false])
      ->addTimestamps()
      ->addColumn('lastlogin', 'timestamp', ["null" => true])
      ->addColumn('status', 'boolean', ['limit' => 1, "null" => false, "default" => 1])
      ->create();

      if($this->isMigratingUp()){
        $bindOperator = [
          'name' => 'admin',
          'username' => 'admin',
          'password' => password_hash("admin", PASSWORD_BCRYPT),
        ];

        $table->insert($bindOperator)->saveData();
      }
  }
}
