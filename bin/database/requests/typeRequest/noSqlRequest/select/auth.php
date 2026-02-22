<?php

namespace Epaphrodites\database\requests\typeRequest\noSqlRequest\select;

use Epaphrodites\database\query\Builders;

class auth extends Builders
{

  /**
   * Verify if usersaccount table exist in database (For mongodb)
   * @return bool
   */
  private function ifCollectionExist():bool
  {

    $collections = $this->rdb(1)->listCollections();

    $result = false;

    foreach ($collections as $collectionInfo) {
      if ($collectionInfo->getName() === "usersaccount") {
        $result = true;
        break;
      }
    }

    return $result;
  }

  /**
   * Verify if usersaccount table exist in database (For mongodb)
   * @return bool
   */
  private function ifKeyExist():bool
  {

    $result = $this->key('usersaccount')->index('*')->checkIsExist();

    return $result;
  }  

  /**
   * Request to select all users of database (For mongo db)
   * 
   * @param string $login
   * @return array|bool
   */
  public function findNosqlUsers(
    string $login
  ):array|bool
  {

    if ($this->ifCollectionExist() === true) {

      $documents = [];

      $result = $this->db(1)
        ->selectCollection('usersaccount')
        ->find(['login' => $login]);

      foreach ($result as $document) {
        $documents[] = $document;
      }

      return $documents;
    } else {

      static::firstSeederGeneration();

      return false;
    }
  }

 /**
   * Request to select all users of database (For Redis db)
   * 
   * @param string $login
   * @return array|bool
   */
  public function findNosqlRedisUsers(
    string $login
  ):array|bool
  {

    if ($this->ifKeyExist() === true) {

      return $this->key('usersaccount')->search(['state'])->param([1])->index($login)->redisGet();

    } else {

      static::firstSeederGeneration();

      return false;
    }
  }  
}