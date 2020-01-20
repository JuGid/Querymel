<?php

namespace Querymel\Factories;

use Querymel\Helpers\Query;
use Querymel\Exceptions\NotAValidQuery;
use Querymel\Converters\SQLQueryBuilderConverter;

class QueryFactory{

  /**
  * Returns the query in the specified language
  */
  public function getQuery($queryBuilder, $lang)
  {
    if($lang == 'sql'){
      return (new SQLQueryBuilderConverter($queryBuilder))->getQuery();
    }

    throw new \Exception('This language is not supported : ' . $lang);
  }


}
