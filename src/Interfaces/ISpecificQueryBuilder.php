<?php

namespace Querymel\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;
use Querymel\Results\SQLResult;

interface ISpecificQueryBuilder
{

  public function getQuery();

}
