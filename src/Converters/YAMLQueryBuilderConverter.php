<?php

namespace Querymel\Converters;

use Symfony\Component\Yaml\Yaml;

class YAMLQueryBuilderConverter{

  private $content;

  public function parseQueryFile($filepath){
    try{
      $contentFromFile = file_get_contents($filepath);
      $this->content = Yaml::parse($contentFromFile);
      return true;
    }catch(\Exception $e)
    {
      return false;
    }
  }

  public function getQueryBuilder($filepath){
    if($this->parseQueryFile($filepath)){

      //Here I have to convert the arrays into querybuilder

      return $this->content;
    }
    return null;
  }
}
