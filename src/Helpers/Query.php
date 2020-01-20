<?php

namespace Querymel\Helpers;

abstract class Query{

  public const NONE = '';

  public const INSERT = "INSERT";
  public const SELECT = "SELECT";
  public const UPDATE = "UPDATE";
  public const DELETE = "DELETE";
  public const DROP = "DROP";

  public const LEFT_JOIN = 'LEFT JOIN';
  public const RIGHT_JOIN = 'RIGHT JOIN';
  public const INNER_JOIN = 'INNER JOIN';
  public const NATURAL_JOIN = 'NATURAL JOIN';
  public const CROSS_JOIN = 'CROSS JOIN';
  public const FULL_JOIN = 'FULL JOIN';

  public const OR_WHERE = 'OR';
  public const AND_WHERE = 'AND';

  public const EQUAL = '=';
  public const N_EQUAL = '<>';
  public const G_THAN = '>';
  public const L_THAN = '<';
  public const GE_THAN = '>=';
  public const LE_THAN = '<=';

  public const DESC = 'DESC';
  public const ASC = 'ASC';

  public static function SUM($value)
  {
    return 'SUM('.$value.')';
  }

  public static function COUNT($value)
  {
    return 'COUNT('.$value.')';
  }


}
