<?php

namespace Querymel\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;
use Querymel\Results\ResultCollection;

interface IRenderer
{
  /**
   * Renders the results.
   * @param OutputInterface  $output  Output Interface.
   * @param ResultCollection $results Result Collection.
   * @return void
   */
  public function render(OutputInterface $output, ResultCollection $results);

}
