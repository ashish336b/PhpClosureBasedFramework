<?php

namespace ashish336b\PhpCBF;

class Migration
{
   public function applyMigrations()
   {
      $this->createMigrationsTable();
      $appliedMigrations = $this->getAppliedMigrations();

      $newMigrations = [];
      $files = scandir(Application::$path . 'app/migrations');
      $toApplyMigrations = array_diff($files, $appliedMigrations);
      foreach ($toApplyMigrations as $migration) {
         if ($migration === '.' || $migration === '..') {
            continue;
         }

         $className = str_replace(".php","","App\\migrations\\$migration"); 
         $instance = new $className();
         $this->log("Applying migration $migration");
         $instance->up();
         $this->log("Applied migration $migration");
         $newMigrations[] = $migration;
      }

      if (!empty($newMigrations)) {
         $this->saveMigrations($newMigrations);
      } else {
         $this->log("There are no migrations to apply");
      }
   }

   public function createMigrationsTable()
   {
      Application::$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
         id INT AUTO_INCREMENT PRIMARY KEY,
         migration VARCHAR(255),
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     )  ENGINE=INNODB;");
   }

   protected function getAppliedMigrations()
   {
      $statement = Application::$pdo->prepare("SELECT migration FROM migrations");
      $statement->execute();

      return $statement->fetchAll(\PDO::FETCH_COLUMN);
   }

   protected function saveMigrations(array $newMigrations)
   {
      $str = implode(',', array_map(fn ($m) => "('$m')", $newMigrations));
      $statement = Application::$pdo->prepare("INSERT INTO migrations (migration) VALUES 
            $str");
      $statement->execute();
   }

   private function log($message)
   {
      echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
   }
}
