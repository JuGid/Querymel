<?php

namespace Querymel\Managers;

use Querymel\Configuration\Configuration;
use Querymel\Helpers\Query;
use Querymel\Results\SQLResult;
use Querymel\Results\ResultCollection;
use PDO;

class DatabaseManager{

  private $connection;

  private $results;

  public function __contruct()
  {
    $this->connection = array();
  }

  public function connect($database)
  {
    $config = Configuration::getInstance();
    try
    {
        $this->connection = new PDO($config->getConnectionString($database), $config->getConnectionParam($database, 'user'), $config->getConnectionParam($database, 'password'));

    } catch (PDOException $e)
    {
        print "Error : " . $e->getMessage() . "<br/>";
    }
  }

  public function query($sql)
  {
    if(!empty($sql)){
      $results = $this->connection->query($sql);
      return $results;
    }

    throw new InvalidArgumentException("The sql query seems to be empty : " . $sql);
    //return the SQL result collection
  }

  public function disconnect(){
    $this->connection = null;
  }


}
