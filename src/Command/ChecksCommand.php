<?php

namespace SiteAudit\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Symfony\Component\Console\Helper\Table;
use SiteAudit\Check\Registry;


class ChecksCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this
      ->setName('checks:list')
      ->setDescription('Show all checks available.')
      ;
  }

  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $map = Registry::load();

    $checks = array();
    foreach ($map as $class => $filepath) {
      $reflect = new \ReflectionClass($class);
      if ($reflect->isAbstract()) {
        continue;
      }
      if ($reflect->isSubClassOf('SiteAudit\Check\Check')) {
        $checks[] = array(
          'class' => $class,
          'namespace' => $class::getNamespace(),
        );
      }
    }
    $table = new Table($output);
    $table
        ->setHeaders(array('Class', 'Namespace'))
        ->setRows($checks);
    $table->render();
  }

}
