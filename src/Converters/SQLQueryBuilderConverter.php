<?php

namespace Querymel\Converters;

use Querymel\Exceptions\NotAValidQuery;
use Querymel\Helpers\Query;
use Querymel\Interfaces\ISpecificQueryBuilder;

class SQLQueryBuilderConverter implements ISpecificQueryBuilder{

  private $query;

  private $querybuilder;

  public function __construct($querybuilder){
    $this->querybuilder = $querybuilder;
  }

  public function getQuery()
  {
    $this->create();
    return $this->query;
  }

  private function create(){
    $type = $this->querybuilder->getType();

    $this->query = $this->getStandardString($type);

    $this->query = str_replace('table', $this->querybuilder->getTable(), $this->query);

    $this->watchForOptions();

    foreach($this->querybuilder->getParams() as $key => $value)
    {
      if(is_array($value))
      {
        if($key === 'values' && !empty($value))
        {
          if(count($this->querybuilder->get('values')) !=  count($this->querybuilder->get('columns')))
          {
            throw new NotAValidQuery('There is more columns than values.');
          }
        }

        $this->query = $this->findAndReplace($key);
      }else
      {
        $this->query = str_replace($key, $this->querybuilder->get($key), $this->query);
      }

    }
  }

  private function findAndReplace($queryVariable)
  {
    if(strpos($this->query, $queryVariable) === false)
    {
      return $this->query;
    }

    return str_replace($queryVariable, $this->getArrayListString($queryVariable), $this->query);
  }


  private function getArrayListString($elem)
  {
    $separator = ($elem == 'where') ?  ' ' : ', ';
    return implode($separator , $this->querybuilder->get($elem));
  }

  /**
  * Adapt the standard query to user needs by replacing values
  */
  private function watchForOptions()
  {
    $optionList = [
      'join'=>'',
      'groupby'=>'GROUP BY ',
      'having'=> 'HAVING ',
      'orderby'=> 'ORDER BY '
    ];

    foreach($optionList as $option=>$sqlform)
    {
      if(!$this->querybuilder->has($option))
      {
        //replace the SQL Element too
        $this->query = str_replace($option.' ', "", $this->query);
        if($option != 'join')
        {
          $this->query = str_replace($sqlform, "", $this->query);
        }
      }
    }
    return $this->query;
  }

  private function getStandardString($type = Query::SELECT)
  {
    switch($type){
      case Query::INSERT:
        return "INSERT INTO table (columns) VALUES (values);";
      case Query::SELECT:
        return "SELECT columns FROM table join WHERE where GROUP BY groupby HAVING having ORDER BY orderby;";
      case Query::UPDATE:
        return "UPDATE table SET set WHERE where;";
      case Query::DELETE:
        return "DELETE FROM table WHERE where;";
      case Query::DROP:
        return "DROP TABLE table";
    }
  }
}
