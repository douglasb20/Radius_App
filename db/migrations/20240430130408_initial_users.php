<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialUsers extends AbstractMigration
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
    $table = $this->table('users');
    $table
      ->addColumn('name', 'string', ['limit' => 75, "null" => false])
      ->addColumn('email', 'string', ['limit' => 100, "null" => false])
      ->addColumn('username', 'string', ['limit' => 50, "null" => false])
      ->addColumn('password', 'string', ['limit' => 20, "null" => false])
      ->addColumn('group', 'string', ['limit' => 10, "null" => false])
      ->addColumn('custom_group', 'string', ['limit' => 30, "null" => true])
      ->addTimestamps()
      ->addColumn('is_request_password', 'boolean', ['limit' => 1, "null" => false, "default" => 1])
      ->addColumn('status', 'boolean', ['limit' => 1, "null" => false, "default" => 1])
      ->addIndex('email', ['unique' => true])
      ->addIndex(
        ['username', 'password'], 
        [
                  'unique' => false, 
                  'name' => 'username_password_idx',
                  'order' => [
                    'username' => 'ASC',
                    'password' => 'ASC',
                  ]
                ]
      )
      ->create();
  }
}
