<?php

namespace Querymel\Configuration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

class Configuration{

    private $config;

    private static $instance;

    private $connection;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }

    private function __construct()
    {
      $content = file_get_contents(__DIR__.'/config.yaml');
      $this->config = Yaml::parse($content);
      var_dump($this->config);
    }

    public function getAllConfigArray()
    {
      return $this->config;
    }

    public function getDatabaseConfiguration()
    {
      return $this->config['database']['configuration'];
    }

    public function getConnections()
    {
      return $this->config['database']['connections'];
    }

    public function getConnectionParams($database)
    {
      return $this->config['database']['connections'][$database];
    }

    public function getConnectionParam($database, $element)
    {
      return $this->config['database']['connections'][$database][$element];
    }

    public function isDevMode()
    {
      return $this->config['database']['configuration']['isDevMode'];
    }

    public function proxyDir()
    {
      return $this->config['database']['configuration']['proxyDir'];
    }

    public function cache()
    {
      return $this->config['database']['configuration']['cache'];
    }

    public function simpleAnnotationReader()
    {
      return $this->config['database']['configuration']['simpleAnnotationReader'];
    }

    public function getConnectionString($database)
    {
      $type = $this->getConnectionParam($database, 'type');
      $host = $this->getConnectionParam($database, 'host');

      return $type. ':host='.$host.';dbname='.$database;
    }

}
