<?php namespace System\Libraries\Database\Traits;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */


 trait InsertDeleteUpdate
 {


       public function insert($insert, array $data = [], Bool $getId = false)
       {
           if (is_string($insert)) {
               $query = $insert;

               $this->bindValues = $data;
           } else {
               if (is_array($insert)) {
                   if (Arr::isAssoc($insert)) {
                       $query = "INSERT INTO {$this->table} SET " . $this->normalizeCrud($insert);

                       $this->bindValues = array_values($insert);
                   }
               }
           }

           $queryString = $this->normalizeQueryString($query);

           try {
               $statement = $this->pdo->prepare($query);

               $this->bindValues($statement);

               $statement->execute();

               $this->reset();

               return $getId ? $this->pdo->lastInsertId() : ($statement->rowCount() > 0);
           } catch (\PDOException $e) {
               throw new DatabaseException($e->getMessage(), $queryString);
           }
       }


       public function insertGetId($insert, array $data = [])
       {
           return $this->insert($insert, $data, true);
       }


       public function update($update, array $data = [])
       {
           if (is_string($update)) {
               $query = $update;

               $this->bindValues = $data;
           } else {
               if (is_array($update)) {
                   if (Arr::isAssoc($update)) {
                       $query = "UPDATE {$this->table} SET " . $this->normalizeCrud($update);
                       $query .= " " . preg_replace("/^SELECT.*FROM {$this->table}/", '', $this->getQueryString(), 1);

                       $this->bindValues = array_merge(array_values($update), $this->bindValues);
                   }
               }
           }

           $queryString = $this->normalizeQueryString($query);

           try {
               $statement = $this->pdo->prepare($query);

               $this->bindValues($statement);

               $statement->execute();

               $this->reset();

               return $statement->rowCount() > 0;
           } catch (\PDOException $e) {
               throw new DatabaseException($e->getMessage(), $queryString);
           }
       }

       public function delete($delete = null, array $data = [])
       {
           if (is_string($delete)) {
               $query = $delete;

               $this->bindValues = $data;
           } else {
               if (is_array($delete)) {
                   if (Arr::isAssoc($delete)) {
                       $this->where($delete);
                   }
               } else {
                   $query = "DELETE FROM {$this->table} " .
                       preg_replace(
                           "/SELECT.*FROM {$this->table}/",
                           '',
                           $this->getQueryString(),
                           1
                       );
               }
           }

           $queryString = $this->normalizeQueryString($query);

           try {
               $statement = $this->pdo->prepare($query);

               $this->bindValues($statement);

               $statement->execute();

               $this->reset();

               return $statement->rowCount() > 0;
           } catch (\PDOException $e) {
               throw new DatabaseException($e->getMessage(), $queryString);
           }
       }

 }
