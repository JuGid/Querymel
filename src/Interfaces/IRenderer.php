<?php

namespace Gindicate\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;
use Gindicate\Results\ResultCollection;

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
