<?php
namespace Querymel\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Querymel\Managers\DatabaseManager;
use Symfony\Component\Yaml\Yaml;
use Querymel\Converters\YAMLQueryBuilderConverter;

class QueryCommand extends Command
{

    public function configure()
    {
        $this -> setName('query')
              -> setDescription('From a YAML file, execute SQL Queries.')
              -> setHelp('To have more information go on GitHub docs.')
              -> addArgument('filepath', InputArgument::REQUIRED, 'The YAML file path.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
      $databaseManager = new DatabaseManager();

      //No database to test
      //$databaseManager->connect('myDatabase');

      $converter = new YAMLQueryBuilderConverter();
      $querybuilder = $converter->getQueryBuilder($input->getArgument('filepath'));

      //For testing
      //var_dump($querybuilder);

      //$sqlquery = $querybuilder->getSQLQuery()
      //$result = $databaseManager->query($sqlquery);

      $databaseManager->disconnect();

      return 0;
    }

    public static function create()
    {
      return new QueryCommand();
    }
}
