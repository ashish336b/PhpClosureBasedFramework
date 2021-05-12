<?php

namespace ashish336b\PhpCBF;

class Model
{
   protected $table;
   private $_db;

   public function getColumns()
   {
      return DB::table($this->table)->getColumns();
   }

   public function fetch()
   {
      return DB::table($this->table)->get();
   }

   public function query($sql, $params = [], $class = false)
   {
      return DB::raw()->query($sql, $params, $class);
   }
}
