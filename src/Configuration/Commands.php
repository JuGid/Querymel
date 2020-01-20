<?php

namespace Querymel\Configuration;

use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Symfony\Component\Yaml\Yaml;

class Commands{

  private static $commandloader;

  public function getCommandLoader(){
    if (self::$commandloader === null)
    {
        self::$commandloader = self::load();
    }
    return self::$commandloader;
  }

  public function load()
  {
    $content = file_get_contents(__DIR__.'/commands.yaml');
    $value = Yaml::parse($content);

    $namespace = $value['commands']['namespace'] . "\\";

    $commands = array();
    foreach($value['commands']['to_load'] as $command=>$commandClass)
    {
      if(class_exists($namespace.$commandClass))
      {
        $commands[$command] = array($namespace.$commandClass,'create');
      }
    }
    return $commandLoader = new FactoryCommandLoader($commands);
  }
}
