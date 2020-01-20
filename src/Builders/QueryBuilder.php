<?php

namespace Querymel\Builders;

use Querymel\Exceptions\NotAValidQuery;
use Querymel\Factories\QueryFactory;
use Querymel\Helpers\Query;

/**
* This QueryBuilder is not as complex as a good QueryBuilder can be but it provides some
* good functions to create a query with php (it don't care about declarations order)
*
* Example :
*$querybuilder = new QueryBuilder(Query::SELECT);
*$query = $querybuilder->from('utilisateur')
*                      ->columns('ville','nom','prenom')
*                      ->where('matricule = paris')
*                      ->andWhere('nom = Richard')
*                      ->leftjoin('createurs', 'utilisateur.ville = createurs.ville')
*                      ->orderBy('prenom', Query::DESC)
*                      ->getQuery('sql');
*/
class QueryBuilder{

  /**
  * Table on which the querybuilder has to request
  *
  * @var string
  */
  private $table;

  /**
  * Type of query from Query::XXXX
  *
  * @var string
  */
  private $type;

  /**
  * All parameters set by user
  *
  * @var mixed[]
  */
  private $parameters =[
    'distinct' => false,
    'columns'  => [],
    'set' => [],
    'values' => [],
    'where'   => [],
    'groupby' => [],
    'having'  => [],
    'orderby' => [],
  ];

  /**
  * Maximum results to retreive
  *
  * @var int
  */
  private $maximumResults;

  public function __construct($type)
  {
    $this->type = $type;
  }

  /**
  * Return the query type
  */
  public function getType()
  {
    return $this->type;
  }

  /**
  * Return the query parameters
  */
  public function getParams()
  {
    return $this->parameters;
  }

  /**
  * Return the query main table
  */
  public function getTable()
  {
    return $this->table;
  }


  public function get($elem)
  {
    return $this->parameters[$elem];
  }


  /**
  * DISTINCT SQL function
  */
  public function distinct($flag = true)
  {
      $this->parameters['distinct'] = (bool) $flag;
      return $this;
  }

  /**
  * FROM SQL function
  * Set the table to use for the request
  */
  public function from($table)
  {
    $this->table = $table;
    return $this;
  }

  /**
  * Set the columns to return or to select to insert/select values
  */
  public function columns($columns = null)
  {
    if($this->type != Query::SELECT && $this->type != Query::INSERT)
    {
      throw new NotAValidQuery('You can not set columns if the query is not a SELECT or INSERT.');
    }

    $trueColumns = is_array($columns) ? [$columns] : func_get_args();

    if(!$this->is_string($trueColumns))
    {
      return $this;
    }

    return $this->add('columns', $trueColumns);
  }

  /**
  * WHERE SQL function
  */
  public function where($expr, $type=''){
    if($this->type == Query::INSERT || $this->type == Query::DROP)
    {
      throw new NotAValidQuery('You can not set a where if the query is not a SELECT or INSERT.');
    }

    if(empty($type) && isset($this->parameters['where']['where']))
    {
      return $this;
    }

    return $this->add('where', ($type == '') ? $expr : $type .' '.$expr);
  }

  public function andWhere($expr)
  {
    return $this->where($expr, Query::AND_WHERE);
  }

  public function orWhere($expr)
  {
    return $this->where($expr, Query::OR_WHERE);
  }

  /**
  * SET SQL function for update
  */
  public function set($column, $value, $operator=Query::EQUAL)
  {
    if($this->type != Query::UPDATE )
    {
      throw new NotAValidQuery('You can not set if the query is not an UPDATE.');
    }

    if(empty($column) && empty($value))
    {
      return $this;
    }

    return $this->add('set',$column.' '.$operator.' '.$value);
  }

  /**
  * Set the values to set in insert query
  */
  public function values($values = null)
  {
    if($this->type != Query::INSERT)
    {
      throw new NotAValidQuery('You can not set values property if the query is not an insert.');
    }

    $trueValues = is_array($values) ? [$values] : func_get_args();

    if(!$this->is_string($trueValues))
    {
      return $this;
    }
    return $this->add('values', $trueValues);
  }

  /**
  * GROUP BY SQL function
  */
  public function groupBy($expr)
  {
    if($this->type != Query::SELECT)
    {
      throw new NotAValidQuery('You can not set a groupby if the query is not a SELECT.');
    }
    return $this->addUnique('groupby', $expr);
  }

  /**
  * HAVING SQL function
  */
  public function having($expr, $value, $operator)
  {
    if($this->type != Query::SELECT)
    {
      throw new NotAValidQuery('You can not set a having if the query is not a SELECT.');
    }
    return $this->addUnique('having', $expr, ' '.$operator.' '.$value);
  }

  /**
  * ORDER BY SQL function
  */
  public function orderBy($expr, $operator = Query::NONE)
  {
    if($this->type != Query::SELECT)
    {
      throw new NotAValidQuery('You can not set a orderby if the query is not a SELECT.');
    }
    return $this->add('orderby', $expr.' '.$operator);
  }

  public function join($table, $type, $on)
  {
    if($this->type != Query::SELECT)
    {
      throw new NotAValidQuery('You can not set a join if the query is not a SELECT.');
    }

    return $this->add('join', $type.' '.$table.' ON '.$on);
  }

  public function leftJoin($table, $on)
  {
    return $this->join($table, Query::LEFT_JOIN, $on);
  }

  public function rightJoin($table, $on)
  {
    return $this->join($table, Query::RIGHT_JOIN, $on);
  }

  public function innerJoin($table, $on)
  {
    return $this->join($table, Query::INNER_JOIN, $on);
  }

  public function naturalJoin($table, $on)
  {
    return $this->join($table, Query::NATURAL_JOIN, $on);
  }

  public function crossJoin($table, $on)
  {
    return $this->join($table, Query::CROSS_JOIN, $on);
  }

  public function fullJoin($table, $on)
  {
    return $this->join($table, Query::FULL_JOIN, $on);
  }
  /**
  * Add the option to parameters array. This options can be mutliple.
  */
  public function add($type, $value)
  {
    if(empty($value) || empty($type))
    {
      throw new NotAValidQuery('Adding values to '.$type.' turns into empty values or bad type.');
    }

    switch($type){
      case 'columns':
      case 'values':
        $this->parameters[$type] = $value;
        return $this;
      default:
        $this->parameters[$type][] = $value;
        return $this;
    }
  }

  /**
  * Add the option to parameters array. This option must be unique
  */
  public function addUnique($type, $value, $addition='')
  {


    if($this->is_in_columns($value) == false)
    {
      throw new NotAValidQuery('You must use elements selected in the columns. When adding '.$type);
    }

    $this->parameters[$type] = $value.$addition;
    return $this;
  }

  /*
  * Get the query (as string) in the specified language
  */
  public function getQuery($lang='sql')
  {
    return (new QueryFactory())->getQuery($this, $lang);
  }

  /**
  * Get the query as string to create a native SQL query.
  */
  public function getSQLQuery()
  {
    return (new QueryFactory())->getQuery($this,'sql');
  }

  /**
  * Get the array representing the query with options
  */
  public function getArrayQuery()
  {
    return $this->parameters;
  }

  /**
  * Return true if the type is set.
  */
  private function type_isset($type)
  {
    return isset($this->parameters[$type]);
  }

  public function has($type){
    return (isset($this->parameters[$type]) && !empty($this->parameters[$type]));
  }
  /**
  * Return true if the value is already in columns.
  * Used to watch if the condition can be added.
  */
  private function is_in_columns($value)
  {
    foreach($this->parameters['columns'] as $k=>$v)
    {
      if($value === $v) return true;
    }
    return false;

    //Doesn't work and I don't know why ...
    //return array_search($value, $this->parameters['columns'][$this->table]);
  }

  private function is_string($array)
  {
    foreach($array as $col){
      if(!is_string($col)) return false;
    }
    return true;
  }

}
