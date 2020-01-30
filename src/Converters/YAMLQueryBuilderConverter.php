<?php

namespace Querymel\Converters;

use Symfony\Component\Yaml\Yaml;
use Querymel\Builders\QueryBuilder;
use Querymel\Helpers\Query;

/**
* This need a big refactoring for query creation
*/
class YAMLQueryBuilderConverter{

  private $filepath;
  private $content;
  private $queryBuilders;

  public function __construct()
  {
    $queryBuilders = array();
  }

  public function parseQueryFile($filepath)
  {
    try
    {
      $contentFromFile = file_get_contents($filepath);
      $this->content = Yaml::parse($contentFromFile);
      return true;
    }
    catch(\Exception $e)
    {
      return false;
    }
  }

  public function setFileQuery($filepath)
  {
    $this->filepath = $filepath;
    return $this->filepath;
  }

  public function convert()
  {
    if($this->parseQueryFile($this->filepath))
    {
      $keys = array_keys($this->content);
      foreach($keys as $key)
      {
        $yamlQuery = $this->content[$key];
        $this->build($key, $yamlQuery);
      }
      $this->print();
      return $this->queryBuilders;
    }
    return null;
  }

  private function build($key, $yamlQuery)
  {
    $qbType = Query::getConstantValue($key);
    //drop must generate multiple Drop elements
    //insert must generate multiple insert elements
    switch($qbType)
    {
      case Query::DROP:
        $this->createDropRequest($yamlQuery);
        break;
      case Query::INSERT:
        $this->createInsertRequest($yamlQuery);
        break;
      case Query::SELECT:
        $this->createSelectRequest($yamlQuery);
        break;
      case Query::UPDATE:
        $this->createUpdateRequest($yamlQuery);
        break;
      case Query::DELETE:
        $this->createDeleteRequest($yamlQuery);
        break;
    }
  }

  private function createDropRequest($yamlQuery)
  {
    foreach($yamlQuery['table'] as $table)
    {
      $qb = new QueryBuilder(Query::DROP);
      $qb->from($table);
      $this->queryBuilders[] = $qb;
    }
  }

  private function createInsertRequest($yamlQuery)
  {
    $table = $yamlQuery['table'];
    $columns = $yamlQuery['columns'];
    foreach($yamlQuery['dataset'] as $values)
    {
      $qb = new QueryBuilder(Query::INSERT);
      $qb->from($table)
         ->columns($columns)
         ->values($values);
      $this->queryBuilders[] = $qb;
    }
  }

  private function createDeleteRequest($yamlQuery)
  {
    $table = $yamlQuery['table'];
    $qb = new QueryBuilder(Query::DELETE);
    $qb->from($table);
    $this->addConditions($qb, $yamlQuery['conditions']);
    $this->queryBuilders[] = $qb;
  }

  private function createUpdateRequest($yamlQuery)
  {
    $table = $yamlQuery['table'];
    $qb = new QueryBuilder(Query::UPDATE);
    $qb->from($table);
    $this->addConditions($qb, $yamlQuery['conditions']);
    $set_array = $yamlQuery['set'];
    $qb->set($set_array['column'], $set_array['value']);

    $this->queryBuilders[] = $qb;
  }

  private function createSelectRequest($yamlQuery)
  {
    $qb = new QueryBuilder(Query::SELECT);
    $qb->from($yamlQuery['table']);
    $qb->columns($yamlQuery['columns']);
    $this->addConditions($qb, $yamlQuery['conditions']);

    $this->queryBuilders[] = $qb;
  }

  private function addConditions($qb, $conditions)
  {
    foreach($conditions as $condition)
    {
      $in_condition = array_key_first($condition);
      $column = $condition[$in_condition]['column'];

      $qb->$in_condition($this->yamlToExpression($condition[$in_condition]));
    }
  }

  private function yamlToExpression($array)
  {
    $keys = array_keys($array);

    $column = $keys[0];
    $value = $keys[1];
    $expr = '';
    switch($value)
    {
      case "not":
        $expr = Query::N_EQUAL;
        break;
      case "is":
        $expr = Query::EQUAL;
        break;
      case "greater":
        $expr = Query::G_THAN;
        break;
      case "lower":
        $expr = Query::L_THAN;
        break;
      case "greater_equal":
        $expr = Query::GE_THAN;
        break;
      case "lower_equal":
        $expr = Query::LE_THAN;
        break;
    }
    return $array[$column].' '.$expr.' '.$array[$value];
  }

  private function print()
  {
    foreach($this->queryBuilders as $qb)
    {
      echo $qb->getSQLQuery() . "\n\r";
    }
  }

}
