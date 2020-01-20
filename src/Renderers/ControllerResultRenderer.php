<?php

namespace Querymel\Renderers;

use Querymel\Interfaces\IRenderer;
use Querymel\Results\ResultCollection;
use Symfony\Component\Console\Output\OutputInterface;
use Querymel\Exceptions\NotAValidCollectionException;

class QueryRenderer implements IRenderer {

  public function render(OutputInterface $output, ResultCollection $results)
  {
      return "The query you asked for is : ";
  }
}
